<?php
require "koneksi.php";

// sql to create table
 $sql = "CREATE TABLE galeri (
 id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 namafile VARCHAR(100) NOT NULL,
 tglunggah TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 )";

// $sql = "CREATE TABLE MyGuests (
// id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
// firstname VARCHAR(30) NOT NULL,
// lastname VARCHAR(30) NOT NULL,
// email VARCHAR(50),
// reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )";
if (mysqli_query($conn, $sql)) {
  echo "Table <strong>galeri</strong> created successfully (atau sudah ada)";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>