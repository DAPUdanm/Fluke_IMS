<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/login_error.log');
session_start();
include('dbconnection.php');

$error_message = '';

if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Fetch user by email
  $stmt = $conn->prepare("SELECT id, email, password, role FROM user WHERE email = ? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    // Verify password using password_verify
    if (password_verify($password, $row['password'])) {
      // Set session variables for access control
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['role'] = $row['role']; // 'admin' or 'user'
      $_SESSION['user_name'] = $row['email'];
      // Redirect based on role
      if (strtolower($_SESSION['role']) === 'admin') {
        error_log('Redirecting to Admin.php for user_id: ' . $_SESSION['user_id']);
        header('Location: Admin.php');
        exit();
      } else {
        error_log('Redirecting to user.php for user_id: ' . $_SESSION['user_id']);
        header('Location: user.php');
        exit();
      }
    } else {
      echo '<script> alert ("Username or Password is incorrect01"); </script>';
    }
  } else {
    echo '<script> alert ("Username or Password is incorrect02"); </script>';
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/styles.css">
  <link rel="website icon" type="png" href="image/Logo.png" />
  <title>Login</title>
</head>

<body> 
  <div class="container">
    <div class="image-section">
      <div class="image-placeholder">
        <img src="images/system logo 3.png" alt="" class="img">
      </div>
    </div>
    <div class="form-section">
      <div class="signup-container">
        <h2>Login</h2>
        <form action="Login.php" method="POST">

          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="email address" required>
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="password" required>
          </div>

          <button type="submit" id="submit" name="submit">Login</button>
          <p class="signin-text">No account yet?<br> <a href="SignUp.php">Sign up</a></p>
        </form>
      </div>
    </div>
  </div>
</body>

</html>