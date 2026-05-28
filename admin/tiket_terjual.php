<?php
// ============================================================
// ADMIN/TIKET_TERJUAL.PHP - Halaman Statistik Penjualan Tiket
// ============================================================

require_once '_auth.php';
require_once '../koneksi.php';

$halaman_aktif = 'tiket_terjual';

// ===== STATISTIK =====
// Total Ticket Terjual (SEMUA booking, termasuk pending/cancelled)
$q_total_ticket = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah_tiket), 0) AS total FROM bookings");
$total_ticket = 0;
if ($q_total_ticket) {
    $row = mysqli_fetch_assoc($q_total_ticket);
    $total_ticket = $row['total'];
}

// Total Pendapatan (HANYA yang status = 'paid' atau 'used')
$q_total_pendapatan = mysqli_query($koneksi, "SELECT COALESCE(SUM(total_harga), 0) AS total FROM bookings WHERE status IN ('paid', 'used')");
$total_pendapatan = 0;
if ($q_total_pendapatan) {
    $row = mysqli_fetch_assoc($q_total_pendapatan);
    $total_pendapatan = $row['total'];
}

// Total Pembeli (unique user_id yang sudah bayar = 'paid' atau 'used')
$q_total_pembeli = mysqli_query($koneksi, "SELECT COUNT(DISTINCT user_id) AS total FROM bookings WHERE status IN ('paid', 'used')");
$total_pembeli = 0;
if ($q_total_pembeli) {
    $row = mysqli_fetch_assoc($q_total_pembeli);
    $total_pembeli = $row['total'];
}

// ===== FILTER =====
$filter_provinsi = isset($_GET['provinsi']) ? (int)$_GET['provinsi'] : 0;
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build WHERE clause
$where = [];
if ($filter_provinsi > 0) {
    $where[] = "d.provinsi_id = $filter_provinsi";
}
if (!empty($filter_status)) {
    $status_aman = mysqli_real_escape_string($koneksi, $filter_status);
    $where[] = "b.status = '$status_aman'";
}
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Query daftar tiket terjual
$sql = "SELECT b.id, b.jumlah_tiket, b.total_harga, b.status, b.nama_pemesan, b.email_pemesan, 
               d.nama_destinasi, p.nama_provinsi, u.username
        FROM bookings b
        JOIN destinasi d ON b.destinasi_id = d.id
        LEFT JOIN provinsi p ON d.provinsi_id = p.id
        LEFT JOIN users u ON b.user_id = u.id
        $where_sql
        ORDER BY b.created_at DESC";

$q_tiket = mysqli_query($koneksi, $sql);
$jumlah_tiket = $q_tiket ? mysqli_num_rows($q_tiket) : 0;

// Query provinsi untuk filter dropdown
$q_provinsi = mysqli_query($koneksi, "SELECT id, nama_provinsi FROM provinsi ORDER BY nama_provinsi ASC");

// Format kode tiket dari ID
function formatKodeTiket($id) {
    return 'TKT' . str_pad($id, 3, '0', STR_PAD_LEFT);
}

// Format status label
function formatStatus($status) {
    $labels = [
        'paid' => 'Lunas',
        'pending' => 'Pending',
    ]; 
    return $labels[$status] ?? ucfirst($status);
}

function statusClass($status) {
    $classes = [
        'paid' => 'status-lunas',
        'pending' => 'status-pending',
    ];
    return $classes[$status] ?? 'status-pending';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Terjual - Admin Nusantara Journey</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/tiket_terjual.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="admin-body">
    <?php require_once '_sidebar.php'; ?>

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Ticket Terjual</h1>
                <p class="page-sub">Lihat dan kelola semua ticket destinasi yang sudah terjual</p>
            </div>
        </div>

        <!-- STATISTIK CARDS -->
        <div class="stats-grid tiket-stats">
            <div class="stat-card tiket-stat-card">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-card-label">Total Ticket</div>
                        <div class="stat-card-number"><?= number_format($total_ticket, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>

            <div class="stat-card tiket-stat-card">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-card-label">Total Pendapatan</div>
                        <div class="stat-card-number">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>

            <div class="stat-card tiket-stat-card">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <div class="stat-card-label">Total Pembeli</div>
                        <div class="stat-card-number"><?= number_format($total_pembeli, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar tiket-filter">
            <form method="GET" action="tiket_terjual.php" class="filter-form">
                <span class="filter-label">Filter:</span>
                <select name="provinsi" onchange="this.form.submit()">
                    <option value="0">Semua Provinsi</option>
                    <?php if ($q_provinsi): while ($prov = mysqli_fetch_assoc($q_provinsi)): ?>
                        <option value="<?= $prov['id'] ?>" <?= $filter_provinsi == $prov['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prov['nama_provinsi']) ?>
                        </option>
                    <?php endwhile; mysqli_data_seek($q_provinsi, 0); endif; ?>
                </select>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="paid" <?= $filter_status === 'paid' ? 'selected' : '' ?>>Lunas</option>
                    <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
                <?php if ($filter_provinsi > 0 || !empty($filter_status)): ?>
                    <a href="tiket_terjual.php" class="btn-sm btn-secondary">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABEL DAFTAR TIKET -->
        <div class="admin-card tiket-card">
            <div class="card-header tiket-card-header">
                <div>
                    <h3>Daftar Ticket Terjual</h3>
                    <span class="tiket-count"><?= $jumlah_tiket ?> ticket</span>
                </div>
            </div>

            <div class="tiket-table-wrap">
                <table class="admin-table tiket-table">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>PEMBELI</th>
                            <th>DESTINASI</th>
                            <th>PROVINSI</th>
                            <th width="80">JUMLAH</th>
                            <th width="140">TOTAL HARGA</th>
                            <th width="100">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($q_tiket && mysqli_num_rows($q_tiket) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($q_tiket)): ?>
                                <tr>
                                    <td>
                                        <span class="tiket-id"><?= formatKodeTiket($row['id']) ?></span>
                                    </td>
                                    <td>
                                        <div class="pembeli-info">
                                            <strong class="pembeli-nama"><?= htmlspecialchars($row['nama_pemesan'] ?? $row['username'] ?? '-') ?></strong>
                                            <span class="pembeli-email"><?= htmlspecialchars($row['email_pemesan'] ?? '-') ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_destinasi']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_provinsi'] ?? '-') ?></td>
                                    <td class="text-center"><?= $row['jumlah_tiket'] ?></td>
                                    <td>
                                        <span class="harga-tiket">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= statusClass($row['status']) ?>">
                                            <?= formatStatus($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-row">
                                    <div class="empty-tiket">
                                        <i class="fa-solid fa-ticket" style="font-size: 2rem; color: #ccc; margin-bottom: 10px; display: block;"></i>
                                        Belum ada ticket terjual.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>