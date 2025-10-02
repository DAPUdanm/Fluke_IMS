<?php
////////////////////////// edit product //////////////////////////
include 'dbconnection.php';

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'], $data['productName'], $data['quantity'], $data['price'], $data['category'])) {
  $id = $data['id'];
  $productName = $data['productName'];
  $quantity = $data['quantity'];
  $price = $data['price'];
  $category = $data['category'];

  $stmt = $conn->prepare("UPDATE inventory SET product_name = ?, quantity = ?, price = ?, category = ? WHERE id = ?");
  $stmt->bind_param("sdisi", $productName, $quantity, $price, $category, $id);

  if ($stmt->execute()) {
    echo "Product updated successfully.";
  } else {
    echo "Error updating product: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>
