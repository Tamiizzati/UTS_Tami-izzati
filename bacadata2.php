
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

// Tombol untuk menambah data guest
echo "<a href='form_guest.html'><button>Add My Guest</button></a><br><br>";

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
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["firstname"] . " " . $row["lastname"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["reg_date"] . "</td>";

        echo "<td>
                <a href='form_editguest.php?id=" . $row["id"] . "'>
                    <button type='button'>Edit</button>
                </a> |
                <a href='hapusguest.php?id=" . $row["id"] . "'
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
