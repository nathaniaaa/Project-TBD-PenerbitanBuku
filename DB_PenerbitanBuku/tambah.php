<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validasi Angka Negatif
    if ((isset($_POST['TahunTerbit']) && $_POST['TahunTerbit'] < 0) || (isset($_POST['Harga']) && $_POST['Harga'] < 0)) {
        die("Error: Nilai untuk Tahun Terbit dan Harga tidak boleh negatif. Silakan tekan tombol 'Back' pada browser Anda dan perbaiki.");
    }


    // Handle Penulis Baru
    if (!empty($_POST['new_penulis'])) {
        $stmt_new = $conn->prepare("INSERT INTO PENULIS (NamaPenulis) VALUES (?)");
        $stmt_new->bind_param("s", $_POST['new_penulis']);
        $stmt_new->execute();
        // Ambil ID penulis yang baru dibuat dan tambahkan ke array
        $_POST['id_penulis'][] = $conn->insert_id;
    }

    // Handle Editor Baru
    if (!empty($_POST['new_editor'])) {
        $stmt_new = $conn->prepare("INSERT INTO EDITOR (NamaEditor) VALUES (?)");
        $stmt_new->bind_param("s", $_POST['new_editor']);
        $stmt_new->execute();
        $_POST['id_editor'][] = $conn->insert_id;
    }

    // Handle Distributor Baru
    if (!empty($_POST['new_distributor'])) {
        $stmt_new = $conn->prepare("INSERT INTO DISTRIBUTOR (NamaDistributor) VALUES (?)");
        $stmt_new->bind_param("s", $_POST['new_distributor']);
        $stmt_new->execute();
        $_POST['id_distributor'][] = $conn->insert_id;
    }

    // 1. Insert buku baru (hanya data buku saja)
    $stmt = $conn->prepare("INSERT INTO BUKU (ISBN, Judul, Kategori, TahunTerbit, Harga, StatusPublikasi) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiis", $_POST['ISBN'], $_POST['Judul'], $_POST['Kategori'], $_POST['TahunTerbit'], $_POST['Harga'], $_POST['StatusPublikasi']);
    $stmt->execute();
    $isbn = $_POST['ISBN'];

    // 2. Handle Penulis (dari checkbox + dari teks baru)
    if (!empty($_POST['id_penulis']) && is_array($_POST['id_penulis'])) {
        $stmt_penulis = $conn->prepare("INSERT INTO KONTRIBUSI_PENULIS (ISBN, ID_Penulis) VALUES (?, ?)");
        foreach ($_POST['id_penulis'] as $id_penulis) {
            $stmt_penulis->bind_param("si", $isbn, $id_penulis);
            $stmt_penulis->execute();
        }
    }

    // 3. Handle Editor (dari checkbox + dari teks baru)
    if (!empty($_POST['id_editor']) && is_array($_POST['id_editor'])) {
        $stmt_editor = $conn->prepare("INSERT INTO KONTRIBUSI_EDITOR (ISBN, ID_Editor) VALUES (?, ?)");
        foreach ($_POST['id_editor'] as $id_editor) {
            $stmt_editor->bind_param("si", $isbn, $id_editor);
            $stmt_editor->execute();
        }
    }

    // 4. Handle Distributor (dari checkbox + dari teks baru)
    if (!empty($_POST['id_distributor']) && is_array($_POST['id_distributor'])) {
        $stmt_distributor = $conn->prepare("INSERT INTO KERJA_SAMA_DISTRIBUSI (ISBN, ID_Distributor) VALUES (?, ?)");
        foreach ($_POST['id_distributor'] as $id_distributor) {
            $stmt_distributor->bind_param("si", $isbn, $id_distributor);
            $stmt_distributor->execute();
        }
    }
    
    header("Location: index.php");
    exit;
}

// Ambil data untuk checkbox
$penulis_list = $conn->query("SELECT ID_Penulis, NamaPenulis FROM PENULIS ORDER BY NamaPenulis");
$editor_list = $conn->query("SELECT ID_Editor, NamaEditor FROM EDITOR ORDER BY NamaEditor");
$distributor_list = $conn->query("SELECT ID_Distributor, NamaDistributor FROM DISTRIBUTOR ORDER BY NamaDistributor");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <style>
        body { background-color: #e8edf9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: rgb(31, 42, 85); margin: 0; padding: 20px; }
        h2 { font-family: Tahoma, Geneva, sans-serif; font-size: 40px; text-align:center; }
        form { background-color: rgba(245, 247, 252, 0.8); padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); max-width: 600px; margin: 20px auto; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccd8ff; border-radius: 8px; box-sizing: border-box; background-color: white; }
        button[type="submit"] { background-color: #6e94e6; border: none; padding: 12px 20px; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: background-color 0.2s ease; width: 100%; font-size: 16px; margin-top: 10px; }
        button[type="submit"]:hover { background-color: rgb(31, 42, 85); }
        .back-link { display: block; text-align:center; margin-top: 15px; color: #6e94e6; text-decoration: none; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }
        .checkbox-group { border: 1px solid #ccd8ff; border-radius: 8px; padding: 10px; background-color: #fff; max-height: 150px; overflow-y: auto; margin-bottom: 5px; }
        .checkbox-group label { display: block; margin-bottom: 5px; cursor: pointer; }
        b { display: block; margin-top: 20px; margin-bottom: 8px; }
        
        .toggle-new-btn {
            background: none;
            border: 1px solid #6e94e6;
            color: #6e94e6;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .new-option-container {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Tambah Buku Baru</h2>
    <form method="post">
        <b>ISBN:</b> <input type="text" name="ISBN" required><br>
        <b>Judul:</b> <input type="text" name="Judul" required><br>
        <b>Kategori:</b> <input type="text" name="Kategori"><br>
        <b>Tahun Terbit:</b> <input type="number" name="TahunTerbit" min="0"><br>
        <b>Harga:</b> <input type="number" name="Harga" step="0.01" min="0"><br>
        
        <b>Status Publikasi:</b> 
        <select name="StatusPublikasi" required>
            <option value="">-- Pilih Status --</option>
            <option value="Cetak">Cetak</option>
            <option value="Distribusi">Distribusi</option>
            <option value="Editing">Editing</option>
        </select><br>
        
        <b>Penulis:</b>
        <div class="checkbox-group">
            <?php while($p = $penulis_list->fetch_assoc()): ?>
                <label><input type="checkbox" name="id_penulis[]" value="<?= $p['ID_Penulis'] ?>"> <?= htmlspecialchars($p['NamaPenulis']) ?></label>
            <?php endwhile; ?>
        </div>
        <button type="button" class="toggle-new-btn">+ Tambah Penulis Baru</button>
        <div class="new-option-container" style="display:none;">
            <input type="text" name="new_penulis" placeholder="Ketik nama penulis baru...">
        </div>

        <b>Editor:</b>
        <div class="checkbox-group">
            <?php while($e = $editor_list->fetch_assoc()): ?>
                <label><input type="checkbox" name="id_editor[]" value="<?= $e['ID_Editor'] ?>"> <?= htmlspecialchars($e['NamaEditor']) ?></label>
            <?php endwhile; ?>
        </div>
        <button type="button" class="toggle-new-btn">+ Tambah Editor Baru</button>
        <div class="new-option-container" style="display:none;">
            <input type="text" name="new_editor" placeholder="Ketik nama editor baru...">
        </div>

        <b>Distributor:</b>
        <div class="checkbox-group">
            <?php while($d = $distributor_list->fetch_assoc()): ?>
                <label><input type="checkbox" name="id_distributor[]" value="<?= $d['ID_Distributor'] ?>"> <?= htmlspecialchars($d['NamaDistributor']) ?></label>
            <?php endwhile; ?>
        </div>
        <button type="button" class="toggle-new-btn">+ Tambah Distributor Baru</button>
        <div class="new-option-container" style="display:none;">
            <input type="text" name="new_distributor" placeholder="Ketik nama distributor baru...">
        </div>

        <button type="submit">Simpan Buku</button>
    </form>
    <a href="index.php" class="back-link">← Kembali ke Daftar Buku</a>

    <script>
        document.querySelectorAll('.toggle-new-btn').forEach(button => {
            button.addEventListener('click', function() {
                let newOptionContainer = this.nextElementSibling;
                if (newOptionContainer.style.display === 'none') {
                    newOptionContainer.style.display = 'block';
                    newOptionContainer.querySelector('input').focus();
                } else {
                    newOptionContainer.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>