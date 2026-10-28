
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5d";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tombol tambah data guest
echo "<a href='form_guest.html'>
      <button type='button'>Add My Guest</button>
      </a><br><br>";

// Mengambil data dari tabel MyGuests
$sql = "SELECT * FROM MyGuests";
$result = $conn->query($sql);

// Menampilkan data
if ($result && $result->num_rows > 0) {

    echo "<table border='1' cellpadding='8' cellspacing='0'>";

    echo "<tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Reg Date</th>
            <th>Action</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {

        $id = $row['id'];

        echo "<tr>";

        echo "<td>" . $id . "</td>";
        echo "<td>" . htmlspecialchars($row['firstname']) . " "
                     . htmlspecialchars($row['lastname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . $row['reg_date'] . "</td>";

        echo "<td>
                <a href='form_editguest.php?id=" . $id . "'>
                    <button type='button'>Edit</button>
                </a> |
                <a href='hapusguest.php?id=" . $id . "'
                   onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>
                    <button type='button'>Delete</button>
                </a>
              </td>";

        echo "</tr>";
    }

    echo "</table>";

} else {
    echo "0 results - Data not found";
}

// Menutup koneksi
$conn->close();
?>
