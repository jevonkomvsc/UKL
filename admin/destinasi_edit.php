<?php
// ============================================================
// ADMIN/DESTINASI_EDIT.PHP - Form Edit Destinasi (dengan Provinsi)
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'destinasi';
$error = "";

$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
if ($id <= 0) { header("Location: destinasi.php"); exit(); }

$q_data = mysqli_query($koneksi, "SELECT * FROM destinasi WHERE id = $id");
if (!$q_data || mysqli_num_rows($q_data) === 0) { header("Location: destinasi.php"); exit(); }
$destinasi = mysqli_fetch_assoc($q_data);

$q_provinsi = mysqli_query($koneksi, "SELECT id, nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_destinasi']);
    $provinsi_id = (int)$_POST['provinsi_id'];
    $deskripsi = trim($_POST['deskripsi']);
    $harga     = (int)$_POST['harga'];

    if (empty($nama) || $provinsi_id <= 0 || empty($deskripsi) || $harga <= 0) {
        $error = "Semua kolom wajib diisi dengan benar!";
    } else {
        $nama_file_gambar = $destinasi['gambar'];

        if (!empty($_FILES['gambar']['name'])) {
            $file = $_FILES['gambar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (empty($ext)) {
                $mime = mime_content_type($file['tmp_name']);
                $map = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
                $ext = $map[$mime] ?? '';
            }
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                if ($ext === 'jpeg') $ext = 'jpg';
                $nama_baru = time() . '_' . preg_replace('/\s+/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], '../uploads/' . $nama_baru)) {
                    if (!empty($destinasi['gambar']) && file_exists('../uploads/' . $destinasi['gambar'])) {
                        unlink('../uploads/' . $destinasi['gambar']);
                    }
                    $nama_file_gambar = $nama_baru;
                }
            } else {
                $error = "Format gambar harus JPG, PNG, atau WEBP!";
            }
        }

        if (empty($error)) {
            $nama_aman      = mysqli_real_escape_string($koneksi, $nama);
            $deskripsi_aman = mysqli_real_escape_string($koneksi, $deskripsi);
            $gambar_aman    = mysqli_real_escape_string($koneksi, $nama_file_gambar);

            $sql = "UPDATE destinasi SET
                        provinsi_id    = $provinsi_id,
                        nama_destinasi = '$nama_aman',
                        deskripsi      = '$deskripsi_aman',
                        harga          = $harga,
                        gambar         = '$gambar_aman'
                    WHERE id = $id";

            if (mysqli_query($koneksi, $sql)) {
                header("Location: destinasi.php?pesan=edit_sukses");
                exit();
            } else {
                $error = "Gagal menyimpan perubahan!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Destinasi - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Destinasi</h1>
            <p class="page-sub">Mengubah data: <strong><?= htmlspecialchars($destinasi['nama_destinasi']) ?></strong></p>
        </div>
        <a href="destinasi.php" class="btn-secondary">&larr; Kembali</a>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="admin-card">
        <form method="POST" action="destinasi_edit.php" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Nama Destinasi <span class="required">*</span></label>
                    <input type="text" name="nama_destinasi" required value="<?= htmlspecialchars($destinasi['nama_destinasi']) ?>">
                </div>
                <div class="form-group">
                    <label>Provinsi <span class="required">*</span></label>
                    <select name="provinsi_id" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <?php if ($q_provinsi): while ($prov = mysqli_fetch_assoc($q_provinsi)): ?>
                            <option value="<?= $prov['id'] ?>" <?= $destinasi['provinsi_id'] == $prov['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prov['nama_provinsi']) ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Harga Tiket (Rp) <span class="required">*</span></label>
                <input type="number" name="harga" min="0" required value="<?= htmlspecialchars($destinasi['harga']) ?>">
            </div>

            <div class="form-group">
                <label>Gambar Destinasi</label>
                <?php if (!empty($destinasi['gambar']) && file_exists('../uploads/' . $destinasi['gambar'])): ?>
                    <div class="current-image">
                        <small class="form-hint">Gambar saat ini:</small>
                        <img src="../uploads/<?= htmlspecialchars($destinasi['gambar']) ?>" class="current-img-preview" alt="Gambar saat ini">
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

            <div class="form-group">
                <label>Deskripsi <span class="required">*</span></label>
                <textarea name="deskripsi" rows="5" required><?= htmlspecialchars($destinasi['deskripsi']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="destinasi.php" class="btn-secondary">Batal</a>
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
