<?php
include 'koneksi.php';

if (isset($_GET['ISBN'])) {
    $isbn = $_GET['ISBN'];
    
    // Karena terdapat ON DELETE CASCADE, maka cukup hapus data dari tabel BUKU
    // Relasi di tabel KONTRIBUSI_PENULIS, KONTRIBUSI_EDITOR, dan KERJA_SAMA_DISTRIBUSI akan terhapus otomatis
    $stmt = $conn->prepare("DELETE FROM BUKU WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
}

header("Location: index.php");
exit;
?>