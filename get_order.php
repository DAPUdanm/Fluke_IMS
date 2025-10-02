<?php
require_once 'dbconnection.php';
require_once 'order_functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Order ID is required');
    }
    
    $orderId = trim($_GET['id']);
    $order = getOrder($orderId);
    if (!$order) {
        throw new Exception('Order not found');
    }
      $items = getOrderItems($orderId);
    if (!$items) {
        throw new Exception('Failed to retrieve order items');
    }

    $order['items'] = array();
    while ($item = $items->fetch_assoc()) {
        // Validate required fields exist
        if (!isset($item['product_id'], $item['product_name'], $item['quantity'], $item['unit_price'])) {
            continue; // Skip invalid items instead of throwing exception
        }
        
        // Format the data properly with additional product details
        $order['items'][] = array(
            'product_id' => (int)$item['product_id'],
            'product_name' => htmlspecialchars($item['product_name']),
            'description' => htmlspecialchars($item['description'] ?? ''),
            'quantity' => (float)$item['quantity'],
            'unit_price' => (float)$item['unit_price'],
            'amount' => (float)$item['amount'],
            'current_price' => (float)$item['current_price'],
            'category' => htmlspecialchars($item['category'] ?? '')
        );
    }

    // Sort items by product name for better readability
    if (!empty($order['items'])) {
        usort($order['items'], function($a, $b) {
            return strcmp($a['product_name'], $b['product_name']);
        });
    }
    
    if (empty($order['items'])) {
        $order['items'] = array(); // Ensure items is always an array
    }
    
    echo json_encode(['success' => true, 'order' => $order]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
