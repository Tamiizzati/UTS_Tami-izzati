<?php
require_once __DIR__ . "/koneksi.php";

$pesan = "";

// 1) Form dikirim: simpan perubahan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nim   = $_POST['nim'];
  $nama  = $_POST['nama'];
  $nilai = (float) $_POST['nilai_akhir'];

  // Konversi nilai ke huruf & predikat 
  if ($nilai >= 85)      { $huruf = 'A'; $predikat = 'Sangat Memuaskan'; }
  elseif ($nilai >= 70)  { $huruf = 'B'; $predikat = 'Memuaskan'; }
  elseif ($nilai >= 55)  { $huruf = 'C'; $predikat = 'Cukup'; }
  elseif ($nilai >= 40)  { $huruf = 'D'; $predikat = 'Kurang'; }
  else                   { $huruf = 'E'; $predikat = 'Sangat Kurang'; }

  try {
    $stmt = mysqli_prepare($conn, "UPDATE nilai_mhsw SET nama=?, nilai_akhir=?, nilai_huruf=?, predikat=? WHERE nim=?");
    mysqli_stmt_bind_param($stmt, "sdsss", $nama, $nilai, $huruf, $predikat, $nim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: bacanilai.php");
    exit;
  } catch (mysqli_sql_exception $e) {
    $pesan = "Error: " . htmlspecialchars($e->getMessage());
  }

  // Jika gagal, tampilkan lagi isian yang tadi diketik
  $data = ['nim' => $nim, 'nama' => $nama, 'nilai_akhir' => $_POST['nilai_akhir']];
} else {
  // 2) Dibuka dari tombol Edit: ambil data lama
  if (!isset($_GET['nim'])) {
    header("Location: bacanilai.php");
    exit;
  }
  $stmt = mysqli_prepare($conn, "SELECT nim, nama, nilai_akhir FROM nilai_mhsw WHERE nim = ?");
  mysqli_stmt_bind_param($stmt, "s", $_GET['nim']);
  mysqli_stmt_execute($stmt);
  $data = mysqli_stmt_get_result($stmt)->fetch_assoc();
  if (!$data) {
    die("Data tidak ditemukan.");
  }
}

$judul = "Edit Data Nilai";
include __DIR__ . "/layout_atas.php";
?>
<div class="card" style="max-width:560px">
  <div class="card-body">
    <?php if ($pesan != "") { ?>
      <div class="alert alert-danger"><?= $pesan ?></div>
    <?php } ?>

    <form method="post" action="editnilai.php">
      <div class="mb-3">
        <label class="form-label">NIM</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nim']) ?>" disabled>
        <input type="hidden" name="nim" value="<?= htmlspecialchars($data['nim']) ?>">
      </div>
      <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama" maxlength="50" required
               value="<?= htmlspecialchars($data['nama']) ?>">
      </div>
      <div class="mb-3">
        <label for="nilai_akhir" class="form-label">Nilai Akhir</label>
        <input type="number" step="0.01" min="0" max="100" class="form-control" id="nilai_akhir"
               name="nilai_akhir" required value="<?= htmlspecialchars($data['nilai_akhir']) ?>">
      </div>
      <a href="bacanilai.php" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</div>
<?php
include __DIR__ . "/layout_bawah.php";
?>
