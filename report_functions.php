<?php
// Turn off all error reporting for downloads
if (isset($_POST['format']) && ($_POST['format'] === 'pdf' || $_POST['format'] === 'excel')) {
    ini_set('display_errors', '0');
    error_reporting(0);
}

require_once 'dbconnection.php';
require_once 'fpdf.php';

// Function to get inventory report data
function getInventoryReport() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM inventory ORDER BY category, product_name");
    $stmt->execute();
    return $stmt->get_result();
}

// Function to get sales report data
function getSalesReport($startDate, $endDate) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT s.*, sp.product_name, sp.quantity, sp.unit_price, sp.total 
        FROM sales s 
        LEFT JOIN sales_products sp ON s.id = sp.sale_id 
        WHERE s.sale_date BETWEEN ? AND ?
        ORDER BY s.sale_date DESC"
    );
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to get order report data
function getOrderReport($startDate, $endDate) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT o.*, oi.product_name, oi.quantity, oi.unit_price, oi.amount 
        FROM orders o 
        LEFT JOIN order_items oi ON o.order_id = oi.order_id 
        WHERE o.order_date BETWEEN ? AND ? 
        ORDER BY o.order_date DESC"
    );
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle AJAX/Download requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get parameters from either POST or JSON input
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $type = $input['type'] ?? '';
    $format = $input['format'] ?? '';
    $startDate = $input['start_date'] ?? $input['startDate'] ?? null;
    $endDate = $input['end_date'] ?? $input['endDate'] ?? null;
    
    // If format is specified, it's a download request
    $isDownload = isset($input['format']) && ($input['format'] === 'pdf' || $input['format'] === 'excel');

    try {
        $data = null;
        switch ($type) {
            case 'inventory':
                $data = getInventoryReport();
                $filename = 'inventory_report';
                break;
            case 'sales':
                if (!$startDate || !$endDate) throw new Exception('Date range required for sales report');
                $data = getSalesReport($startDate, $endDate);
                $filename = 'sales_report_' . $startDate . '_' . $endDate;
                break;
            case 'orders':
                if (!$startDate || !$endDate) throw new Exception('Date range required for orders report');
                $data = getOrderReport($startDate, $endDate);
                $filename = 'orders_report_' . $startDate . '_' . $endDate;
                break;            default:
                throw new Exception('Invalid report type. Must be inventory, sales, or orders');
        }

        if (!$data) {
            throw new Exception('No data available for the selected criteria');
        }

        if ($format === 'pdf') {
            generatePDFReport($type, $data, $filename);
        } else if ($format === 'excel') {
            generateExcelReport($type, $data, $filename);
        } else {
            // Return JSON data for preview
            $rows = [];
            while ($row = $data->fetch_assoc()) {
                $rows[] = $row;
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $rows]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

function generatePDFReport($type, $result, $filename) {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    // Title
    $title = ucfirst($type) . ' Report';
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10);
    
    $pdf->SetFont('Arial', 'B', 12);
    
    // Headers based on report type
    switch ($type) {
        case 'inventory':
            $pdf->Cell(20, 7, 'ID', 1);
            $pdf->Cell(80, 7, 'Product Name', 1);
            $pdf->Cell(40, 7, 'Category', 1);
            $pdf->Cell(30, 7, 'Quantity', 1);
            $pdf->Cell(40, 7, 'Price (Rs.)', 1);
            break;
        case 'sales':
            $pdf->Cell(30, 7, 'Date', 1);
            $pdf->Cell(60, 7, 'Customer', 1);
            $pdf->Cell(60, 7, 'Product', 1);
            $pdf->Cell(30, 7, 'Quantity', 1);
            $pdf->Cell(30, 7, 'Amount (Rs.)', 1);
            break;
        case 'orders':
            $pdf->Cell(40, 7, 'Order ID', 1);
            $pdf->Cell(60, 7, 'Customer', 1);
            $pdf->Cell(30, 7, 'Date', 1);
            $pdf->Cell(30, 7, 'Status', 1);
            $pdf->Cell(40, 7, 'Amount (Rs.)', 1);
            break;
    }
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 11);
    
    // Data rows
    while ($row = $result->fetch_assoc()) {
        switch ($type) {
            case 'inventory':
                $pdf->Cell(20, 7, $row['id'], 1);
                $pdf->Cell(80, 7, $row['product_name'], 1);
                $pdf->Cell(40, 7, $row['category'], 1);
                $pdf->Cell(30, 7, $row['quantity'], 1);
                $pdf->Cell(40, 7, number_format($row['price'], 2), 1);
                break;            case 'sales':
                $pdf->Cell(30, 7, $row['sale_date'] ?? '', 1);
                $pdf->Cell(60, 7, $row['customer_name'] ?? '', 1);
                $pdf->Cell(60, 7, $row['product_name'] ?? '', 1);
                $pdf->Cell(30, 7, $row['quantity'] ?? '0', 1);
                $pdf->Cell(30, 7, number_format(floatval($row['total'] ?? 0), 2), 1);
                break;
            case 'orders':
                $pdf->Cell(40, 7, $row['order_id'], 1);
                $pdf->Cell(60, 7, $row['customer_name'], 1);
                $pdf->Cell(30, 7, $row['order_date'], 1);
                $pdf->Cell(30, 7, $row['status'], 1);
                $pdf->Cell(40, 7, number_format($row['total_amount'], 2), 1);
                break;
        }
        $pdf->Ln();
    }
    
    // Output PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    $pdf->Output('D', $filename . '.pdf');
    exit;
}

function generateExcelReport($type, $result, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Headers based on report type
    switch ($type) {
        case 'inventory':
            fputcsv($output, ['ID', 'Product Name', 'Category', 'Quantity', 'Price (Rs.)']);
            break;
        case 'sales':
            fputcsv($output, ['Date', 'Customer', 'Product', 'Quantity', 'Amount (Rs.)']);
            break;
        case 'orders':
            fputcsv($output, ['Order ID', 'Customer', 'Date', 'Status', 'Amount (Rs.)']);
            break;
    }
      // Data rows
    while ($row = $result->fetch_assoc()) {
        switch ($type) {
            case 'inventory':
                fputcsv($output, [
                    $row['id'],
                    $row['product_name'],
                    $row['category'],
                    $row['quantity'],
                    number_format($row['price'], 2)
                ]);
                break;            case 'sales':
                fputcsv($output, [
                    $row['sale_date'] ?? '',
                    $row['customer_name'] ?? '',
                    $row['product_name'] ?? '',
                    $row['quantity'] ?? '0',
                    number_format(floatval($row['total'] ?? 0), 2)
                ]);
                break;
            case 'orders':
                fputcsv($output, [
                    $row['order_id'],
                    $row['customer_name'],
                    $row['order_date'],
                    $row['status'],
                    number_format($row['total_amount'], 2)
                ]);
                break;
        }
    }
    fclose($output);
    exit;
}

// Function to generate PDF report
// function generatePDFReport($type, $startDate = null, $endDate = null) {
//     $pdf = new FPDF();
//     $pdf->AddPage();
    
//     // Header
//     $pdf->SetFont('Arial', 'B', 16);
//     $pdf->Cell(190, 10, strtoupper($type . ' Report'), 0, 1, 'C');
//     $pdf->SetFont('Arial', '', 10);
    
//     if ($startDate && $endDate) {
//         $pdf->Cell(190, 10, "Period: $startDate to $endDate", 0, 1, 'C');
//     }
    
//     $pdf->Ln(10);
    
//     switch ($type) {
//         case 'inventory':
//             generateInventoryPDF($pdf);
//             break;
//         case 'sales':
//             generateSalesPDF($pdf, $startDate, $endDate);
//             break;
//         case 'orders':
//             generateOrdersPDF($pdf, $startDate, $endDate);
//             break;
//     }
    
//     return $pdf->Output('S'); // Return as string
// }

// Function to generate Excel report
// function generateExcelReport($type, $startDate = null, $endDate = null) {
//     header('Content-Type: text/csv');
//     header('Content-Disposition: attachment; filename="' . $type . '_report.csv"');
    
//     $output = fopen('php://output', 'w');
    
//     switch ($type) {
//         case 'inventory':
//             generateInventoryExcel($output);
//             break;
//         case 'sales':
//             generateSalesExcel($output, $startDate, $endDate);
//             break;
//         case 'orders':
//             generateOrdersExcel($output, $startDate, $endDate);
//             break;
//     }
    
//     fclose($output);
// }

// Helper function for Inventory PDF
function generateInventoryPDF($pdf) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 7, 'ID', 1, 0);
    $pdf->Cell(70, 7, 'Product Name', 1, 0);
    $pdf->Cell(30, 7, 'Category', 1, 0);
    $pdf->Cell(30, 7, 'Quantity', 1, 0);
    $pdf->Cell(40, 7, 'Price', 1, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $result = getInventoryReport();
    
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 7, $row['id'], 1, 0);
        $pdf->Cell(70, 7, $row['product_name'], 1, 0);
        $pdf->Cell(30, 7, $row['category'], 1, 0);
        $pdf->Cell(30, 7, $row['quantity'], 1, 0);
        $pdf->Cell(40, 7, 'Rs. ' . number_format($row['price'], 2), 1, 1);
    }
}

// Helper function for Sales PDF
function generateSalesPDF($pdf, $startDate, $endDate) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 7, 'Date', 1, 0);
    $pdf->Cell(40, 7, 'Invoice ID', 1, 0);
    $pdf->Cell(60, 7, 'Customer', 1, 0);
    $pdf->Cell(60, 7, 'Amount', 1, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $result = getSalesReport($startDate, $endDate);
    
    $totalSales = 0;
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 7, $row['sale_date'], 1, 0);
        $pdf->Cell(40, 7, $row['invoice_id'], 1, 0);
        $pdf->Cell(60, 7, $row['customer_name'], 1, 0);
        $pdf->Cell(60, 7, 'Rs. ' . number_format($row['total_amount'], 2), 1, 1);
        $totalSales += $row['total_amount'];
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(130, 7, 'Total Sales:', 1, 0, 'R');
    $pdf->Cell(60, 7, 'Rs. ' . number_format($totalSales, 2), 1, 1);
}

// Helper function for Orders PDF
function generateOrdersPDF($pdf, $startDate, $endDate) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 7, 'Date', 1, 0);
    $pdf->Cell(40, 7, 'Order ID', 1, 0);
    $pdf->Cell(60, 7, 'Customer', 1, 0);
    $pdf->Cell(30, 7, 'Status', 1, 0);
    $pdf->Cell(30, 7, 'Amount', 1, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $result = getOrderReport($startDate, $endDate);
    
    $totalOrders = 0;
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 7, $row['order_date'], 1, 0);
        $pdf->Cell(40, 7, $row['order_id'], 1, 0);
        $pdf->Cell(60, 7, $row['customer_name'], 1, 0);
        $pdf->Cell(30, 7, ucfirst($row['status']), 1, 0);
        $pdf->Cell(30, 7, 'Rs. ' . number_format($row['total_amount'], 2), 1, 1);
        $totalOrders += $row['total_amount'];
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(160, 7, 'Total Orders:', 1, 0, 'R');
    $pdf->Cell(30, 7, 'Rs. ' . number_format($totalOrders, 2), 1, 1);
}

// Helper function for Inventory Excel
function generateInventoryExcel($output) {
    fputcsv($output, ['ID', 'Product Name', 'Category', 'Quantity', 'Price']);
    
    $result = getInventoryReport();
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['product_name'],
            $row['category'],
            $row['quantity'],
            $row['price']
        ]);
    }
}

// Helper function for Sales Excel
function generateSalesExcel($output, $startDate, $endDate) {
    fputcsv($output, ['Date', 'Invoice ID', 'Customer', 'Product', 'Quantity', 'Unit Price', 'Total']);
    
    $result = getSalesReport($startDate, $endDate);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['sale_date'],
            $row['invoice_id'],
            $row['customer_name'],
            $row['product_name'],
            $row['quantity'],
            $row['unit_price'],
            $row['total']
        ]);
    }
}

// Helper function for Orders Excel
function generateOrdersExcel($output, $startDate, $endDate) {
    fputcsv($output, ['Date', 'Order ID', 'Customer', 'Status', 'Product', 'Quantity', 'Unit Price', 'Amount']);
    
    $result = getOrderReport($startDate, $endDate);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['order_date'],
            $row['order_id'],
            $row['customer_name'],
            $row['status'],
            $row['product_name'],
            $row['quantity'],
            $row['unit_price'],
            $row['amount']
        ]);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'generate_report':
                try {
                    $type = $data['type'];
                    $format = $data['format'];
                    $startDate = $data['start_date'] ?? null;
                    $endDate = $data['end_date'] ?? null;
                    
                    if ($format === 'pdf') {
                        $pdfContent = generatePDFReport($type, $startDate, $endDate);
                        echo json_encode([
                            'success' => true,
                            'content' => base64_encode($pdfContent)
                        ]);
                    } else {
                        generateExcelReport($type, $startDate, $endDate);
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }
}
?>
