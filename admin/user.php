<?php
// ============================================================
// ADMIN/USER.PHP - Kelola User
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'user';
$pesan = "";

if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $hapus = mysqli_query($koneksi, "DELETE FROM users WHERE id = $id_hapus");

    if ($hapus && mysqli_affected_rows($koneksi) > 0) {
        $pesan = "sukses:User berhasil dihapus!";
    } else {
        $pesan = "error:Gagal menghapus user.";
    }
}

$q_users    = mysqli_query($koneksi, "SELECT id, email, username FROM users ORDER BY id DESC");
$total_user = $q_users ? mysqli_num_rows($q_users) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola User</h1>
            <p class="page-sub">Lihat dan kelola akun pengguna yang terdaftar</p>
        </div>
        <span class="badge-count"><?= $total_user ?> user terdaftar</span>
    </div>

    <?php if ($pesan): ?>
        <?php list($tipe, $teks) = explode(':', $pesan, 2); ?>
        <div class="alert alert-<?= $tipe === 'sukses' ? 'success' : 'error' ?>"><?= htmlspecialchars($teks) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="card-header">
            <h3>Daftar Semua User</h3>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th width="60">ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($q_users && mysqli_num_rows($q_users) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($q_users)): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <span class="badge-id">#<?= $row['id'] ?></span>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar-placeholder">
                                        <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars($row['username']) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <a href="user.php?hapus=<?= $row['id'] ?>"
                                   class="btn-sm btn-red"
                                   onclick="return confirm('Hapus user <?= htmlspecialchars($row['username'], ENT_QUOTES) ?>?\nAksi ini tidak bisa dibatalkan!')">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-row">Belum ada user yang mendaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </main>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
