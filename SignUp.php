<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/styles.css">
  <link rel="website icon" type="png" href="image/Logo.png" />
  <title>Sign Up</title>
  <script>
    function validatePasswordStrength(password) {
      // At least 8 chars, 1 uppercase, 1 lowercase, 1 digit, 1 special char
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;
      return regex.test(password);
    }
    function checkPasswordInput() {
      const password = document.getElementById('password').value;
      const message = document.getElementById('password-strength-message');
      if (!validatePasswordStrength(password)) {
        message.style.display = 'block';
        return false;
      } else {
        message.style.display = 'none';
        return true;
      }
    }
    function validateForm(e) {
      if (!checkPasswordInput()) {
        alert('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
        e.preventDefault();
        return false;
      }
      return true;
    }
    window.onload = function() {
      document.getElementById('password').addEventListener('input', checkPasswordInput);
      document.querySelector('form').addEventListener('submit', validateForm);
    }
  </script>
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
        <h2>Sign Up</h2>
        <form action="SignUp.php" method="POST">
          <div class="input-row">
            <div class="input-group">
              <label for="first-name">First Name</label>
              <input type="text" id="first-name" name="first-name" placeholder="firstname" required>
            </div>
            <div class="input-group">
              <label for="last-name">Last Name</label>
              <input type="text" id="last-name" name="last-name" placeholder="lastname" required>
            </div>
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="email" required>
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="password" required>
            <small id="password-strength-message" style="color:red; display:none;">Password must be at least 8 characters, include uppercase, lowercase, number, and special character.</small>
          </div>
          <div class="input-group">
            <label for="confirm-password">Renter password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="renter password" required>
          </div>
          <button type="submit" name="submit">Register</button>
          <p class="signin-text">Already have an account? <br> <a href="Login.php">Sign in</a></p>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

<?php

include('dbconnection.php');

if (isset($_POST['submit'])) {

    $firstname = $_POST['first-name'];
    $lastname = $_POST['last-name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirm-password'];

    if ($password === $confirmpassword) {
        // Password strength validation (server-side)
        $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*()_+\-=[\\]{};':\"\\|,.<>\/?]).{8,}$/";
        if (!preg_match($pattern, $password)) {
            echo '<script> alert("Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.");</script>';
        } else {
            // Hash the password before saving
            $new_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO user (first_name, last_name, email, password) VALUES ('$firstname', '$lastname', '$email', '$new_password')";

            if ($conn->query($sql) === true) {
                echo '<script> alert("Data Insert Successful"); </script>';
                header('Location: Login.php');
                exit();
            } else {
                echo "Something went wrong!";
            }
        }
    } else {
        echo '<script> alert("Passwords do not match. Please try again.");</script>';
    }
}

?>
