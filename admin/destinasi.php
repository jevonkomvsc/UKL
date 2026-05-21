<?php
// ============================================================
// ADMIN/DESTINASI.PHP - Kelola Destinasi (dengan relasi Provinsi)
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'destinasi';
$pesan = "";

function cariGambar($nama_file) {
    if (empty($nama_file)) return null;
    $path = '../uploads/' . $nama_file;
    if (file_exists($path)) return $nama_file;
    if (strpos($nama_file, '.') === false) {
        $ekstensi = ['.jpg', '.jpeg', '.png', '.webp', '.gif'];
        foreach ($ekstensi as $ext) {
            $path = '../uploads/' . $nama_file . $ext;
            if (file_exists($path)) return $nama_file . $ext;
        }
    }
    return null;
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $q_gambar = mysqli_query($koneksi, "SELECT gambar FROM destinasi WHERE id = $id_hapus");
    if ($q_gambar && mysqli_num_rows($q_gambar) > 0) {
        $data_gambar = mysqli_fetch_assoc($q_gambar);
        $hapus = mysqli_query($koneksi, "DELETE FROM destinasi WHERE id = $id_hapus");
        if ($hapus) {
            $file_aktual = cariGambar($data_gambar['gambar']);
            if ($file_aktual && file_exists('../uploads/' . $file_aktual)) {
                unlink('../uploads/' . $file_aktual);
            }
            $pesan = "sukses:Destinasi berhasil dihapus!";
        } else {
            $pesan = "error:Gagal menghapus destinasi.";
        }
    }
}

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'tambah_sukses') $pesan = "sukses:Destinasi berhasil ditambahkan!";
    if ($_GET['pesan'] === 'edit_sukses')   $pesan = "sukses:Destinasi berhasil diperbarui!";
}

$filter_provinsi = isset($_GET['provinsi']) ? (int)$_GET['provinsi'] : 0;
$sql = "SELECT d.*, p.nama_provinsi 
        FROM destinasi d 
        LEFT JOIN provinsi p ON d.provinsi_id = p.id";
if ($filter_provinsi > 0) {
    $sql .= " WHERE d.provinsi_id = $filter_provinsi";
}
$sql .= " ORDER BY d.created_at DESC";

$q_semua = mysqli_query($koneksi, $sql);
$q_provinsi = mysqli_query($koneksi, "SELECT id, nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Destinasi - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola Destinasi</h1>
            <p class="page-sub">Tambah, edit, atau hapus destinasi wisata</p>
        </div>
        <a href="destinasi_tambah.php" class="btn-primary">+ Tambah Destinasi</a>
    </div>

    <?php if ($pesan): ?>
        <?php list($tipe, $teks) = explode(':', $pesan, 2); ?>
        <div class="alert alert-<?= $tipe === 'sukses' ? 'success' : 'error' ?>"><?= htmlspecialchars($teks) ?></div>
    <?php endif; ?>

    <!-- Filter Provinsi -->
    <div class="filter-bar">
        <form method="GET" action="destinasi.php" class="filter-form">
            <label>Filter Provinsi:</label>
            <select name="provinsi" onchange="this.form.submit()">
                <option value="0">Semua Provinsi</option>
                <?php if ($q_provinsi): while ($prov = mysqli_fetch_assoc($q_provinsi)): ?>
                    <option value="<?= $prov['id'] ?>" <?= $filter_provinsi == $prov['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prov['nama_provinsi']) ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
            <?php if ($filter_provinsi > 0): ?>
                <a href="destinasi.php" class="btn-sm btn-secondary">Reset Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h3>Daftar Semua Destinasi</h3>
            <span class="badge-count"><?= mysqli_num_rows($q_semua) ?> destinasi</span>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th width="70">Gambar</th>
                    <th>Nama Destinasi</th>
                    <th>Provinsi</th>
                    <th>Harga Tiket</th>
                    <th width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($q_semua && mysqli_num_rows($q_semua) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($q_semua)): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php $file_gambar = cariGambar($row['gambar']); ?>
                                <?php if ($file_gambar): ?>
                                    <img src="../uploads/<?= htmlspecialchars($file_gambar) ?>" class="thumb-img" alt="<?= htmlspecialchars($row['nama_destinasi']) ?>">
                                <?php else: ?>
                                    <div class="thumb-placeholder">Foto</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama_destinasi']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_provinsi'] ?? 'Tanpa Provinsi') ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td class="aksi-col">
                                <a href="destinasi_edit.php?id=<?= $row['id'] ?>" class="btn-sm btn-blue">Edit</a>
                                <a href="destinasi.php?hapus=<?= $row['id'] ?>" class="btn-sm btn-red" onclick="return konfirmasiHapus('<?= htmlspecialchars($row['nama_destinasi'], ENT_QUOTES) ?>')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="empty-row">Belum ada destinasi. <a href="destinasi_tambah.php">Tambah destinasi pertama!</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
    </div>
    <script>
        function konfirmasiHapus(nama) {
            return confirm('Yakin hapus destinasi "' + nama + '"?\nData tidak bisa dikembalikan!');
        }
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>
