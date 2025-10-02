<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'fluke_ims';

$conn = new mysqli($servername,$username,$password,$dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  // echo "<script>  alert('Connection Sucsssefully..') </script>";
  // echo "<script> console.log('Database Connection Sucsssefully..') </script>";
}
?>