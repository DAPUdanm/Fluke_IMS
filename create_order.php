<?php
require_once 'dbconnection.php';
require_once 'order_functions.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Log the incoming data
    error_log("Creating order with data: " . print_r($data, true));
    
    $result = createOrder(
        $data['customerName'],
        $data['customerAddress'],
        $data['contactNumber'],
        $data['paymentMethod'],
        $data['items']
    );
    
    // Log the result
    error_log("Order creation result: " . print_r($result, true));
    
    if ($result['success']) {
        // Verify order was created
        $order = getOrder($result['order_id']);
        if (!$order) {
            throw new Exception('Order was not created properly');
        }
        
        // Verify order items were created
        $items = getOrderItems($result['order_id']);
        if ($items->num_rows === 0) {
            throw new Exception('Order items were not created properly');
        }
        
        $result['debug'] = [
            'order' => $order,
            'items_count' => $items->num_rows
        ];
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    error_log("Order creation error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => ['error_trace' => $e->getTraceAsString()]
    ]);
}
