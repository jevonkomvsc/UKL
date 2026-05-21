<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../koneksi.php';

$username = $_SESSION['username'];

// Query PROVINCES instead of destinations
$q = mysqli_query($koneksi, "SELECT p.*, COUNT(d.id) as jumlah_destinasi 
    FROM provinsi p 
    LEFT JOIN destinasi d ON p.id = d.provinsi_id 
    GROUP BY p.id 
    ORDER BY p.nama_provinsi ASC");

$warna_fallback = [
    'linear-gradient(135deg,#006994,#0099cc)',
    'linear-gradient(135deg,#667eea,#764ba2)',
    'linear-gradient(135deg,#ff6b35,#f7b733)',
    'linear-gradient(135deg,#1565c0,#42a5f5)',
    'linear-gradient(135deg,#11998e,#38ef7d)',
    'linear-gradient(135deg,#8e5b30,#d4a257)',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Nusantara Journey</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../user/assets/css/dashboard.css">
    
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
            <div class="mobile-menu-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
            <div class="mobile-menu-user-info">
                <strong><?= htmlspecialchars($username) ?></strong>
                <span><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
            </div>
        </div>
        <nav class="mobile-menu-nav">
            <a href="dashboard.php">Home</a>
            <a href="dashboard.php" class="aktif">Destination</a>
            <a href="../cart/tiket_saya.php">Tiket Saya</a>
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
            <a href="dashboard.php">Home</a>
            <a href="dashboard.php" class="aktif">Destination</a>
            <a href="../cart/tiket_saya.php">Tiket Saya</a>
        </div>
        <div class="nav-hamburger" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="dash-hero">
        <img src="../uploads/background.jpg" class="dash-hero-bg" alt="Nusantara Journey"
             onerror="this.style.display='none'">
        <div class="dash-hero-overlay"></div>

        <!-- Search bar atas tengah -->
        <div class="dash-hero-search-wrap">
            <div class="dash-hero-search">
                <span class="dash-hero-search-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2.5" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input type="text" id="inputCari" placeholder="Cari provinsi impianmu...">
                <button class="dash-btn-search" onclick="cariProvinsi()">Search</button>
            </div>
        </div>

        <!-- Konten kiri bawah -->
        <div class="dash-hero-content">
            <div class="dash-hero-badge">Selamat Datang Kembali</div>
            <h1>Halo, <span><?= htmlspecialchars($username) ?></span>!<br>Mau ke Mana Hari Ini?</h1>
            <p>Temukan keindahan Nusantara dari Sabang sampai Merauke — pilih provinsimu di bawah.</p>
        </div>

        <!-- Wave putih bawah -->
        <div class="dash-hero-wave">
            <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path fill="#ffffff"
                    d="M0,60 C200,90 400,20 600,50 C800,80 1000,20 1200,50 C1320,65 1400,55 1440,60 L1440,90 L0,90 Z"/>
            </svg>
        </div>
    </section>

    <!-- FILTER KATEGORI -->
    <div class="dash-kategori" id="provinsi">
        <button class="btn-kategori" onclick="filterKategori(this)">Pulau Jawa</button>
        <button class="btn-kategori" onclick="filterKategori(this)">Pulau Bali</button>
        <button class="btn-kategori" onclick="filterKategori(this)">Pulau Sumatera</button>
        <button class="btn-kategori" onclick="filterKategori(this)">Pulau Kalimantan</button>
        <button class="btn-kategori aktif" onclick="filterKategori(this)">Semua</button>
    </div>

    <!-- SECTION PROVINSI -->
    <section class="dash-destinasi">
        <div class="dash-container">
            <div class="dash-section-header" style="margin-bottom: 40px;">
                <div class="dash-section-header-left">
                    <h2>Provinsi Pilihan</h2>
                    <p>Klik kartu untuk melihat detail lengkap provinsi dan destinasi wisatanya</p>
                </div>
            </div>

            <?php if ($q && mysqli_num_rows($q) > 0): ?>
            <div class="dash-grid" id="gridProvinsi">
                <?php $i = 0; while ($row = mysqli_fetch_assoc($q)):
                    $warna      = $warna_fallback[$i % count($warna_fallback)];
                    $ada_gambar = !empty($row['gambar']) && file_exists('../uploads/' . $row['gambar']);
                    $i++;
                ?>
                <a class="dash-card" href="provinsi_detail.php?id=<?= $row['id'] ?>">
                    <div class="dash-card-img" <?= !$ada_gambar ? 'style="background:' . $warna . '"' : '' ?>>
                        <?php if ($ada_gambar): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>"
                                 alt="<?= htmlspecialchars($row['nama_provinsi']) ?>">
                        <?php else: ?>
                            <div class="dash-card-fallback" style="background:<?= $warna ?>">
                                <span class="dash-card-fallback-text"><?= htmlspecialchars($row['nama_provinsi']) ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="dash-card-badge"><?= $row['jumlah_destinasi'] ?> Destinasi</span>
                    </div>
                    <div class="dash-card-body">
                        <h4><?= htmlspecialchars($row['nama_provinsi']) ?></h4>
                        <p><?= htmlspecialchars($row['deskripsi'] ?? '') ?></p>
                        <div class="dash-card-footer">
                            <div class="dash-card-harga">
                                Jumlah Wisata
                                <strong><?= $row['jumlah_destinasi'] ?> Destinasi</strong>
                            </div>
                            <span class="btn-detail-card">Lihat Detail</span>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>

            <?php else: ?>
            <div class="dash-empty">
                <p>Belum ada provinsi tersedia.</p>
                <small>Admin sedang menyiapkan data terbaik untukmu!</small>
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

        function filterKategori(el) {
            document.querySelectorAll('.btn-kategori').forEach(b => b.classList.remove('aktif'));
            el.classList.add('aktif');
        }

        function cariProvinsi() {
            var kata = document.getElementById('inputCari').value.toLowerCase().trim();
            var kartu = document.querySelectorAll('.dash-card');
            kartu.forEach(function(k) {
                var nama = k.querySelector('h4') ? k.querySelector('h4').textContent.toLowerCase() : '';
                k.style.display = (nama.includes(kata)) ? 'block' : 'none';
            });
            document.getElementById('provinsi').scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('inputCari').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') cariProvinsi();
        });
    </script>

    <script src="../assets/js/script.js"></script>
</body>
</html>