<?php
///////////////////// get product details /////////////////////
include 'dbconnection.php';

if (isset($_POST['product_id'])) {
  $product_id = $_POST['product_id'];
  
  // Fetch product details
  $sql = "SELECT * FROM inventory WHERE product_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode($product);
  } else {
    echo json_encode(["error" => "Product not found"]);
  }

  $stmt->close();
  $conn->close();
}


///////////////////// get product for proposal /////////////////////

if (isset($_GET['productId'])) {
  $productId = $_GET['productId'];
  $query = "SELECT price AS unit_price FROM inventory WHERE id = '$productId'";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
      $product = $result->fetch_assoc();
      echo json_encode($product);
  } else {
      echo json_encode(["unit_price" => ""]);
  }
}

?>
