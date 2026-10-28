<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5d";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// tombol untuk upload gambar baru (sesuaikan nama file form upload-mu)
echo "<a href='form_upload.html'><button type='button'>Upload Gambar Baru</button></a>";
echo "<br><br>";

// Menampilkan data galeri
$sql = "SELECT id, namafile, tglunggah FROM galeri ORDER BY tglunggah DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  // wadah selebar tabel, supaya judul bisa ketengah mengikuti lebar tabel
  echo "<div style='display:inline-block;'>";

  echo "<div style='text-align:center;'><b>My Photo Gue</b></div>";
  echo "<br>";

  echo "<table border='1' cellpadding='8' cellspacing='0'>";

  echo "<tr>
          <th>No</th>
          <th>Nama File</th>
          <th>Tgl Unggah</th>
          <th>Aksi</th>
          <th>Pratinjau</th>
        </tr>";

  $no = 1;
  while ($row = $result->fetch_assoc()) {
    $path = htmlspecialchars($row["namafile"]);
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . $path . "</td>";
    echo "<td>" . $row["tglunggah"] . "</td>";
    echo "<td>
            <a href='form_editgaleri.php?id=" . $row["id"] . "'>
              <button class='btn-edit' type='button'>Edit</button>
            </a>
            <a href='hapusgaleri.php?id=" . $row["id"] . "'
               onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>
              <button class='btn-delete' type='button'>Hapus</button>
            </a>
          </td>";
    echo "<td><a href='" . $path . "' target='_blank'><img src='" . $path . "' width='100' alt='foto'></a></td>";
    echo "</tr>";
  }

  echo "</table>";
  echo "</div>";

} else {
  echo "<b>Daftar Galeri</b><br><br>";
  echo "0 results - Data not found";
}

$conn->close();
?>