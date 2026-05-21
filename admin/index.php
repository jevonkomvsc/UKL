<?php
// ============================================================
// ADMIN/INDEX.PHP - Dashboard Utama Admin
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'dashboard';

$q_destinasi = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM destinasi");
$total_destinasi = 0;
if ($q_destinasi) {
    $row = mysqli_fetch_assoc($q_destinasi);
    $total_destinasi = $row['total'];
}

$q_user = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users");
$total_user = 0;
if ($q_user) {
    $row = mysqli_fetch_assoc($q_user);
    $total_user = $row['total'];
}

$q_provinsi = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM provinsi");
$total_provinsi = 0;
if ($q_provinsi) {
    $row = mysqli_fetch_assoc($q_provinsi);
    $total_provinsi = $row['total'];
}

$q_terbaru = mysqli_query(
    $koneksi,
    "SELECT d.nama_destinasi, p.nama_provinsi, d.harga, d.created_at
    FROM destinasi d
    LEFT JOIN provinsi p ON d.provinsi_id = p.id
    ORDER BY d.created_at DESC
    LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Nusantara Journey</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once '_sidebar.php'; ?>

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
        </div>
        <div class="header-date">
            <?= date('l, d F Y') ?>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $total_destinasi ?></div>
            <div class="stat-label">Total Destinasi</div>
            <a href="destinasi.php" class="stat-link">Kelola &rarr;</a>
        </div>

        <div class="stat-card">
            <div class="stat-number"><?= $total_provinsi ?></div>
            <div class="stat-label">Total Provinsi</div>
            <a href="provinsi.php" class="stat-link">Kelola &rarr;</a>
        </div>

        <div class="stat-card">
            <div class="stat-number"><?= $total_user ?></div>
            <div class="stat-label">Total User Terdaftar</div>
            <a href="user.php" class="stat-link">Lihat &rarr;</a>
        </div>

        <div class="stat-card">
            <div class="stat-number">+</div>
            <div class="stat-label">Tambah Destinasi Baru</div>
            <a href="destinasi_tambah.php" class="stat-link">Tambah &rarr;</a>
        </div>
    </div>

    <!-- TABEL DESTINASI TERBARU -->
    <div class="admin-card">
        <div class="card-header">
            <h3>Destinasi Terbaru</h3>
            <a href="destinasi.php" class="btn-sm btn-green">Lihat Semua</a>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama Destinasi</th>
                    <th>Provinsi</th>
                    <th>Harga Tiket</th>
                    <th>Ditambahkan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($q_terbaru && mysqli_num_rows($q_terbaru) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($q_terbaru)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['nama_destinasi']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_provinsi'] ?? 'Tanpa Provinsi') ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-row">
                            Belum ada destinasi. <a href="destinasi_tambah.php">Tambah sekarang</a>!
                        </td>
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
