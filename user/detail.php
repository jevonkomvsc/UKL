<?php
// ============================================================
// DETAIL.PHP - Detail Destinasi (RAPIH - Layout seperti provinsi_detail.php)
// CSS: assets/css/detail.css | JS: inline (minimal)
// ============================================================

session_start();
require_once '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: dashboard.php"); exit(); }

// Query destinasi + join provinsi
$q = mysqli_query($koneksi, "SELECT d.*, p.nama_provinsi, p.gambar as gambar_provinsi, p.deskripsi as deskripsi_provinsi 
    FROM destinasi d 
    LEFT JOIN provinsi p ON d.provinsi_id = p.id 
    WHERE d.id = $id");
$destinasi = ($q && mysqli_num_rows($q) > 0) ? mysqli_fetch_assoc($q) : null;

if (!$destinasi) { header("Location: dashboard.php"); exit(); }

// Cek gambar destinasi
$ada_gambar = !empty($destinasi['gambar']) && file_exists('../uploads/' . $destinasi['gambar']);

// Fallback: gambar destinasi → gambar provinsi → gambar default
if ($ada_gambar) {
    $gambar_hero = '../uploads/' . $destinasi['gambar'];
} elseif (!empty($destinasi['gambar_provinsi']) && file_exists('../uploads/' . $destinasi['gambar_provinsi'])) {
    $gambar_hero = '../uploads/' . $destinasi['gambar_provinsi'];
} else {
    $gambar_hero = 'uploads/1778551955_ntt.jpg';
}

// Determine back URL
$back_url = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'provinsi_detail.php') !== false 
    ? $_SERVER['HTTP_REFERER'] 
    : 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($destinasi['nama_destinasi']) ?> - Nusantara Journey</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- MOBILE MENU OVERLAY -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="toggleMobileMenu()"></div>

    <!-- MOBILE MENU SIDEBAR -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <button class="mobile-menu-close" onclick="toggleMobileMenu()">&times;</button>
            <h3>Nusantara Journey</h3>
            <p>Menu Navigasi</p>
        </div>
        <div class="mobile-menu-user">
            <div class="mobile-menu-avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'G', 0, 1)) ?></div>
            <div class="mobile-menu-user-info">
                <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?></strong>
                <span><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
            </div>
        </div>
        <nav class="mobile-menu-nav">
            <?php if (isset($_SESSION["user_id"])): ?>
            <a href="dashboard.php">Home</a>
            <a href="dashboard.php" class="aktif">Destination</a>
            <a href="../cart/tiket_saya.php">Tiket Saya</a>
            <?php else: ?>
            <a href="../index.php">Home</a>
            <a href="provinsi.php" class="aktif">Destination</a>
            <a href="../login.php">Tiket Saya</a>
            <?php endif; ?>
            <div class="mobile-menu-divider"></div>
            <a href="dashboard.php">Profile</a>
        </nav>
        <div class="mobile-menu-footer">
            <a href="../logout.php" class="mobile-menu-btn-logout" onclick="return confirm('Yakin mau logout?')">
                Logout
            </a>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="main-nav">
        <a href="../index.php" class="nav-brand">
            <img src="../uploads/logo.png" alt="Logo" class="nav-logo-leaf" onerror="this.style.display='none'">
            <span class="nav-brand-name">Nusantara Journey</span>
        </a>
        <div class="nav-links">
            <?php if (isset($_SESSION["user_id"])): ?>
            <a href="dashboard.php">Home</a>
            <a href="dashboard.php" class="aktif">Destination</a>
            <a href="../cart/tiket_saya.php">Tiket Saya</a>
            <?php else: ?>
            <a href="../index.php">Home</a>
            <a href="provinsi.php" class="aktif">Destination</a>
            <a href="../login.php">Tiket Saya</a>
            <?php endif; ?>
        </div>
        <div class="nav-hamburger" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- HERO DESTINASI -->
    <section class="detail-hero">
        <img src="<?= htmlspecialchars($gambar_hero) ?>" class="detail-hero-bg" alt="<?= htmlspecialchars($destinasi['nama_destinasi']) ?>"
             onerror="this.style.display='none'; this.parentElement.style.background='linear-gradient(135deg, #1a936f, #2ecc71)';">
        <div class="detail-hero-overlay"></div>

        <div class="detail-hero-wrapper">
            <div class="detail-hero-left">
                <div class="detail-title-area">
                    <a href="<?= htmlspecialchars($back_url) ?>" class="btn-kembali">&larr; Kembali ke Destinasi</a>
                    <h1><?= htmlspecialchars($destinasi['nama_destinasi']) ?></h1>
                    <p><?= htmlspecialchars($destinasi['deskripsi']) ?></p>
                </div>

                <div class="detail-info-card">
                    <div class="info-card-header">
                        <div class="info-card-title">Overview</div>
                    </div>
                    <div class="info-stats-row">
                        <div class="info-stat-item">
                            <div class="info-stat-label">Harga Tiket</div>
                            <div class="info-stat-value big-number">Rp <?= number_format($destinasi['harga'], 0, ',', '.') ?></div>
                        </div>
                        <div class="info-stat-item">
                            <div class="info-stat-label">Lokasi</div>
                            <div class="info-stat-value"><?= htmlspecialchars($destinasi['nama_provinsi'] ?? 'Indonesia') ?></div>
                        </div>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Lokasi</div>
                            <div class="info-value"><?= htmlspecialchars($destinasi['nama_provinsi'] ?? 'Indonesia') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Harga</div>
                            <div class="info-value">Rp <?= number_format($destinasi['harga'], 0, ',', '.') ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Rating</div>
                            <div class="info-value">4.8/5</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KONTEN DESKRIPSI -->
    <section class="detail-content">
        <div class="detail-content-header">
            <h2>Tentang Destinasi</h2>
            <div class="detail-harga-box">
                <div class="detail-harga-label">Harga Tiket</div>
                <div class="detail-harga-value">Rp <?= number_format($destinasi['harga'], 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="detail-deskripsi">
            <?= nl2br(htmlspecialchars($destinasi['deskripsi'])) ?>
        </div>

        <div class="detail-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../cart/booking.php?destinasi_id=<?= $destinasi['id'] ?>" class="btn-beli-tiket">Beli Tiket Sekarang</a>
            <?php else: ?>
                <a href="../login.php?redirect=user/detail.php?id=<?= $destinasi['id'] ?>" class="btn-beli-tiket">Login untuk Booking</a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($back_url) ?>" class="btn-kembali-dash">&larr; Kembali</a>
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
                        <li><a href="provinsi.php">Destinasi Wisata</a></li>
                        <li><a href="dashboard.php">Paket Tour</a></li>
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

    <script>
        function toggleMobileMenu() {
            var menu = document.getElementById('mobileMenu');
            var overlay = document.getElementById('mobileMenuOverlay');
            menu.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
        }
    </script>
</body>
</html>