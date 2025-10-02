<?php
include 'dbconnection.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['productName'], $data['quantity'], $data['price'], $data['category'])) {
    $productName = $data['productName'];
    $quantity = $data['quantity'];
    $price = $data['price'];
    $category = $data['category'];

    // Insert data into the inventory table
    $sql = "INSERT INTO inventory (product_name, quantity, price, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("sdis", $productName, $quantity, $price, $category);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding product: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
}

$conn->close();
?>