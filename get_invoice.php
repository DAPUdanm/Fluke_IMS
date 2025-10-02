<?php
require_once 'dbconnection.php';

header('Content-Type: application/json');

try {    if (!isset($_GET['id'])) {
        throw new Exception('Invoice ID is required');
    }
    
    // Get invoice information
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
    
    // Get items from the product_details JSON field
    $invoice['items'] = json_decode($invoice['product_details'], true);
    
    // Remove the raw product_details field from response
    unset($invoice['product_details']);
    
    echo json_encode(['success' => true, 'invoice' => $invoice]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
