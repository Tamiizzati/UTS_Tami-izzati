<?php
require_once __DIR__ . "/koneksi.php";

// ubah nilai angka jadi huruf + predikat
function konversiNilai($nilai) {
    if ($nilai >= 85) return ['A', 'Sangat Memuaskan'];
    if ($nilai >= 70) return ['B', 'Memuaskan'];
    if ($nilai >= 55) return ['C', 'Cukup'];
    if ($nilai >= 40) return ['D', 'Kurang'];
    return ['E', 'Sangat Kurang'];
}

// proses form tambah & edit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $nilai = (float)($_POST['nilai_akhir'] ?? 0);
    list($huruf, $predikat) = konversiNilai($nilai);

    if ($action === 'tambah' && $nim !== '' && $nama !== '') {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO nilai_mhsw (nim, nama, nilai_akhir, nilai_huruf, predikat) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssdss", $nim, $nama, $nilai, $huruf, $predikat);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: adminlte.php");
        exit;
    } elseif ($action === 'edit' && $nim !== '' && $nama !== '') {
        $nim_lama = trim($_POST['nim_lama'] ?? $nim);
        $stmt = mysqli_prepare($conn,
            "UPDATE nilai_mhsw SET nim=?, nama=?, nilai_akhir=?, nilai_huruf=?, predikat=? WHERE nim=?");
        mysqli_stmt_bind_param($stmt, "ssdsss", $nim, $nama, $nilai, $huruf, $predikat, $nim_lama);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: adminlte.php");
        exit;
    }
}

$result = mysqli_query($conn, "SELECT * FROM nilai_mhsw");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Rekap Nilai Mahasiswa</title>

    <!-- atur tema terang/gelap sebelum halaman tampil supaya tidak berkedip -->
    <script>
      (() => {
        'use strict';
        const root = document.documentElement;
        if (root.getAttribute('data-lte-color-mode') === 'off') return;

        let stored = null;
        try {
          stored = localStorage.getItem('lte-theme');
        } catch {
          // localStorage bisa saja tidak tersedia
        }
        // prioritas: pilihan tersimpan, lalu tema dari halaman, lalu setelan sistem
        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
          resolved = stored;
        } else if (authored === 'dark' || authored === 'light') {
          resolved = authored;
        } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
          resolved = 'dark';
        }
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
        if (resolved !== authored) {
          root.setAttribute('data-lte-theme-resolved', '');
        }
      })();
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

    <meta name="title" content="AdminLTE 4 | Users" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a free Bootstrap 5 admin dashboard template with almost 50 example pages, built with vanilla JS and designed with accessibility in mind."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel"
    />
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="./css/adminlte.css" as="style" />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="./css/adminlte.css" />
  </head>
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
      <nav class="app-header navbar navbar-expand bg-body">

      </nav>

      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Rekap Nilai Mahasiswa</h1>
              </div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nilai</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <div class="app-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card mb-4">

                  <!-- bagian atas kartu: judul, pencarian, filter, tombol tambah -->
                  <div class="card-header">
                    <div class="row g-2 align-items-center">
                      <div class="col-12 col-md-4">
                        <h3 class="card-title">Daftar Nilai Mahasiswa</h3>
                      </div>
                      <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                          <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text">
                              <i class="bi bi-search" aria-hidden="true"></i>
                            </span>
                            <input
                              type="search"
                              id="user-search"
                              class="form-control"
                              placeholder="Cari Berdasarkan nama"
                              aria-label="Search name"
                              style="width: 180px"
                            />
                          </div>
                          <select
                            id="user-role-filter"
                            class="form-select form-select-sm w-auto"
                            aria-label="Filter by role"
                          >
                            <option value="all" selected>Semua Predikat</option>
                            <option value="Sangat Memuaskan">Sangat Memuaskan</option>
                            <option value="Memuaskan">Memuaskan</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Kurang">Kurang</option>
                            <option value="Sangat Kurang">Sangat Kurang</option>
                          </select>
                          <div class="input-group input-group-sm w-auto">
                            <label class="input-group-text" for="page-size-select">Page Size</label>
                            <select
                              id="page-size-select"
                              class="form-select form-select-sm"
                              aria-label="Limit Page Size"
                              title="Pilih ukuran batas data per halaman"
                            >
                              <option value="5" selected>5</option>
                              <option value="10">10</option>
                              <option value="15">15</option>
                            </select>
                          </div>
                          <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-tambah-nilai"
                          >
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"> </i>
                            Tambah Data Nilai
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

<?php
if (mysqli_num_rows($result) > 0) {
?>

                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle m-0">
                        <thead>
                          <tr>
                            <th>NIM</th>
                            <th>Foto & Nama Mahasiswa</th>
                            <th id="th-nilai-akhir" role="button" style="cursor: pointer; user-select: none;" title="Klik untuk mengurutkan nilai: Klik 1 (Terendah ke Tertinggi), Klik 2 (Tertinggi ke Terendah)">
                              Nilai Akhir
                              <i id="sort-icon-nilai" class="bi bi-arrow-down-up ms-1 text-muted" aria-hidden="true"></i>
                            </th>
                            <th>Nilai Huruf</th>
                            <th>Predikat</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-end">Aksi</th>
                          </tr>
                        </thead>
                        <tbody id="table-body-nilai">
                        <?php
                        $rowIndex = 0;
                        while($row = mysqli_fetch_assoc($result)) {
                            // pakai kolom photo/foto kalau ada, kalau tidak pakai foto default
                            $photo = !empty($row["photo"]) ? $row["photo"] : (!empty($row["foto"]) ? $row["foto"] : "./assets/img/user1-128x128.jpg");
                            if (!empty($row["photo"]) && !file_exists($row["photo"])) {
                                foreach (["./uploads/", "./galeri/", "./assets/img/"] as $folder) {
                                    if (file_exists($folder . $row["photo"])) {
                                        $photo = $folder . $row["photo"];
                                        break;
                                    }
                                }
                            }
                        ?>
                          <tr class="data-row" data-index="<?php echo $rowIndex++; ?>" data-nilai="<?php echo (float)$row["nilai_akhir"]; ?>">
                            <td>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($row["nim"]); ?>
                                </span>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img
                                  src="<?php echo htmlspecialchars($photo); ?>"
                                  alt="<?php echo htmlspecialchars($row["nama"]); ?>"
                                  class="img-size-32 rounded-circle me-2"
                                  style="width: 32px; height: 32px; object-fit: cover;"
                                  onerror="this.onerror=null; this.src='./assets/img/user1-128x128.jpg';"
                                />
                                <span class="fw-medium col-nama"><?php echo htmlspecialchars($row["nama"]); ?></span>
                              </div>
                            </td>
                            <td>
                                <span class="fw-medium col-nilai"><?php echo htmlspecialchars($row["nilai_akhir"]); ?></span>
                            </td>
                            <td>
                              <span class="fw-medium"><?php echo htmlspecialchars($row["nilai_huruf"]); ?></span>
                            </td>
                            <td>
                              <span class="fw-medium col-predikat"><?php echo htmlspecialchars($row["predikat"]); ?></span>
                            </td>
                            <td>
                                <span class="fw-medium"><?php echo htmlspecialchars($row["reg_date"]); ?></span>
                            </td>
                            <td class="text-end">
                              <div class="btn-group btn-group-sm">
                                <button
                                  type="button"
                                  class="btn btn-outline-secondary btn-edit-nilai"
                                  data-nim="<?php echo htmlspecialchars($row['nim']); ?>"
                                  data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                  data-nilai="<?php echo htmlspecialchars($row['nilai_akhir']); ?>"
                                  aria-label="Ubah"
                                  title="Ubah data nilai mahasiswa"
                                >
                                  <i class="bi bi-pencil" aria-hidden="true"> </i>
                                </button>
                                <a
                                  href="deletenilai.php?nim=<?php echo urlencode($row['nim']); ?>"
                                  class="btn btn-outline-danger"
                                  aria-label="Hapus"
                                  title="Hapus data mahasiswa"
                                  onclick="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa <?php echo htmlspecialchars(addslashes($row['nama'])); ?> (NIM: <?php echo htmlspecialchars($row['nim']); ?>)? Tindakan ini tidak dapat dibatalkan.');"
                                >
                                  <i class="bi bi-trash" aria-hidden="true"> </i>
                                </a>
                              </div>
                            </td>
                          </tr>
                        <?php
                        }
                        ?>
                          <tr id="no-data-row" style="display: none;">
                            <td colspan="7" class="text-center py-4 text-muted">
                              <i class="bi bi-info-circle me-1"></i> Data tidak ditemukan
                            </td>
                          </tr>
                        </tbody>
                    </table>

<?php
} else {
  echo "0 results - Data tidak ditemukan";
}
mysqli_close($conn);
?>
                    </div>
                  </div>

                  <div class="card-footer clearfix" id="pagination-footer">
                    <div class="float-start pt-1 fs-7 text-body-secondary" id="pagination-info">
                      Memuat data...
                    </div>
                    <ul class="pagination pagination-sm m-0 float-end" id="pagination-controls">
                      <!-- diisi oleh JavaScript -->
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- modal tambah data -->
            <div
              class="modal fade"
              id="modal-tambah-nilai"
              tabindex="-1"
              aria-labelledby="modal-tambah-nilai-label"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form method="post" action="adminlte.php">
                    <input type="hidden" name="action" value="tambah" />
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-tambah-nilai-label">Tambah Data Nilai Mahasiswa</h5>
                      <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                      ></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="tambah-nim" class="form-label">NIM</label>
                        <input
                          type="text"
                          class="form-control"
                          id="tambah-nim"
                          name="nim"
                          placeholder="misal 3202416022"
                          required
                        />
                      </div>
                      <div class="mb-3">
                        <label for="tambah-nama" class="form-label">Nama Lengkap</label>
                        <input
                          type="text"
                          class="form-control"
                          id="tambah-nama"
                          name="nama"
                          placeholder="misal Jane Doe"
                          required
                        />
                      </div>
                      <div class="mb-3">
                        <label for="tambah-nilai" class="form-label">Nilai Akhir</label>
                        <input
                          type="number"
                          step="0.01"
                          min="0"
                          max="100"
                          class="form-control"
                          id="tambah-nilai"
                          name="nilai_akhir"
                          placeholder="misal 85.50"
                          required
                        />
                        <div class="form-text text-muted">Nilai huruf dan predikat dihitung otomatis oleh sistem.</div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                      </button>
                      <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- modal edit data -->
            <div
              class="modal fade"
              id="modal-edit-nilai"
              tabindex="-1"
              aria-labelledby="modal-edit-nilai-label"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form method="post" action="adminlte.php">
                    <input type="hidden" name="action" value="edit" />
                    <input type="hidden" name="nim_lama" id="edit-nim-lama" value="" />
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-edit-nilai-label">Ubah Data Nilai Mahasiswa</h5>
                      <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                      ></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="edit-nim" class="form-label">NIM</label>
                        <input
                          type="text"
                          class="form-control"
                          id="edit-nim"
                          name="nim"
                          readonly
                          required
                        />
                        <div class="form-text text-muted">NIM adalah identitas unik mahasiswa dan tidak dapat diubah.</div>
                      </div>
                      <div class="mb-3">
                        <label for="edit-nama" class="form-label">Nama Lengkap</label>
                        <input
                          type="text"
                          class="form-control"
                          id="edit-nama"
                          name="nama"
                          placeholder="misal Jane Doe"
                          required
                        />
                      </div>
                      <div class="mb-3">
                        <label for="edit-nilai" class="form-label">Nilai Akhir</label>
                        <input
                          type="number"
                          step="0.01"
                          min="0"
                          max="100"
                          class="form-control"
                          id="edit-nilai"
                          name="nilai_akhir"
                          placeholder="misal 85.50"
                          required
                        />
                        <div class="form-text text-muted">Nilai huruf dan predikat akan otomatis diperbarui.</div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                      </button>
                      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>
        </div>
      </main>

      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">
            Pemrograman Web Semester 5 - Desain menggunakan
            <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </div>
        <strong>
          Hak Cipta &copy; Pada Prodi TI 2026&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        Seluruh Hak Cipta Dilindungi.
      </footer>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="./js/adminlte.js"></script>

    <!-- scrollbar sidebar (dimatikan di mobile biar tidak mengganggu sentuhan) -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        const isMobile = window.innerWidth <= 992;

        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined && !isMobile) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: 'os-theme-light',
              autoHide: 'leave',
              clickScroll: true,
            },
          });
        }
      });
    </script>

    <!-- cari, filter predikat, urut nilai, dan pagination -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("user-search");
        const predikatFilter = document.getElementById("user-role-filter");
        const pageSizeSelect = document.getElementById("page-size-select");
        const thNilai = document.getElementById("th-nilai-akhir");
        const sortIcon = document.getElementById("sort-icon-nilai");
        const tbody = document.getElementById("table-body-nilai");
        const noDataRow = document.getElementById("no-data-row");
        const info = document.getElementById("pagination-info");
        const pagination = document.getElementById("pagination-controls");

        // kalau tidak ada data di tabel, tidak perlu lanjut
        if (!tbody) return;

        const semuaBaris = Array.from(tbody.querySelectorAll("tr.data-row"));
        let halaman = 1;
        let urutan = null; // null, "asc", atau "desc"

        // ambil baris yang cocok dengan pencarian dan filter, lalu urutkan
        function ambilBarisTersaring() {
          const kata = searchInput.value.toLowerCase().trim();
          const predikat = predikatFilter.value.toLowerCase().trim();

          const hasil = semuaBaris.filter(function (baris) {
            const nama = baris.querySelector(".col-nama").textContent.toLowerCase().trim();
            const pred = baris.querySelector(".col-predikat").textContent.toLowerCase().trim();
            return nama.includes(kata) && (predikat === "all" || pred === predikat);
          });

          hasil.sort(function (a, b) {
            if (urutan === "asc") return a.dataset.nilai - b.dataset.nilai;
            if (urutan === "desc") return b.dataset.nilai - a.dataset.nilai;
            return a.dataset.index - b.dataset.index; // urutan asli dari database
          });

          return hasil;
        }

        function tampilkan() {
          const perHalaman = parseInt(pageSizeSelect.value, 10);
          const hasil = ambilBarisTersaring();
          const totalHalaman = Math.max(1, Math.ceil(hasil.length / perHalaman));
          if (halaman > totalHalaman) halaman = totalHalaman;

          const awal = (halaman - 1) * perHalaman;
          const akhir = awal + perHalaman;

          // sembunyikan semua dulu, lalu tampilkan yang ada di halaman ini
          semuaBaris.forEach(function (baris) { baris.style.display = "none"; });
          hasil.forEach(function (baris, i) {
            tbody.insertBefore(baris, noDataRow);
            if (i >= awal && i < akhir) baris.style.display = "";
          });

          noDataRow.style.display = hasil.length === 0 ? "" : "none";

          info.textContent = hasil.length === 0
            ? "Menampilkan 0 data"
            : `Menampilkan ${awal + 1} sampai ${Math.min(akhir, hasil.length)} dari total ${hasil.length} data`;

          buatPagination(totalHalaman, hasil.length);
          updateIkon();
        }

        function buatPagination(totalHalaman, totalData) {
          pagination.innerHTML = "";
          if (totalData === 0) return;

          let html = `<li class="page-item ${halaman === 1 ? "disabled" : ""}">
                        <a class="page-link" href="#" data-page="${halaman - 1}" aria-label="Previous">&laquo;</a></li>`;
          for (let p = 1; p <= totalHalaman; p++) {
            html += `<li class="page-item ${p === halaman ? "active" : ""}">
                       <a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
          }
          html += `<li class="page-item ${halaman === totalHalaman ? "disabled" : ""}">
                     <a class="page-link" href="#" data-page="${halaman + 1}" aria-label="Next">&raquo;</a></li>`;
          pagination.innerHTML = html;
        }

        function updateIkon() {
          if (urutan === "asc") {
            sortIcon.className = "bi bi-arrow-up ms-1 text-primary fw-bold";
            sortIcon.title = "Urutan: Nilai Terendah ke Tertinggi (ASC) - Klik lagi untuk DESC";
          } else if (urutan === "desc") {
            sortIcon.className = "bi bi-arrow-down ms-1 text-primary fw-bold";
            sortIcon.title = "Urutan: Nilai Tertinggi ke Terendah (DESC) - Klik lagi untuk ASC";
          } else {
            sortIcon.className = "bi bi-arrow-down-up ms-1 text-muted";
            sortIcon.title = "Klik untuk mengurutkan nilai (ASC / DESC)";
          }
        }

        // klik header Nilai Akhir: pertama ASC, berikutnya DESC
        thNilai.addEventListener("click", function () {
          urutan = urutan === "asc" ? "desc" : "asc";
          halaman = 1;
          tampilkan();
        });

        // klik nomor halaman / tombol sebelumnya / berikutnya
        pagination.addEventListener("click", function (e) {
          const link = e.target.closest("a.page-link");
          if (!link) return;
          e.preventDefault();
          if (link.parentElement.classList.contains("disabled")) return;
          halaman = parseInt(link.dataset.page, 10);
          tampilkan();
        });

        // kalau pencarian, filter, atau jumlah per halaman berubah, kembali ke halaman 1
        [searchInput, predikatFilter, pageSizeSelect].forEach(function (el) {
          el.addEventListener(el === searchInput ? "input" : "change", function () {
            halaman = 1;
            tampilkan();
          });
        });

        // tombol edit: isi form modal dengan data baris yang dipilih
        document.addEventListener("click", function (e) {
          const btn = e.target.closest(".btn-edit-nilai");
          if (!btn) return;

          document.getElementById("edit-nim-lama").value = btn.dataset.nim;
          document.getElementById("edit-nim").value = btn.dataset.nim;
          document.getElementById("edit-nama").value = btn.dataset.nama;
          document.getElementById("edit-nilai").value = btn.dataset.nilai;

          bootstrap.Modal.getOrCreateInstance(document.getElementById("modal-edit-nilai")).show();
        });

        tampilkan();
      });
    </script>
  </body>
</html>