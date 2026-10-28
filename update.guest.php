
<?php
// Koneksi ke database
require_once __DIR__ . "/koneksi.php";

// Data dari form edit guest
$id = (int) $_POST['id'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];

// SQL untuk mengubah data
$sql = "UPDATE MyGuests
        SET firstname = '$fname',
            lastname = '$lname',
            email = '$email'
        WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    // Kembali ke tampilan data guest
    header('Location: bacadata2.php');
    exit;
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

$conn->close();
?>
