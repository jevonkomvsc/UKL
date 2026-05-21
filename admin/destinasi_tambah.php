<?php
// ============================================================
// ADMIN/DESTINASI_TAMBAH.PHP - Form Tambah Destinasi (dengan Provinsi)
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'destinasi';
$error = "";

$q_provinsi = mysqli_query($koneksi, "SELECT id, nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_destinasi']);
    $provinsi_id = (int)$_POST['provinsi_id'];
    $deskripsi = trim($_POST['deskripsi']);
    $harga     = (int)$_POST['harga'];

    if (empty($nama) || $provinsi_id <= 0 || empty($deskripsi) || $harga <= 0) {
        $error = "Semua kolom wajib diisi dengan benar!";
    } else {
        $nama_file_gambar = "";
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
                $nama_file_gambar = time() . '_' . preg_replace('/\s+/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], '../uploads/' . $nama_file_gambar)) {
                    $error = "Gagal mengupload gambar!";
                    $nama_file_gambar = "";
                }
            } else {
                $error = "Format gambar harus JPG, PNG, atau WEBP!";
            }
        }

        if (empty($error)) {
            $nama_aman      = mysqli_real_escape_string($koneksi, $nama);
            $deskripsi_aman = mysqli_real_escape_string($koneksi, $deskripsi);
            $gambar_aman    = mysqli_real_escape_string($koneksi, $nama_file_gambar);
            $gambar_sql     = empty($gambar_aman) ? 'NULL' : "'$gambar_aman'";

            $sql = "INSERT INTO destinasi (provinsi_id, nama_destinasi, deskripsi, harga, gambar)
                    VALUES ($provinsi_id, '$nama_aman', '$deskripsi_aman', $harga, $gambar_sql)";

            if (mysqli_query($koneksi, $sql)) {
                header("Location: destinasi.php?pesan=tambah_sukses");
                exit();
            } else {
                $error = "Gagal menyimpan ke database!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Destinasi - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Destinasi</h1>
            <p class="page-sub">Isi form di bawah untuk menambahkan destinasi baru</p>
        </div>
        <a href="destinasi.php" class="btn-secondary">&larr; Kembali</a>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="admin-card">
        <form method="POST" action="destinasi_tambah.php" enctype="multipart/form-data" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Destinasi <span class="required">*</span></label>
                    <input type="text" name="nama_destinasi" placeholder="Contoh: Raja Ampat" required
                           value="<?= isset($_POST['nama_destinasi']) ? htmlspecialchars($_POST['nama_destinasi']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Provinsi <span class="required">*</span></label>
                    <select name="provinsi_id" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <?php if ($q_provinsi): mysqli_data_seek($q_provinsi, 0); while ($prov = mysqli_fetch_assoc($q_provinsi)): ?>
                            <option value="<?= $prov['id'] ?>" <?= (isset($_POST['provinsi_id']) && $_POST['provinsi_id'] == $prov['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prov['nama_provinsi']) ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Harga Tiket (Rp) <span class="required">*</span></label>
                <input type="number" name="harga" placeholder="Contoh: 150000" min="0" required
                       value="<?= isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : '' ?>">
                <small class="form-hint">Masukkan angka saja, tanpa titik atau koma</small>
            </div>

            <div class="form-group">
                <label>Gambar Destinasi</label>
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="gambar" id="inputGambar" accept="image/*" onchange="previewGambar(this)">
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <div class="upload-icon-box"></div>
                        <p>Klik atau seret gambar ke sini</p>
                        <small>JPG, PNG, WEBP &mdash; Maks 2MB</small>
                    </div>
                    <img id="previewImg" class="preview-img" style="display:none;" alt="Preview">
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi <span class="required">*</span></label>
                <textarea name="deskripsi" rows="5" placeholder="Jelaskan keindahan dan daya tarik destinasi ini..." required><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Simpan Destinasi</button>
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
