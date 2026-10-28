<?php
require_once __DIR__ . "/koneksi.php";

$pesan = "";
$nim = $nama = $nilai_akhir = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nim         = $_POST['nim'];
  $nama        = $_POST['nama'];
  $nilai_akhir = $_POST['nilai_akhir'];
  $nilai       = (float) $nilai_akhir;

  // Konversi nilai ke huruf & predikat (sesuaikan batasnya dengan aturan dosen)
  if ($nilai >= 85)      { $huruf = 'A'; $predikat = 'Sangat Memuaskan'; }
  elseif ($nilai >= 70)  { $huruf = 'B'; $predikat = 'Memuaskan'; }
  elseif ($nilai >= 55)  { $huruf = 'C'; $predikat = 'Cukup'; }
  elseif ($nilai >= 40)  { $huruf = 'D'; $predikat = 'Kurang'; }
  else                   { $huruf = 'E'; $predikat = 'Sangat Kurang'; }

  try {
    $stmt = mysqli_prepare($conn, "INSERT INTO nilai_mhsw (nim, nama, nilai_akhir, nilai_huruf, predikat) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssdss", $nim, $nama, $nilai, $huruf, $predikat);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: bacanilai.php");
    exit;
  } catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
      $pesan = "NIM " . htmlspecialchars($nim) . " sudah terdaftar. Gunakan NIM lain, atau ubah datanya lewat tombol Edit.";
    } else {
      $pesan = "Error: " . htmlspecialchars($e->getMessage());
    }
  }
}

$judul = "Tambah Data Nilai";
include __DIR__ . "/layout_atas.php";
?>
<div class="card" style="max-width:560px">
  <div class="card-body">
    <?php if ($pesan != "") { ?>
      <div class="alert alert-danger"><?= $pesan ?></div>
    <?php } ?>

    <form method="post" action="tambahnilai.php">
      <div class="mb-3">
        <label for="nim" class="form-label">NIM</label>
        <input type="text" class="form-control" id="nim" name="nim" maxlength="15" required
               value="<?= htmlspecialchars($nim) ?>">
      </div>
      <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama" maxlength="50" required
               value="<?= htmlspecialchars($nama) ?>">
      </div>
      <div class="mb-3">
        <label for="nilai_akhir" class="form-label">Nilai Akhir</label>
        <input type="number" step="0.01" min="0" max="100" class="form-control" id="nilai_akhir"
               name="nilai_akhir" required value="<?= htmlspecialchars($nilai_akhir) ?>">
      </div>
      <a href="bacanilai.php" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>
</div>
<?php
include __DIR__ . "/layout_bawah.php";
?>
