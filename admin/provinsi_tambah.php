<?php
// ============================================================
// ADMIN/PROVINSI_TAMBAH.PHP - Form Tambah Provinsi
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'provinsi';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_provinsi']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama)) {
        $error = "Nama provinsi wajib diisi!";
    } else {
        $nama_aman = mysqli_real_escape_string($koneksi, $nama);
        $cek = mysqli_query($koneksi, "SELECT id FROM provinsi WHERE nama_provinsi = '$nama_aman'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Provinsi sudah ada!";
        } else {
            $gambar = "";
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
                    $gambar = time() . '_provinsi_' . preg_replace('/\s+/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], '../uploads/' . $gambar);
                }
            }

            $deskripsi_aman = mysqli_real_escape_string($koneksi, $deskripsi);
            $gambar_sql = empty($gambar) ? 'NULL' : "'$gambar'";
            $sql = "INSERT INTO provinsi (nama_provinsi, gambar, deskripsi) VALUES ('$nama_aman', $gambar_sql, '$deskripsi_aman')";

            if (mysqli_query($koneksi, $sql)) {
                header("Location: provinsi.php?pesan=tambah_sukses");
                exit();
            } else {
                $error = "Gagal menyimpan data!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Provinsi - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Provinsi</h1>
            <p class="page-sub">Tambah provinsi baru ke database</p>
        </div>
        <a href="provinsi.php" class="btn-secondary">&larr; Kembali</a>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="admin-card">
        <form method="POST" action="provinsi_tambah.php" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label>Nama Provinsi <span class="required">*</span></label>
                <input type="text" name="nama_provinsi" placeholder="Contoh: Bali" required
                       value="<?= isset($_POST['nama_provinsi']) ? htmlspecialchars($_POST['nama_provinsi']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat tentang provinsi ini..."><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Provinsi</label>
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="gambar" id="inputGambar" accept="image/*" onchange="previewGambar(this)">
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <div class="upload-icon-box"></div>
                        <p>Klik atau seret gambar ke sini</p>
                        <small>JPG, PNG, WEBP</small>
                    </div>
                    <img id="previewImg" class="preview-img" style="display:none;" alt="Preview">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Provinsi</button>
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
