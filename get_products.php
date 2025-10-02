<?php
require_once 'dbconnection.php';

header('Content-Type: application/json');

try {    $query = "SELECT id, product_name, price, quantity FROM inventory WHERE quantity > 0 ORDER BY product_name";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
