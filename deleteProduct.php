<?php
/////////////////////////// delete product ///////////////////////////
include 'dbconnection.php';

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
  $id = $data['id'];

  // Prepare and execute the delete statement
  $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "Product deleted successfully.";
  } else {
    echo "Error deleting product: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>
