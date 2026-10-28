<?php
$servername = "localhost";
$username = "username";
$password = "";
$dbname = "db5d";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, firstname, lastname FROM MyGuests";
$sql = "SELECT *FROM nilaimhsw";
// Execute the SQL query
$result = $conn->query($sql);

// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
   // echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
   echo "NIM: " . $row["nim"]. " - Nama Lengkap: " . $row["namalengkap"]  . "Nilai Akhir: " $row["nilaiakhir"]
  }
} else {
  echo "0 results - Data not found!";
}

$conn->close();
?>
Code Explanation