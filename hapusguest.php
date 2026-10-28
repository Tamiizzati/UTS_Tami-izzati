<?php
// koneksi ke db
require_once __DIR__ . " /koneksi.php";

// SQL to delete a record
$sql = "DELETE FROM MyGuests WHERE id=$_GET[id]";

if (mysqli_query($conn, $sql)) {
    //jika penghapusan berhasil
  echo "<script>alert('Record deleted successfully');</script>";

}
// jika penghapusan gagal
 else {
  echo "Error deleting record: " . mysqli_error($conn);
}

// kembali ke tampilan data  guest (file)
header('Location: bacadata2.php');
//tutup/ akhir koneksi ke db
//mysqli_close($conn);
?>