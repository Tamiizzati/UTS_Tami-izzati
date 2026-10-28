<?php
if(isset($_POST["submit"])) {
    // $namaFile = $_FILES["foto"]["name"];
    // $tipeFile = $_FILES["foto"]["type"];
    $ukuranFile = $_FILES["foto"]["size"];
    $fileSementara = $_FILES["foto"]["tmp_name"];
    // $statusError = $_FILES["foto"]["error"];

    // // tampilkan
    // echo "Nama File: ".$namaFile."<br>";
    // echo "Tipe File: ".$tipeFile."<br>";
    echo "Ukuran File: ".$ukuranFile."<br>";
    echo "File Sementara: ".$fileSementara."<br>";
    // echo "Status Error: ".$statusError."<br>";

    // inisialisasi status upload 
    $uploadOK = 1;

    // Checkpoint 1(Autentikasi)
    // Memeriksa apakah file yang diunggah 'real image' atau 'false image'
    $cek = getimagesize($fileSementara);
    if ($cek !== false) {
        echo "File adalah gambar ASLI - " . $cek["mime"] . ".";
        $uploadOK = 1;
    } else {
        echo "File adalah BUKAN gambar!.";
        $uploadOK = 0;
    }

    // Checkpoint 2(Duplikasi)
    // Definisikan direktori target/tujuan
    $target_dir = "galeri/";
    $file_path = $target_dir . basename($_FILES["foto"]["name"]);
    // Mengubah ekstensi file menjadi huruf kecil
    $imageFileType = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

    // Memeriksa apakah nama file yang SAMA ada di direktori tujuan
    if (file_exists($file_path)) {
        echo "Maaf, file " . basename($_FILES['foto']['name']) . " sudah ada.";
        $uploadOK = 0;
    }

    // Checkpoint 3(Pembatasan Ukuran File)
    // Memeriksa apakah ukuran file yang diunggah TIDAK MELEBIHI ukuran yg ditentukan
    if ($ukuranFile > 1000000) {
        echo "Maaf, ukuran file melebihi 1 Mb<br>";
        $uploadOK = 0;
    }

    // Ceheckpoint 4 (Format file)
    // memeriksa ekstensi file termasuk dalam 'whitelist' yang dijalankan
    if (
        $imageFileType != "jpg" && $imageFileType != "png" &&
        $imageFileType != "jpeg" && $imageFileType != "gif"
    ) {
        echo "Maaf, hanya file JPG, JPEG, PNG, & GIF yang dijalankan.<br>";
        $uploadOK = 0;
    }

    // Jika status uploadOK = 1 pindahkan file ke direktori tujuan
    if ($uploadOK == 1) {
        // pindahkan fisik file ke direktori tujuan / target
        if (move_uploaded_file($fileSementara, $file_path)) {
            echo "File" . basename($_FILES['foto']['name']) . "berhasil diunggah.";
        }
        // salin alamat file (path nama) ke 'galeri' di database: 'db5d'
        require_once __DIR__ . "/phpdb/koneksi.php";
        // jalankan  kueri
        $sql = "INSERT INTO galeri (namafile) VALUES ('$file_path')";
        // cek apakah kueri berhasil atau tidak
        if (mysqli_query($conn, $sql)) {
            echo "Data baru berhasil ditambahkan";
        } else {
            echo "Kesalahan dalam menambahkan data: " . mysqli_error($conn);
        }
        // tutup koneksi ke db
        mysqli_close($conn);
            }
}
?>