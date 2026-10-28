
<?php
// Terima data 1 record yang dipilih untuk diedit
// Yang diambil dari tampilan bacadata2.php pada posisi tombol Edit diklik
require_once __DIR__ . "/koneksi.php";

$id = (int) $_GET['id'];

$sql = "SELECT * FROM MyGuests WHERE id = $id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $id = $row["id"];
    $fname = $row["firstname"];
    $lname = $row["lastname"];
    $email = $row["email"];
} else {
    echo "Data tidak ditemukan";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Guest</title>
</head>
<body>

    <h2>Form Edit My Guest</h2>

    <form action="update.guest.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label for="fname">First Name:</label><br>
        <input type="text" name="fname" id="fname" value="<?php echo $fname; ?>" required><br>

        <label for="lname">Last Name:</label><br>
        <input type="text" name="lname" id="lname" value="<?php echo $lname; ?>" required><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" value="<?php echo $email; ?>" required><br><br>

        <input type="reset" value="Cancel">
        <input type="submit" value="Save">
    </form>


</body>
</html>
