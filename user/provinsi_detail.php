<?php
// ============================================================
// PROVINSI_DETAIL.PHP - Detail Provinsi & Daftar Destinasi
// Sesuai mockup Figma: Hero + Info Card (kiri bawah) + Destinasi List
// ============================================================

session_start();
require_once '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: dashboard.php"); exit(); }

$q_provinsi = mysqli_query($koneksi, "SELECT * FROM provinsi WHERE id = $id");
$provinsi = ($q_provinsi && mysqli_num_rows($q_provinsi) > 0) ? mysqli_fetch_assoc($q_provinsi) : null;
if (!$provinsi) { header("Location: dashboard.php"); exit(); }

$q_destinasi = mysqli_query($koneksi, "SELECT * FROM destinasi WHERE provinsi_id = $id ORDER BY created_at DESC");

$warna_fallback = [
    'linear-gradient(135deg,#006994,#0099cc)',
    'linear-gradient(135deg,#667eea,#764ba2)',
    'linear-gradient(135deg,#ff6b35,#f7b733)',
    'linear-gradient(135deg,#1565c0,#42a5f5)',
    'linear-gradient(135deg,#11998e,#38ef7d)',
    'linear-gradient(135deg,#8e5b30,#d4a257)',
];

$ada_gambar_provinsi = !empty($provinsi['gambar']) && file_exists('../uploads/' . $provinsi['gambar']);

// Hitung stats
$jumlah_destinasi = mysqli_num_rows($q_destinasi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($provinsi['nama_provinsi']) ?> - Nusantara Journey</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/provinsi_detail.css">
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
            <a href="dashboard.php" class="aktif">estination</a>
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

    <!-- HERO PROVINSI -->
    <section class="provinsi-detail-hero">
        <?php if ($ada_gambar_provinsi): ?>
            <img src="../uploads/<?= htmlspecialchars($provinsi['gambar']) ?>" class="provinsi-detail-bg" alt="<?= htmlspecialchars($provinsi['nama_provinsi']) ?>">
        <?php else: ?>
            <div class="provinsi-detail-bg" style="background: linear-gradient(135deg, #1a936f, #2ecc71);"></div>
        <?php endif; ?>
        <div class="provinsi-detail-overlay"></div>

        <!-- HERO CONTENT: Title + Info Card stacked on LEFT BOTTOM -->
        <div class="provinsi-detail-hero-wrapper">
            <div class="provinsi-detail-left">
                <!-- Title area -->
                <div class="provinsi-title-area">
                    <h1><?= htmlspecialchars($provinsi['nama_provinsi']) ?></h1>
                    <p><?= htmlspecialchars($provinsi['deskripsi'] ?? '') ?></p>
                </div>

                <!-- INFO CARD - below title on left bottom (like Figma) -->
                <div class="provinsi-info-card">
                    <div class="info-card-header">
                        <div class="info-card-title">Overview</div>
                    </div>
                    <div class="info-card-body">
                        <div class="info-stats-row">
                            <div class="info-stat-item">
                                <div class="info-stat-label">Island/City</div>
                                <div class="info-stat-value"><?= htmlspecialchars($provinsi['pulau'] ?? 'Indonesia') ?></div>
                            </div>
                            <div class="info-stat-item">
                                <div class="info-stat-label">Populasi</div>
                                <div class="info-stat-value big-number"><?= htmlspecialchars($provinsi['populasi'] ?? '-') ?></div>
                            </div>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Pulau</div>
                                <div class="info-value"><?= htmlspecialchars($provinsi['pulau'] ?? 'Indonesia') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Ibu Kota</div>
                                <div class="info-value"><?= htmlspecialchars($provinsi['ibu_kota'] ?? '-') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Populasi</div>
                                <div class="info-value"><?= htmlspecialchars($provinsi['populasi'] ?? '-') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION DESTINASI -->
    <section class="destinasi-section">
        <div class="section-container">
            <div class="section-header" style="margin-bottom: 40px;">
                <div class="section-header-left">
                    <h2>Destinasi di <?= htmlspecialchars($provinsi['nama_provinsi']) ?></h2>
                    <p>Pilih destinasi impianmu untuk melihat detail dan memesan tiket</p>
                </div>
            </div>

            <?php if ($q_destinasi && mysqli_num_rows($q_destinasi) > 0): ?>
            <div class="dest-grid">
                <?php $i = 0; while ($row = mysqli_fetch_assoc($q_destinasi)):
                    $warna = $warna_fallback[$i % count($warna_fallback)];
                    $ada_gambar = !empty($row['gambar']) && file_exists('../uploads/' . $row['gambar']);
                    $i++;
                ?>
                <div class="dest-card">
                    <div class="dest-card-img">
                        <?php if ($ada_gambar): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_destinasi']) ?>">
                        <?php else: ?>
                            <div class="dest-placeholder-text" style="background:<?= $warna ?>">
                                <?= htmlspecialchars($row['nama_destinasi']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="dest-card-body">
                        <h4><?= htmlspecialchars($row['nama_destinasi']) ?></h4>
                        <p><?= htmlspecialchars(substr($row['deskripsi'], 0, 120)) ?><?= strlen($row['deskripsi']) > 120 ? '...' : '' ?></p>
                        <div class="dest-card-footer">
                            <div class="dest-card-harga">
                                Estimasi Tiket
                                <strong>Rp <?= number_format($row['harga'], 0, ',', '.') ?></strong>
                            </div>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn-dest-detail">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="dest-empty">
                <p>Belum ada destinasi di provinsi ini.</p>
                <small>Admin sedang menyiapkan destinasi terbaik!</small>
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
            document.getElementById('mobileMenu').classList.toggle('active');
            document.getElementById('mobileMenuOverlay').classList.toggle('active');
            document.body.style.overflow = document.getElementById('mobileMenu').classList.contains('active') ? 'hidden' : '';
        }
    </script>
</body>
</html>