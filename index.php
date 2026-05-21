<?php
// ============================================================
// INDEX.PHP - Halaman Utama (Landing Page) - For Guests
// ============================================================

session_start();

// Redirect logged-in users to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
require_once 'koneksi.php';

$q = mysqli_query($koneksi, "SELECT d.*, p.nama_provinsi FROM destinasi d LEFT JOIN provinsi p ON d.provinsi_id = p.id ORDER BY d.created_at DESC");

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
    <title>Nusantara Journey — Jelajahi Keindahan Indonesia</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="main-nav">
        <a href="index.php" class="nav-brand">
            <img src="uploads/logo.png" alt="Logo" class="nav-logo-leaf"
                 onerror="this.onerror=null; this.src='uploads/logo.png'">
            <span class="nav-brand-name">Nusantara Journey</span>
        </a>

        <!-- Menu tengah -->
        <div class="nav-links">
            <a href="index.php" class="aktif">Home</a>
            <a href="user/provinsi.php">Destination</a>
            <a href="user/provinsi.php">Package</a>
        </div>

        <!-- Login/Register buttons for guests -->
        <div style="display: flex; gap: 12px; align-items: center;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user/dashboard.php" style="color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Dashboard</a>
                <a href="logout.php" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.85rem;">Logout</a>
            <?php else: ?>
                <a href="login.php" style="color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Login</a>
                <a href="register.php" style="background: #f97316; color: white; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.85rem;">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">

        <!-- Foto background -->
        <img src="uploads/background.jpg" class="hero-bg" alt="Nusantara Journey"
             onerror="this.style.display='none'">

        <div class="hero-overlay"></div>

        <!-- Search bar di atas tengah -->
        <div class="hero-search-wrap">
            <div class="hero-search">
                <span class="hero-search-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2.5" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input type="text" id="inputCari" placeholder="Cari destinasi impianmu...">
                <button class="btn-search" onclick="cariDestinasi()">Search</button>
            </div>
        </div>

        <!-- Judul & teks di kiri bawah -->
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="underline-accent">Nusantara Journey</span>
            </h1>
            <p class="hero-desc">
                Temukan keindahan tersembunyi di<br>setiap sudut Indonesia
            </p>
            <a href="user/provinsi.php" class="btn-explore">Jelajahi Destinasi</a>
        </div>

        <!-- Wave putih besar -->
        <div class="hero-wave">
            <svg viewBox="0 0 1440 90" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path fill="#ffffff"
                    d="M0,60 C200,90 400,20 600,50 C800,80 1000,20 1200,50 C1320,65 1400,55 1440,60 L1440,90 L0,90 Z"/>
            </svg>
        </div>
    </section>

    <!-- FILTER KATEGORI -->
    <div class="kategori-section" id="destinasi">
        <button class="btn-kategori" onclick="filterKategori(this)">Gunung</button>
        <button class="btn-kategori" onclick="filterKategori(this)">Pantai</button>
        <button class="btn-kategori" onclick="filterKategori(this)">Kuliner</button>
        <button class="btn-kategori" onclick="filterKategori(this)">Budaya</button>
        <button class="btn-kategori aktif" onclick="filterKategori(this)">Lainnya</button>
    </div>

    <!-- SECTION DESTINASI -->
    <section class="destinasi-section">
        <div class="section-container">
            <div class="section-header">
                <div class="section-header-left">
                    <h2>Destinasi Unggulan</h2>
                    <p>Jelajahi destinasi wisata terbaik Indonesia</p>
                </div>
                <div class="section-header-right">
                    <a href="user/provinsi.php">Lihat Semua &rarr;</a>
                </div>
            </div>

            <?php if ($q && mysqli_num_rows($q) > 0): ?>
            <div class="dest-grid" id="gridDestinasi">
                <?php $i = 0; while ($row = mysqli_fetch_assoc($q)):
                    $warna = $warna_fallback[$i % count($warna_fallback)];
                    $ada_gambar = !empty($row['gambar']) && file_exists('uploads/' . $row['gambar']);
                    $i++;
                ?>
                <a class="dest-card" href="user/detail.php?id=<?= $row['id'] ?>">
                    <div class="dest-card-img">
                        <?php if ($ada_gambar): ?>
                            <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>"
                                alt="<?= htmlspecialchars($row['nama_destinasi']) ?>">
                        <?php else: ?>
                            <div class="dest-card-img-placeholder">
                                <div class="dest-placeholder-text" style="background:<?= $warna ?>">
                                    <?= htmlspecialchars($row['nama_destinasi']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <span class="dest-card-badge"><?= htmlspecialchars($row['nama_provinsi'] ?? $row['provinsi'] ?? 'Indonesia') ?></span>
                    </div>
                    <div class="dest-card-body">
                        <h4><?= htmlspecialchars($row['nama_destinasi']) ?></h4>
                        <p><?= htmlspecialchars($row['deskripsi']) ?></p>
                        <div class="dest-card-footer">
                            <div class="dest-card-harga">
                                Estimasi Tiket
                                <strong>Rp <?= number_format($row['harga'], 0, ',', '.') ?></strong>
                            </div>
                            <span class="btn-dest-detail">Lihat Detail</span>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="dest-empty">
                <p>Belum ada destinasi tersedia.</p>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- FOOTER -->
    <!-- FOOTER - Traveloka Style -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-brand">
                    <div class="footer-brand-name">
                        <img src="uploads/logo.png" alt="Logo" class="footer-brand-logo" onerror="this.style.display='none'">
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
                        <li><a href="user/provinsi.php">Destinasi Wisata</a></li>
                        <li><a href="user/dashboard.php">Paket Tour</a></li>
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
        function filterKategori(el) {
            document.querySelectorAll('.btn-kategori').forEach(b => b.classList.remove('aktif'));
            el.classList.add('aktif');
        }

        function cariDestinasi() {
            var kata = document.getElementById('inputCari').value.toLowerCase().trim();
            var kartu = document.querySelectorAll('.dest-card');
            kartu.forEach(function(k) {
                var nama = k.querySelector('h4') ? k.querySelector('h4').textContent.toLowerCase() : '';
                var prov = k.querySelector('.dest-card-badge') ? k.querySelector('.dest-card-badge').textContent.toLowerCase() : '';
                k.style.display = (nama.includes(kata) || prov.includes(kata)) ? 'block' : 'none';
            });
            document.getElementById('destinasi').scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('inputCari').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') cariDestinasi();
        });
    </script>
</body>
</html>