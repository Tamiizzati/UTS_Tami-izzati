<?php
require_once __DIR__ . "/koneksi.php";

$judul = "Rekap Nilai Mahasiswa";

// ---------- Proses simpan dari modal "Tambah Data Nilai" ----------
$pesan = "";
$buka_modal = false;
$nim = $nama = $nilai_akhir = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nim         = trim($_POST['nim'] ?? '');
  $nama        = trim($_POST['nama'] ?? '');
  $nilai_akhir = $_POST['nilai_akhir'] ?? '';
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
      $pesan = "NIM " . htmlspecialchars($nim) . " sudah terdaftar. Gunakan NIM lain.";
    } else {
      $pesan = "Error: " . htmlspecialchars($e->getMessage());
    }
    $buka_modal = true; // buka lagi modalnya supaya pesan error terlihat
  }
}

// Parameter pencarian, filter, urutan, dan halaman
$cari     = trim($_GET['cari'] ?? '');
$filter   = $_GET['predikat'] ?? '';
$urut     = $_GET['urut'] ?? '';
$halaman  = max(1, (int) ($_GET['hal'] ?? 1));
$per_hal  = 9; // jumlah baris per halaman (sama seperti di gambar)

$daftar_predikat = ['Sangat Memuaskan', 'Memuaskan', 'Cukup', 'Kurang', 'Sangat Kurang'];

// Warna badge untuk setiap predikat
$warna_predikat = [
  'Sangat Memuaskan' => 'text-bg-success',
  'Memuaskan'        => 'text-bg-primary',
  'Cukup'            => 'text-bg-info',
  'Kurang'           => 'text-bg-warning',
  'Sangat Kurang'    => 'text-bg-danger',
];

// Bagian WHERE dipakai bersama oleh query hitung & query data
$where = " WHERE nama LIKE ?";
$tipe  = "s";
$param = ["%" . $cari . "%"];

if ($filter !== '' && in_array($filter, $daftar_predikat, true)) {
  $where  .= " AND predikat = ?";
  $tipe   .= "s";
  $param[] = $filter;
}

// 1) Hitung total data (untuk "Showing ... of ..." dan pagination)
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM nilai_mhsw" . $where);
mysqli_stmt_bind_param($stmt, $tipe, ...$param);
mysqli_stmt_execute($stmt);
$total = (int) mysqli_stmt_get_result($stmt)->fetch_assoc()['total'];
mysqli_stmt_close($stmt);

$total_hal = max(1, (int) ceil($total / $per_hal));
$halaman   = min($halaman, $total_hal);
$offset    = ($halaman - 1) * $per_hal;

// 2) Ambil data untuk halaman ini
$sql = "SELECT nim, nama, nilai_akhir, nilai_huruf, predikat, reg_date, tgl_diubah
        FROM nilai_mhsw" . $where;

if ($urut === 'terbesar') {
  $sql .= " ORDER BY nilai_akhir DESC, nim ASC";
} elseif ($urut === 'terkecil') {
  $sql .= " ORDER BY nilai_akhir ASC, nim ASC";
} else {
  $sql .= " ORDER BY nim ASC";
}
$sql .= " LIMIT ? OFFSET ?";

$tipe_data  = $tipe . "ii";
$param_data = array_merge($param, [$per_hal, $offset]);

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $tipe_data, ...$param_data);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$mulai  = $total > 0 ? $offset + 1 : 0;
$sampai = min($offset + $per_hal, $total);

// Helper: bikin link halaman dengan mempertahankan pencarian/filter/urutan
function link_hal($no) {
  $q = $_GET;
  $q['hal'] = $no;
  return '?' . http_build_query($q);
}

// Helper: inisial nama untuk avatar bulat
function inisial($nama) {
  $kata = preg_split('/\s+/', trim($nama));
  $i = strtoupper(substr($kata[0], 0, 1));
  if (count($kata) > 1) {
    $i .= strtoupper(substr(end($kata), 0, 1));
  }
  return $i;
}

include __DIR__ . "/layout_atas.php";
?>
<div class="card">
  <!-- Header: judul + pencarian + filter + tombol tambah -->
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="card-title mb-0">Direktori Nilai</h3>
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <form method="get" class="d-flex gap-2 align-items-center mb-0">
        <div class="input-group input-group-sm" style="width:230px">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="cari" class="form-control" placeholder="Cari Berdasarkan Nama"
                 value="<?= htmlspecialchars($cari) ?>">
        </div>
        <select name="predikat" class="form-select form-select-sm" style="width:170px" onchange="this.form.submit()">
          <option value="">Semua Predikat</option>
          <?php foreach ($daftar_predikat as $p) { ?>
            <option value="<?= $p ?>" <?= $filter === $p ? 'selected' : '' ?>><?= $p ?></option>
          <?php } ?>
        </select>
        <select name="urut" class="form-select form-select-sm" style="width:190px" onchange="this.form.submit()">
          <option value="">Urut: NIM</option>
          <option value="terbesar" <?= $urut === 'terbesar' ? 'selected' : '' ?>>Nilai Terbesar - Terkecil</option>
          <option value="terkecil" <?= $urut === 'terkecil' ? 'selected' : '' ?>>Nilai Terkecil - Terbesar</option>
        </select>
      </form>
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-person-plus-fill"></i> Tambah Data Nilai
      </button>
    </div>
  </div>

  <!-- Tabel -->
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th class="ps-3">Nama Lengkap</th>
          <th>NIM</th>
          <th>Nilai Akhir</th>
          <th>Nilai Huruf</th>
          <th>Predikat</th>
          <th>Tgl Dibuat</th>
          <th>Tgl Diubah</th>
          <th class="text-end pe-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td class="ps-3">
              <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle bg-secondary-subtle text-secondary-emphasis d-inline-flex align-items-center justify-content-center fw-semibold"
                      style="width:36px;height:36px;font-size:.8rem">
                  <?= htmlspecialchars(inisial($row['nama'])) ?>
                </span>
                <span><?= htmlspecialchars($row['nama']) ?></span>
              </div>
            </td>
            <td><?= htmlspecialchars($row['nim']) ?></td>
            <td><?= $row['nilai_akhir'] ?></td>
            <td><?= htmlspecialchars($row['nilai_huruf']) ?></td>
            <td>
              <span class="badge <?= $warna_predikat[$row['predikat']] ?? 'text-bg-secondary' ?>">
                <?= htmlspecialchars($row['predikat']) ?>
              </span>
            </td>
            <td><?= date('M j, Y', strtotime($row['reg_date'])) ?></td>
            <td><?= date('M j, Y', strtotime($row['tgl_diubah'])) ?></td>
            <td class="text-end pe-3 text-nowrap">
              <div class="btn-group btn-group-sm">
                <a href="editnilai.php?nim=<?= urlencode($row['nim']) ?>"
                   class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                <a href="deletenilai.php?nim=<?= urlencode($row['nim']) ?>"
                   class="btn btn-outline-danger" title="Hapus"
                   onclick="return confirm('Yakin Ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php } ?>
        <?php } else { ?>
          <tr><td colspan="8" class="text-center text-secondary py-4">Data tidak ditemukan</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- Footer kartu: info jumlah data + pagination -->
  <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
    <small class="text-secondary">
      Menampilkan <?= $mulai ?> sampai <?= $sampai ?> dari <?= $total ?> data
    </small>

    <ul class="pagination pagination-sm mb-0">
      <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= link_hal($halaman - 1) ?>">&laquo;</a>
      </li>
      <?php
      // Tampilkan maksimal 5 nomor halaman di sekitar halaman aktif
      $awal  = max(1, $halaman - 2);
      $akhir = min($total_hal, $awal + 4);
      $awal  = max(1, $akhir - 4);
      for ($i = $awal; $i <= $akhir; $i++) { ?>
        <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
          <a class="page-link" href="<?= link_hal($i) ?>"><?= $i ?></a>
        </li>
      <?php } ?>
      <li class="page-item <?= $halaman >= $total_hal ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= link_hal($halaman + 1) ?>">&raquo;</a>
      </li>
    </ul>
  </div>
</div>
<!-- Modal: Tambah Data Nilai -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="bacanilai.php">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Data Nilai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($pesan != "") { ?>
          <div class="alert alert-danger"><?= $pesan ?></div>
        <?php } ?>

        <div class="mb-3">
          <label for="nim" class="form-label">NIM</label>
          <input type="text" class="form-control" id="nim" name="nim" maxlength="15"
                 placeholder="mis. 2023010001" required value="<?= htmlspecialchars($nim) ?>">
          <div class="form-text">Maksimal 15 karakter, tidak boleh sama dengan NIM yang sudah ada.</div>
        </div>
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" maxlength="50"
                 placeholder="mis. Jane Doe" required value="<?= htmlspecialchars($nama) ?>">
        </div>
        <div class="mb-3">
          <label for="nilai_akhir" class="form-label">Nilai Akhir</label>
          <input type="number" step="0.01" min="0" max="100" class="form-control" id="nilai_akhir"
                 name="nilai_akhir" placeholder="0 - 100" required value="<?= htmlspecialchars($nilai_akhir) ?>">
          <div class="form-text">Nilai huruf dan predikat dihitung otomatis.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php if ($buka_modal) { ?>
<script>
  // Jika simpan gagal (mis. NIM kembar), buka lagi modalnya setelah Bootstrap termuat
  document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('modalTambah')).show();
  });
</script>
<?php } ?>
<?php
mysqli_stmt_close($stmt);
include __DIR__ . "/layout_bawah.php";
mysqli_close($conn);
?>