<?php

// baca data darii kiriman from: form_guest.html
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];

require "koneksi.php";


// sql to create table
$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('$fname', '$lname', '$email')";

if (mysqli_query($conn, $sql)) {
  echo "New record inserted successfully";
} else {
  echo "Error inserted table: " . mysqli_error($conn);
}

//mysqli_close($conn);
header('Location: bacadata2.php');
?>