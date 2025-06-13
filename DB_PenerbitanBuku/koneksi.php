<?php
$host = "localhost";
$user = "root";
$password = ""; //Kosongkan jika default WAMP
$database = "DB_PenerbitanBuku";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>