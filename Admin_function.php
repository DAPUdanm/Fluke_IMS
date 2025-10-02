<?php
include('dbconnection.php');


function getUsers(){

  global $conn;
  $query = "SELECT id, CONCAT(first_name, ' ', last_name) AS username, email, role FROM user";
  $result = mysqli_query($conn, $query);
  return $result;
}

// function updateUserRole($id, $newRole) {
//     global $conn;
//     $stmt = $conn->prepare("UPDATE user SET role = ? WHERE id = ?");
//     $stmt->bind_param("si", $newRole, $id);
//     return $stmt->execute();
// }

// function deleteUser($id) {
//     global $conn;
//     $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
//     $stmt->bind_param("i", $id);
//     return $stmt->execute();
// }


if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $new_role = $_POST['new_role'];

  $stmt = $conn->prepare("UPDATE user SET role = ? WHERE id = ?");
  $stmt->bind_param("si", $new_role, $id);

  if ($stmt->execute()) {
    echo "<script>alert('User role updated successfully.');</script>";
  } else {
    echo "<script>alert('Failed to update user role.');</script>";
  }
  $stmt->close();
  header('Location: Admin.php');
  exit();
}

if (isset($_POST['delete'])) {
  $id = $_POST['id'];

  $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "<script>alert('User deleted successfully.');</script>";
  } else {
    echo "<script>alert('Failed to delete user.');</script>";
  }
  $stmt->close();
  header('Location: Admin.php');
  exit();
}

/////////////////////////////////////////// Inventory Management ///////////////////////////////////////////

// Check if the form data has been sent
if (isset($_POST['productName'], $_POST['quantity'], $_POST['price'], $_POST['category'])) {
  // Retrieve form data
  $productName = $_POST['productName'];
  $quantity = $_POST['quantity'];
  $price = $_POST['price'];
  $category = $_POST['category'];

  // Insert data into the inventory table
  $sql = "INSERT INTO inventory (product_name, quantity, price, category) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);

  if ($stmt) {
      // Bind parameters
      $stmt->bind_param("sdis", $productName, $quantity, $price, $category);

      // Execute the query
      if ($stmt->execute()) {
          echo "<script>alert('Product added successfully!');</script>";
      } else {
          echo "Error executing statement: " . $stmt->error;
      }

      // Close statement
      $stmt->close();
  } else {
      echo "Error preparing statement: " . $conn->error;
  }
} else {
  echo "";
  // echo "Form data is missing!";
}


?>
