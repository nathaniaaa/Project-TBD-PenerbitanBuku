<?php
include 'koneksi.php';

// VIEW sudah dibuat melalui file .sql, oleh karena itu bisa langsung di panggil
$result = $conn->query("SELECT * FROM Buku_Lengkap ORDER BY Judul");
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Data Buku</title>
        <style>
            body { background-color: #e8edf9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: rgb(31, 42, 85); margin: 0; padding: 20px; }
            h1 { font-family: Tahoma, Geneva, sans-serif; text-align: center; font-size: 50px; }
            table { width: 100%; border-collapse: collapse; background-color: rgba(244, 247, 255, 0.69); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); border-radius: 12px; overflow: hidden; }
            td, th { padding: 12px 10px; text-align: left; border-bottom: 1px solid #ccd8ff; font-size: 14px; }
            th { background-color:rgba(19, 32, 70, 0.89); color:rgb(255, 255, 255); font-size: 16px; text-align: left; padding: 12px 10px; }
            tr:hover { background-color:rgb(255, 255, 255); }
            .btn { padding: 6px 10px; border-radius: 6px; border: none; font-weight: bold; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; }
            .btn-tambah { background-color: rgb(83, 153, 218); color: white; margin-bottom: 15px; padding: 10px 18px; font-size: 18px; float: right; }
            .btn-edit { background-color:rgb(163, 199, 197); color:rgb(0, 14, 90); }
            .btn-hapus { background-color:rgb(253, 104, 104); color: #fff; }
            .btn:hover { opacity: 0.8; }
        </style>
    </head>
    <body>
        <h1>Daftar Buku Penerbit X</h1>
        <a href="tambah.php" class="btn btn-tambah">+ Tambah Buku</a>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ISBN</th><th>Judul</th><th>Kategori</th><th>Tahun</th><th>Harga</th><th>Status</th><th>Penulis</th><th>Editor</th><th>Distributor</th><th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row["ISBN"]?></td>
                <td><?= htmlspecialchars($row["Judul"]) ?></td>
                <td><?= htmlspecialchars($row["Kategori"]) ?></td>
                <td><?= $row["TahunTerbit"] ?></td>
                <td><?= 'Rp ' . number_format($row["Harga"], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($row["StatusPublikasi"]) ?></td>
                <td><?= htmlspecialchars($row["Penulis"]) ?></td>
                <td><?= htmlspecialchars($row["Editor"]) ?></td>
                <td><?= htmlspecialchars($row["Distributor"]) ?></td>
                <td>
                    <a href="edit.php?ISBN=<?= $row["ISBN"] ?>" class="btn btn-edit">Edit</a> 
                    <a href="hapus.php?ISBN=<?= $row["ISBN"] ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </body>
</html>