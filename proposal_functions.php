<?php
require_once 'dbconnection.php';
require_once 'fpdf.php';

// Function to generate a unique proposal ID
function generateProposalId() {
    $date = date('Ymd');
    $random = mt_rand(10000, 99999);
    return "PROP-{$date}-{$random}";
}

// Function to get all proposals
function getProposals() {
    global $conn;
    $query = "SELECT * FROM proposals ORDER BY created_at DESC";
    return $conn->query($query);
}

// Function to get a specific proposal
function getProposal($proposalId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM proposals WHERE proposal_id = ?");
    $stmt->bind_param("s", $proposalId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to get proposal items
function getProposalItems($proposalId) {
    global $conn;
    $stmt = $conn->prepare("SELECT pi.*, p.product_name FROM proposal_items pi 
                           JOIN inventory p ON pi.product_id = p.id 
                           WHERE pi.proposal_id = ?");
    $stmt->bind_param("s", $proposalId);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new proposal
function createProposal($customerName, $customerAddress, $contactNumber, $validityDate, $items, $terms) {
    global $conn;
    
    try {
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Start transaction
        if (!$conn->begin_transaction()) {
            throw new Exception("Could not start transaction");
        }
        
        $proposalId = generateProposalId();
        $dateCreated = date('Y-m-d');
        $totalAmount = 0;
        
        // Validate and calculate total amount
        foreach ($items as $item) {
            if (!isset($item['product_id'], $item['quantity'], $item['unit_price'], $item['amount'])) {
                throw new Exception("Invalid item data structure");
            }
            $totalAmount += floatval($item['amount']);
        }
        
        // Prepare and execute proposal insertion
        $stmt = $conn->prepare("INSERT INTO proposals (proposal_id, date_created, customer_name, 
                              customer_address, contact_number, validity_date, total_amount, terms) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                              
        if (!$stmt) {
            throw new Exception("Failed to prepare proposal statement: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssds", $proposalId, $dateCreated, $customerName, 
                         $customerAddress, $contactNumber, $validityDate, $totalAmount, $terms);
                         
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert proposal: " . $stmt->error);
        }
        
        // Insert proposal items
        $stmt = $conn->prepare("INSERT INTO proposal_items (proposal_id, product_id, quantity, unit_price, amount) 
                              VALUES (?, ?, ?, ?, ?)");
                              
        if (!$stmt) {
            throw new Exception("Failed to prepare items statement: " . $conn->error);
        }
          foreach ($items as $item) {
            $stmt->bind_param("siddd", $proposalId, $item['product_id'], 
                            $item['quantity'], $item['unit_price'], $item['amount']);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert item: " . $stmt->error);
            }
        }
        
        // Commit transaction
        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction");
        }
        
        return ['success' => true, 'proposal_id' => $proposalId];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn && $conn->ping()) {
            $conn->rollback();
        }
        error_log("Database error in createProposal: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Function to update a proposal
function updateProposal($proposalId, $customerName, $customerAddress, $contactNumber, $validityDate, $items, $terms) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Calculate new total
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item['amount'];
        }
        
        // Update proposal
        $stmt = $conn->prepare("UPDATE proposals SET customer_name = ?, customer_address = ?, 
                              contact_number = ?, validity_date = ?, total_amount = ?, terms = ?, 
                              modified = 1, last_modified = NOW() WHERE proposal_id = ?");
        $stmt->bind_param("ssssdss", $customerName, $customerAddress, $contactNumber, 
                         $validityDate, $totalAmount, $terms, $proposalId);
        $stmt->execute();
        
        // Delete old proposal items
        $stmt = $conn->prepare("DELETE FROM proposal_items WHERE proposal_id = ?");
        $stmt->bind_param("s", $proposalId);
        $stmt->execute();
        
        // Insert new proposal items
        $stmt = $conn->prepare("INSERT INTO proposal_items (proposal_id, product_id, quantity, unit_price, amount) 
                              VALUES (?, ?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $stmt->bind_param("sidd", $proposalId, $item['product_id'], $item['quantity'], 
                            $item['unit_price'], $item['amount']);
            $stmt->execute();
        }
        
        $conn->commit();
        return ['success' => true];
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Function to generate PDF for a proposal
function generateProposalPDF($proposalId) {
    global $conn;
    
    $proposal = getProposal($proposalId);
    $items = getProposalItems($proposalId);
    
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'PROPOSAL', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Company Info
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'FLUKE IMS', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 5, 'Address:  42/1B, Jayanthi Place, Gangarama Road, Boralesgamuwa.', 0, 1, 'L');
    $pdf->Cell(190, 5, 'Phone: 071-1032-454, 071-5676-999', 0, 1, 'L');
    $pdf->Cell(190, 5, 'Email: info@flkc.lk', 0, 1, 'L');
    $pdf->Ln(10);
    
    // Proposal Info
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 5, 'Proposal ID:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(160, 5, $proposal['proposal_id'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 5, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(160, 5, $proposal['date_created'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 5, 'Valid Until:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(160, 5, $proposal['validity_date'], 0, 1);
    $pdf->Ln(10);
    
    // Customer Info
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(190, 5, 'Customer Information:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 5, 'Name: ' . $proposal['customer_name'], 0, 1);
    $pdf->Cell(190, 5, 'Address: ' . $proposal['customer_address'], 0, 1);
    $pdf->Cell(190, 5, 'Contact: ' . $proposal['contact_number'], 0, 1);
    $pdf->Ln(10);
    
    // Items Table
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 7, '#', 1, 0, 'C');
    $pdf->Cell(80, 7, 'Product', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Quantity', 1, 0, 'C');
    $pdf->Cell(35, 7, 'Unit Price', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Amount', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 10);
    $i = 1;
    while ($item = $items->fetch_assoc()) {
        $pdf->Cell(10, 6, $i, 1, 0, 'C');
        $pdf->Cell(80, 6, $item['product_name'], 1, 0, 'L');
        $pdf->Cell(25, 6, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(35, 6, number_format($item['unit_price'], 2), 1, 0, 'R');
        $pdf->Cell(40, 6, number_format($item['amount'], 2), 1, 1, 'R');
        $i++;
    }
    
    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(150, 7, 'Total Amount', 1, 0, 'R');
    $pdf->Cell(40, 7, number_format($proposal['total_amount'], 2), 1, 1, 'R');
    $pdf->Ln(10);
    
    // Terms and Conditions
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(190, 7, 'Terms and Conditions:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(190, 5, $proposal['terms']);
    
    return $pdf->Output('S');
}
