<?php
require "koneksi.php";

// baca data darii kiriman from: form_guest.html
// $fname = $_POST[''];
// $lname = $_POST[''];
// $email = $_POST[''];

// sql to create table
$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

if (mysqli_query($conn, $sql)) {
  echo "New record inserted successfully";
} else {
  echo "Error inserted table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>