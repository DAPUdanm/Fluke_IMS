<?php

// Prevent any accidental output before headers
ob_start();
require_once 'dbconnection.php';
require_once 'fpdf.php';

try {    if (!isset($_GET['id'])) {
        throw new Exception('Invoice ID is required');
    }    // Get invoice information
    $stmt = $conn->prepare("SELECT i.*, o.customer_name, o.contact_number, o.customer_address 
                           FROM invoices i 
                           LEFT JOIN orders o ON i.order_id = o.order_id 
                           WHERE i.invoice_id = ?");
    $stmt->bind_param("s", $_GET['id']);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();

    if (!$invoice) {
        throw new Exception('Invoice not found');
    }
    
    // Check if invoice is canceled
    if ($invoice['invoice_status'] === 'canceled') {
        throw new Exception('Cannot download canceled invoices');
    }

    // Get items from product_details JSON field

    $items = json_decode($invoice['product_details'], true);
    if (!is_array($items)) {
        $items = [];
    }

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'INVOICE', 0, 1, 'C');
    $pdf->Ln(10);

    // Company Info
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'FLUKE IMS', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 5, 'Address:  42/1B, Jayanthi Place, Gangarama Road, Boralesgamuwa.', 0, 1, 'L');
    $pdf->Cell(190, 5, 'Phone: 071-1032-454, 071-5676-999', 0, 1, 'L');
    $pdf->Cell(190, 5, 'Email: info@flukeims.lk', 0, 1, 'L');
    $pdf->Ln(10);

    // Invoice Info
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 5, 'Order ID:', 0, 0);
    $pdf->SetFont('Arial', '', 10);    $pdf->Cell(160, 5, $invoice['order_id'], 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 5, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(160, 5, $invoice['invoice_date'], 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 5, 'Status:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(160, 5, ucfirst($invoice['invoice_status']), 0, 1);
    $pdf->Ln(10);

    // Customer Info
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(190, 5, 'Customer Information:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 5, 'Name: ' . $invoice['customer_name'], 0, 1);
    $pdf->Cell(190, 5, 'Contact: ' . $invoice['contact_number'], 0, 1);
    $pdf->Cell(190, 5, 'Address: ' . $invoice['customer_address'], 0, 1);
    $pdf->Ln(10);

    // Items Table
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 7, '#', 1, 0, 'C');
    $pdf->Cell(80, 7, 'Product', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Quantity', 1, 0, 'C');
    $pdf->Cell(35, 7, 'Unit Price', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Amount', 1, 1, 'C');    $pdf->SetFont('Arial', '', 10);

    $i = 1;
    foreach ($items as $item) {
        // Defensive: ensure all keys exist
        $productName = isset($item['product_name']) ? $item['product_name'] : '';
        $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
        $unitPrice = isset($item['unit_price']) ? $item['unit_price'] : 0;
        // 'total' or 'amount' fallback
        $amount = isset($item['total']) ? $item['total'] : (isset($item['amount']) ? $item['amount'] : ($unitPrice * $quantity));

        $pdf->Cell(10, 6, $i, 1, 0, 'C');
        $pdf->Cell(80, 6, $productName, 1, 0, 'L');
        $pdf->Cell(25, 6, $quantity, 1, 0, 'C');
        $pdf->Cell(35, 6, number_format($unitPrice, 2), 1, 0, 'R');
        $pdf->Cell(40, 6, number_format($amount, 2), 1, 1, 'R');
        $i++;
    }

    // Total    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(150, 7, 'Total Amount', 1, 0, 'R');
    $pdf->Cell(40, 7, number_format($invoice['total_amount'], 2), 1, 1, 'R');
    $pdf->Ln(10);

    // Payment Info
    $pdf->SetFont('Arial', 'B', 10);    $pdf->Cell(190, 7, 'Payment Information:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 5, 'Payment Method: ' . $invoice['payment_method'], 0, 1);
    $pdf->Cell(190, 5, 'Status: ' . ucfirst($invoice['invoice_status']), 0, 1);

    // Clean output buffer before sending PDF headers
    if (ob_get_length()) {
        ob_end_clean();
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice.pdf"');
    // Output PDF as string to avoid extra output
    echo $pdf->Output('S');
} catch (Exception $e) {
    die('Error generating PDF: ' . $e->getMessage());
}
