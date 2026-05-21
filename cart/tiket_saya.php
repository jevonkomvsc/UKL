<?php
// ============================================================
// TIKET_SAYA.PHP - Daftar Tiket User (FIXED)
// ============================================================

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../koneksi.php';

$user_id = $_SESSION['user_id'];

// Cek apakah tabel e_tickets ada
$cek_tickets = mysqli_query($koneksi, "SHOW TABLES LIKE 'e_tickets'");
$ada_e_tickets = mysqli_num_rows($cek_tickets) > 0;

// Query dengan LEFT JOIN ke e_tickets (jika ada)
if ($ada_e_tickets) {
    $q = mysqli_query($koneksi, "SELECT b.*, d.nama_destinasi, d.gambar, p.nama_provinsi, t.kode_tiket, t.status as tiket_status 
        FROM bookings b 
        JOIN destinasi d ON b.destinasi_id = d.id 
        LEFT JOIN provinsi p ON d.provinsi_id = p.id 
        LEFT JOIN e_tickets t ON b.id = t.booking_id 
        WHERE b.user_id = $user_id 
        ORDER BY b.created_at DESC");
} else {
    $q = mysqli_query($koneksi, "SELECT b.*, d.nama_destinasi, d.gambar, p.nama_provinsi 
        FROM bookings b 
        JOIN destinasi d ON b.destinasi_id = d.id 
        LEFT JOIN provinsi p ON d.provinsi_id = p.id 
        WHERE b.user_id = $user_id 
        ORDER BY b.created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Saya - Nusantara Journey</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="assets/css/tiket_saya.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- NAVBAR - KONSISTEN HIJAU -->
    <nav class="main-nav">
        <a href="../index.php" class="nav-brand">
            <img src="../uploads/logo.png" alt="Logo" class="nav-logo-leaf" onerror="this.style.display='none'">
            <span class="nav-brand-name">Nusantara Journey</span>
        </a>
        <div class="nav-links">
            <a href="../user/dashboard.php">Home</a>
            <a href="../user/dashboard.php">Destination</a>
            <a href="tiket_saya.php" class="aktif">Tiket Saya</a>
        </div>
        <div class="nav-hamburger"><span></span><span></span><span></span></div>
    </nav>
    
    <div style="height: 68px;"></div>
    <section class="tiket-section">
        <div class="section-container">
            <div class="section-header" style="margin-bottom: 40px;">
                <div class="section-header-left">
                    <h1>Tiket Saya</h1>
                    <p>Kelola semua pemesanan dan tiket wisatamu</p>
                </div>
            </div>

            <?php if (isset($_GET['sukses'])): ?>
                <div class="alert alert-success">Pembayaran berhasil! Tiketmu sudah aktif.</div>
            <?php endif; ?>
            <?php if (isset($_GET['gagal'])): ?>
                <div class="alert alert-error">Pembayaran gagal atau dibatalkan.</div>
            <?php endif; ?>

            <?php if ($q && mysqli_num_rows($q) > 0): ?>
            <div class="tiket-list">
                <?php while ($row = mysqli_fetch_assoc($q)): 
                    $status_class = $row['status'];
                    $ada_gambar = !empty($row['gambar']) && file_exists('../uploads/' . $row['gambar']);
                ?>
                <div class="tiket-card tiket-<?= $status_class ?>">
                    <div class="tiket-img">
                        <?php if ($ada_gambar): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_destinasi']) ?>">
                        <?php else: ?>
                            <div class="tiket-img-placeholder"></div>
                        <?php endif; ?>
                    </div>
                    <div class="tiket-info">
                        <div class="tiket-header">
                            <h3><?= htmlspecialchars($row['nama_destinasi']) ?></h3>
                            <span class="tiket-status badge-<?= $status_class ?>"><?= ucfirst($row['status']) ?></span>
                        </div>
                        <p class="tiket-lokasi"><i class="fa-solid fa-location-dot" style="color: #e53935; margin-right: 6px;"></i> <?= htmlspecialchars($row['nama_provinsi'] ?? 'Indonesia') ?></p>
                        <div class="tiket-details">
                            <div class="tiket-detail">
                                <span>ID Booking</span>
                                <strong>#<?= $row['id'] ?></strong>
                            </div>
                            <div class="tiket-detail">
                                <span>Tanggal Kunjungan</span>
                                <strong><?= date('d F Y', strtotime($row['tanggal_kunjungan'])) ?></strong>
                            </div>
                            <div class="tiket-detail">
                                <span>Jumlah Tamu</span>
                                <strong><?= $row['jumlah_tiket'] ?? $row['jumlah_tamu'] ?? 1 ?> orang</strong>
                            </div>
                            <div class="tiket-detail">
                                <span>Total</span>
                                <strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong>
                            </div>
                        </div>
                        <?php if ($row['status'] === 'paid'): ?>
                            <?php if ($ada_e_tickets && !empty($row['kode_tiket'])): ?>
                            <div class="tiket-code">
                                <span class="tiket-label">Kode Tiket:</span>
                                <strong class="tiket-kode"><?= htmlspecialchars($row['kode_tiket']) ?></strong>
                                <span class="tiket-status-tiket"><?= ucfirst($row['tiket_status'] ?? 'active') ?></span>
                            </div>
                            <?php else: ?>
                            <div class="tiket-code">
                                <span class="tiket-label">Status:</span>
                                <strong class="tiket-kode" style="color: #2e7d32;">✓ Pembayaran Berhasil</strong>
                            </div>
                            <?php endif; ?>
                        <?php elseif ($row['status'] === 'pending'): ?>
                        <div class="tiket-actions">
                            <a href="checkout.php?booking_id=<?= $row['id'] ?>" class="btn-bayar">Bayar Sekarang</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="tiket-empty">
                <h3>Belum Ada Tiket</h3>
                <p>Kamu belum melakukan pemesanan. Yuk jelajahi destinasi wisata Indonesia!</p>
                <a href="../user/provinsi.php" class="btn-explore-tiket">Jelajahi Destinasi</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FOOTER - Traveloka Style -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-brand">
                    <div class="footer-brand-name">
                        <img src="../uploads/logo.png" alt="Logo" class="footer-brand-logo" onerror="this.style.display='none'">
                        Nusantara Journey
                    </div>
                    <p class="footer-desc">
                        Platform pemesanan tiket wisata terpercaya di Indonesia. 
                        Temukan keindahan Nusantara dari Sabang sampai Merauke.
                    </p>
                    <span class="footer-tagline">Bangga Produk Indonesia 🇮🇩</span>
                    <div class="footer-social">
                        <a href="#" class="footer-social-link" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="footer-social-link" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="footer-social-link" title="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="footer-social-link" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="#" class="footer-social-link" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h4>Tentang Kami</h4>
                    <ul>
                        <li><a href="#">Profil Perusahaan</a></li>
                        <li><a href="#">Karir</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Partner</a></li>
                    </ul>
                </div>

                <!-- Products -->
                <div class="footer-col">
                    <h4>Produk</h4>
                    <ul>
                        <li><a href="../user/provinsi.php">Destinasi Wisata</a></li>
                        <li><a href="../user/dashboard.php">Paket Tour</a></li>
                        <li><a href="#">Promo & Diskon</a></li>
                        <li><a href="#">Gift Voucher</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="footer-col">
                    <h4>Hubungi Kami</h4>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon"><i class="fa-solid fa-envelope"></i></span>
                        <span>hello@nusantarajourney.id</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon"><i class="fa-solid fa-phone"></i></span>
                        <span>+62 812-3456-7890</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <span>Jl. Nusantara No. 123<br>Jakarta, Indonesia</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span class="footer-copyright">&copy; 2025 Nusantara Journey. All rights reserved.</span>
            <div class="footer-links">
                <a href="#">Syarat & Ketentuan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Pusat Bantuan</a>
            </div>
        </div>
    </footer>
</body>
</html>