<?php
// ============================================================
// PROVINSI.PHP - Halaman Daftar Provinsi (User - Guest View)
// ============================================================

session_start();
require_once '../koneksi.php';

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
    <title>Provinsi - Nusantara Journey</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/provinsi.css">
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
            <a href="../index.php">Home</a>
            <a href="provinsi.php" class="aktif">Destination</a>
            <a href="provinsi.php">Package</a>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../cart/tiket_saya.php">Tiket Saya</a>
            <?php endif; ?>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" style="color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Dashboard</a>
                <a href="../logout.php" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.85rem;">Logout</a>
            <?php else: ?>
                <a href="../login.php" style="color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Login</a>
                <a href="../register.php" style="background: #f97316; color: white; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.85rem;">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- HERO -->
    <section class="provinsi-hero">
        <div class="provinsi-hero-overlay"></div>
        <div class="provinsi-hero-content">
            <h1>Jelajahi Provinsi di Indonesia</h1>
            <p>Temukan keindahan destinasi wisata dari setiap sudut Nusantara</p>
        </div>
    </section>

    <!-- SECTION PROVINSI -->
    <section class="provinsi-section">
        <div class="section-container">
            <div class="section-header" style="margin-bottom: 40px;">
                <div class="section-header-left">
                    <h2>Daftar Provinsi</h2>
                    <p>Pilih provinsi untuk melihat destinasi wisatanya</p>
                </div>
            </div>

            <?php if ($q && mysqli_num_rows($q) > 0): ?>
            <div class="provinsi-grid">
                <?php $i = 0; while ($row = mysqli_fetch_assoc($q)): 
                    $warna = $warna_fallback[$i % count($warna_fallback)];
                    $ada_gambar = !empty($row['gambar']) && file_exists('../uploads/' . $row['gambar']);
                    $i++;
                ?>
                <a class="provinsi-card" href="provinsi_detail.php?id=<?= $row['id'] ?>">
                    <div class="provinsi-card-img">
                        <?php if ($ada_gambar): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_provinsi']) ?>">
                        <?php else: ?>
                            <div class="provinsi-card-fallback" style="background:<?= $warna ?>">
                                <span><?= htmlspecialchars($row['nama_provinsi']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="provinsi-card-overlay">
                            <span class="provinsi-card-count"><?= $row['jumlah_destinasi'] ?> Destinasi</span>
                        </div>
                    </div>
                    <div class="provinsi-card-body">
                        <h3><?= htmlspecialchars($row['nama_provinsi']) ?></h3>
                        <p><?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 100)) ?><?= strlen($row['deskripsi'] ?? '') > 100 ? '...' : '' ?></p>
                        <span class="btn-provinsi-detail">Lihat Destinasi &rarr;</span>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="provinsi-empty">
                <p>Belum ada provinsi tersedia.</p>
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
</body>
</html>