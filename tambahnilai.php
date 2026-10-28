
<?php
require_once __DIR__ . "/koneksi.php";

// Konversi nilai ke huruf dan predikat
function konversi($nilai) {
    if ($nilai >= 85) {
        return ['A', 'Sangat Memuaskan'];
    } elseif ($nilai >= 70) {
        return ['B', 'Memuaskan'];
    } elseif ($nilai >= 55) {
        return ['C', 'Cukup'];
    } elseif ($nilai >= 40) {
        return ['D', 'Kurang'];
    } else {
        return ['E', 'Sangat Kurang'];
    }
}

// Proses simpan dan update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mode = $_POST['mode'] ?? 'tambah';
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $nilai = (float) ($_POST['nilai_akhir'] ?? 0);

    list($huruf, $predikat) = konversi($nilai);

    if ($mode == 'edit') {
        // NIM lama digunakan untuk mencari data yang akan diubah
        $nim_lama = trim($_POST['nim_lama'] ?? '');

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE nilai_mhsw
             SET nim=?, nama=?, nilai_akhir=?, nilai_huruf=?, predikat=?
             WHERE nim=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdsss",
            $nim,
            $nama,
            $nilai,
            $huruf,
            $predikat,
            $nim_lama
        );
    } else {
        // Tambah data baru
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO nilai_mhsw
             (nim, nama, nilai_akhir, nilai_huruf, predikat)
             VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssdss",
            $nim,
            $nama,
            $nilai,
            $huruf,
            $predikat
        );
    }

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: bacanilai.php");
        exit;
    } else {
        echo "Gagal menyimpan data: " .
             htmlspecialchars(mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
    }
}

// Menentukan mode tambah atau edit
$edit = isset($_GET['nim']);

$data = [
    'nim' => '',
    'nama' => '',
    'nilai_akhir' => ''
];

$nim_lama = '';

if ($edit) {
    $nim_lama = $_GET['nim'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT nim, nama, nilai_akhir
         FROM nilai_mhsw
         WHERE nim=?"
    );

    mysqli_stmt_bind_param($stmt, "s", $nim_lama);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$data) {
        die("Data mahasiswa tidak ditemukan.");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>
        <?= $edit ? "Form Edit Nilai" : "Tambah Data Nilai" ?>
    </title>
</head>
<body>

<h3>
    <?= $edit
        ? "Form Edit Nilai Mahasiswa"
        : "Tambah Data Nilai Mahasiswa" ?>
</h3>

<form method="post" action="tambahnilai.php">

    <input type="hidden" name="mode"
           value="<?= $edit ? 'edit' : 'tambah' ?>">

    <?php if ($edit) { ?>
        <input type="hidden" name="nim_lama"
               value="<?= htmlspecialchars($nim_lama) ?>">
    <?php } ?>

    <label for="nim">NIM</label><br>
    <input
        type="text"
        id="nim"
        name="nim"
        required
        value="<?= htmlspecialchars($data['nim']) ?>"
        disabled
    >
    <br>

    <label for="nama">Nama</label><br>
    <input
        type="text"
        id="nama"
        name="nama"
        required
        value="<?= htmlspecialchars($data['nama']) ?>"
    >
    <br>

    <label for="nilai_akhir">Nilai Akhir</label><br>
    <input
        type="number"
        step="0.01"
        min="0"
        max="100"
        id="nilai_akhir"
        name="nilai_akhir"
        required
        value="<?= htmlspecialchars((string)$data['nilai_akhir']) ?>"
    >
    <br><br>

   <input type="submit" value="Simpan">

<a href="bacanilai.php">
    <button type="button">Batal</button>
</a>

</form>

</body>
</html>
