<?php
require_once __DIR__ . "/koneksi.php";

if (isset($_GET['nim']) && trim($_GET['nim']) !== '') {
    $nim = trim($_GET['nim']);
    $stmt = mysqli_prepare($conn, "DELETE FROM nilai_mhsw WHERE nim = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $nim);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
header("Location: adminlte.php");
exit;
?>