<?php
// ============================================================
// ADMIN/PROVINSI.PHP - Kelola Daftar Provinsi
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'provinsi';
$pesan = "";

// Proses hapus
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $q = mysqli_query($koneksi, "SELECT gambar FROM provinsi WHERE id = $id_hapus");
    if ($q && mysqli_num_rows($q) > 0) {
        $data = mysqli_fetch_assoc($q);
        if (!empty($data['gambar']) && file_exists('../uploads/' . $data['gambar'])) {
            unlink('../uploads/' . $data['gambar']);
        }
        $hapus = mysqli_query($koneksi, "DELETE FROM provinsi WHERE id = $id_hapus");
        $pesan = $hapus ? "sukses:Provinsi berhasil dihapus!" : "error:Gagal menghapus provinsi.";
    }
}

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'tambah_sukses') $pesan = "sukses:Provinsi berhasil ditambahkan!";
    if ($_GET['pesan'] === 'edit_sukses')   $pesan = "sukses:Provinsi berhasil diperbarui!";
}

$q = mysqli_query($koneksi, "SELECT p.*, COUNT(d.id) as jumlah_destinasi 
    FROM provinsi p 
    LEFT JOIN destinasi d ON p.id = d.provinsi_id 
    GROUP BY p.id 
    ORDER BY p.nama_provinsi ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Provinsi - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola Provinsi</h1>
            <p class="page-sub">Tambah, edit, atau hapus data provinsi</p>
        </div>
        <a href="provinsi_tambah.php" class="btn-primary">+ Tambah Provinsi</a>
    </div>

    <?php if ($pesan): ?>
        <?php list($tipe, $teks) = explode(':', $pesan, 2); ?>
        <div class="alert alert-<?= $tipe === 'sukses' ? 'success' : 'error' ?>"><?= htmlspecialchars($teks) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="card-header">
            <h3>Daftar Semua Provinsi</h3>
            <span class="badge-count"><?= mysqli_num_rows($q) ?> provinsi</span>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th width="80">Gambar</th>
                    <th>Nama Provinsi</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Destinasi</th>
                    <th width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($q && mysqli_num_rows($q) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['gambar']) && file_exists('../uploads/' . $row['gambar'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" class="thumb-img" alt="<?= htmlspecialchars($row['nama_provinsi']) ?>">
                                <?php else: ?>
                                    <div class="thumb-placeholder">🏝️</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama_provinsi']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 80)) ?><?= strlen($row['deskripsi'] ?? '') > 80 ? '...' : '' ?></td>
                            <td class="text-center"><span class="badge-count"><?= $row['jumlah_destinasi'] ?></span></td>
                            <td class="aksi-col">
                                <a href="provinsi_edit.php?id=<?= $row['id'] ?>" class="btn-sm btn-blue">Edit</a>
                                <a href="provinsi.php?hapus=<?= $row['id'] ?>" class="btn-sm btn-red" onclick="return confirm('Yakin hapus provinsi "<?= htmlspecialchars($row['nama_provinsi'], ENT_QUOTES) ?>"?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="empty-row">Belum ada provinsi. <a href="provinsi_tambah.php">Tambah provinsi pertama!</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
