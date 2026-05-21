<?php
// ============================================================
// ADMIN/PROVINSI_EDIT.PHP - Form Edit Provinsi
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'provinsi';
$error = "";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: provinsi.php"); exit(); }

$q = mysqli_query($koneksi, "SELECT * FROM provinsi WHERE id = $id");
if (!$q || mysqli_num_rows($q) === 0) { header("Location: provinsi.php"); exit(); }
$provinsi = mysqli_fetch_assoc($q);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_provinsi']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama)) {
        $error = "Nama provinsi wajib diisi!";
    } else {
        $nama_aman = mysqli_real_escape_string($koneksi, $nama);
        $deskripsi_aman = mysqli_real_escape_string($koneksi, $deskripsi);
        $gambar = $provinsi['gambar'];

        if (!empty($_FILES['gambar']['name'])) {
            $file = $_FILES['gambar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (empty($ext)) {
                $mime = mime_content_type($file['tmp_name']);
                $map = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                $ext = $map[$mime] ?? '';
            }
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                if ($ext === 'jpeg') $ext = 'jpg';
                $nama_baru = time() . '_provinsi_' . preg_replace('/\s+/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], '../uploads/' . $nama_baru)) {
                    if (!empty($provinsi['gambar']) && file_exists('../uploads/' . $provinsi['gambar'])) {
                        unlink('../uploads/' . $provinsi['gambar']);
                    }
                    $gambar = $nama_baru;
                }
            }
        }

        $gambar_sql = empty($gambar) ? 'NULL' : "'$gambar'";
        $sql = "UPDATE provinsi SET nama_provinsi = '$nama_aman', gambar = $gambar_sql, deskripsi = '$deskripsi_aman' WHERE id = $id";

        if (mysqli_query($koneksi, $sql)) {
            header("Location: provinsi.php?pesan=edit_sukses");
            exit();
        } else {
            $error = "Gagal memperbarui data!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Provinsi - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Provinsi</h1>
            <p class="page-sub">Mengubah data: <strong><?= htmlspecialchars($provinsi['nama_provinsi']) ?></strong></p>
        </div>
        <a href="provinsi.php" class="btn-secondary">&larr; Kembali</a>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="admin-card">
        <form method="POST" action="provinsi_edit.php?id=<?= $id ?>" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label>Nama Provinsi <span class="required">*</span></label>
                <input type="text" name="nama_provinsi" required value="<?= htmlspecialchars($provinsi['nama_provinsi']) ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4"><?= htmlspecialchars($provinsi['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Provinsi</label>
                <?php if (!empty($provinsi['gambar']) && file_exists('../uploads/' . $provinsi['gambar'])): ?>
                    <div class="current-image">
                        <small class="form-hint">Gambar saat ini:</small>
                        <img src="../uploads/<?= htmlspecialchars($provinsi['gambar']) ?>" class="current-img-preview" alt="Gambar saat ini">
                    </div>
                <?php endif; ?>
                <div class="upload-area" style="margin-top: 10px;">
                    <input type="file" name="gambar" id="inputGambar" accept="image/*" onchange="previewGambar(this)">
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <div class="upload-icon-box"></div>
                        <p>Pilih gambar baru (opsional)</p>
                        <small>Kosongkan jika tidak ingin ganti gambar</small>
                    </div>
                    <img id="previewImg" class="preview-img" style="display:none;" alt="Preview baru">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="provinsi.php" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    </main>
    </div>
    <script>
        function previewGambar(input) {
            var placeholder = document.getElementById('uploadPlaceholder');
            var preview = document.getElementById('previewImg');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>
