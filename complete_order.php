<?php
require_once 'dbconnection.php';
require_once 'order_functions.php';

header('Content-Type: application/json');

try {
    // Get the order ID from the POST request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['order_id'])) {
        throw new Exception('Order ID is required');
    }
    
    $orderId = $data['order_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update order status to completed
        $query = "UPDATE orders SET status = 'Completed' WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $orderId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update order status: " . $stmt->error);
        }
        
        // Get order items to update inventory
        $query = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $orderId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to get order items: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        
        // Update inventory quantities
        foreach ($items as $item) {
            $query = "UPDATE inventory SET quantity = quantity - ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $item['quantity'], $item['product_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update inventory for product ID " . $item['product_id']);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Order completed successfully']);
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
