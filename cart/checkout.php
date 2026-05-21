<?php
// ============================================================
// CHECKOUT.PHP - Halaman Pembayaran dengan Midtrans (FIXED)
// ============================================================

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../koneksi.php';

// Konfigurasi Midtrans (GANTI DENGAN KEY ANDA)
$midtrans_server_key = 'SB-Mid-server-XXXXX'; // Sandbox key
$midtrans_client_key = 'SB-Mid-client-XXXXX';   // Sandbox client key
$is_production = false;

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($booking_id <= 0) { header("Location: tiket_saya.php"); exit(); }

// Query dengan kolom yang sesuai struktur tabel
$q = mysqli_query($koneksi, "SELECT b.*, d.nama_destinasi, d.gambar, p.nama_provinsi 
    FROM bookings b 
    JOIN destinasi d ON b.destinasi_id = d.id 
    LEFT JOIN provinsi p ON d.provinsi_id = p.id 
    WHERE b.id = $booking_id AND b.user_id = {$_SESSION['user_id']}");
$booking = ($q && mysqli_num_rows($q) > 0) ? mysqli_fetch_assoc($q) : null;
if (!$booking) { header("Location: tiket_saya.php"); exit(); }

if ($booking['status'] === 'paid') {
    header("Location: tiket_saya.php?sukses=1");
    exit();
}

// Generate order_id dari ID booking (karena tidak ada kode_booking)
$order_id = 'NJ-' . str_pad($booking['id'], 6, '0', STR_PAD_LEFT);
$gross_amount = $booking['total_harga'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - <?= htmlspecialchars($booking['nama_destinasi']) ?></title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="assets/css/booking.css">
    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $midtrans_client_key ?>"></script>
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
            <a href="tiket_saya.php">Tiket Saya</a>
        </div>
        <div class="nav-hamburger"><span></span><span></span><span></span></div>
    </nav>

    <section class="booking-section">
        <div class="booking-container">
            <div class="checkout-layout">
                <!-- DETAIL BOOKING -->
                <div class="checkout-detail">
                    <a href="booking.php?destinasi_id=<?= $booking['destinasi_id'] ?>" class="btn-kembali">&larr; Kembali</a>
                    <h1>Konfirmasi Pembayaran</h1>

                    <div class="booking-info-card">
                        <div class="booking-info-header">
                            <span class="booking-kode"><?= htmlspecialchars($order_id) ?></span>
                            <span class="booking-status status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                        </div>

                        <div class="booking-destinasi">
                            <?php 
                            $ada_gambar = !empty($booking['gambar']) && file_exists('../uploads/' . $booking['gambar']);
                            if ($ada_gambar):
                            ?>
                                <img src="../uploads/<?= htmlspecialchars($booking['gambar']) ?>" alt="<?= htmlspecialchars($booking['nama_destinasi']) ?>">
                            <?php else: ?>
                                <div class="booking-img-placeholder"></div>
                            <?php endif; ?>
                            <div>
                                <h3><?= htmlspecialchars($booking['nama_destinasi']) ?></h3>
                                <p><?= htmlspecialchars($booking['nama_provinsi'] ?? 'Indonesia') ?></p>
                            </div>
                        </div>

                        <div class="booking-details">
                            <div class="detail-row">
                                <span>Nama Pemesan</span>
                                <strong><?= htmlspecialchars($booking['nama_pemesan']) ?></strong>
                            </div>
                            <div class="detail-row">
                                <span>Email</span>
                                <strong><?= htmlspecialchars($booking['email_pemesan'] ?? $booking['email'] ?? '-') ?></strong>
                            </div>
                            <div class="detail-row">
                                <span>No. HP</span>
                                <strong><?= htmlspecialchars($booking['no_hp']) ?></strong>
                            </div>
                            <div class="detail-row">
                                <span>Tanggal Kunjungan</span>
                                <strong><?= date('d F Y', strtotime($booking['tanggal_kunjungan'])) ?></strong>
                            </div>
                            <div class="detail-row">
                                <span>Jumlah Tamu</span>
                                <strong><?= $booking['jumlah_tiket'] ?? $booking['jumlah_tamu'] ?? 1 ?> orang</strong>
                            </div>
                            <?php if (!empty($booking['catatan'])): ?>
                            <div class="detail-row">
                                <span>Catatan</span>
                                <strong><?= htmlspecialchars($booking['catatan']) ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- PEMBAYARAN -->
                <div class="checkout-payment">
                    <h3>Ringkasan Pembayaran</h3>

                    <div class="payment-summary">
                        <div class="payment-row">
                            <span>Harga Tiket (<?= $booking['jumlah_tiket'] ?? $booking['jumlah_tamu'] ?? 1 ?>x)</span>
                            <span>Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></span>
                        </div>
                        <div class="payment-row">
                            <span>Biaya Layanan</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="payment-divider"></div>
                        <div class="payment-row payment-total">
                            <span>Total Pembayaran</span>
                            <strong>Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></strong>
                        </div>
                    </div>

                    <div class="payment-methods">
                        <h4>Metode Pembayaran</h4>
                        <div class="method-list">
                            <div class="method-item">
                                <span class="method-icon"></span>
                                <span>Transfer Bank (BCA, BNI, Mandiri, BRI)</span>
                            </div>
                            <div class="method-item">
                                <span class="method-icon"></span>
                                <span>E-Wallet (GoPay, ShopeePay, DANA)</span>
                            </div>
                            <div class="method-item">
                                <span class="method-icon"></span>
                                <span>QRIS</span>
                            </div>
                            <div class="method-item">
                                <span class="method-icon"></span>
                                <span>Indomaret / Alfamart</span>
                            </div>
                        </div>
                    </div>

                    <button id="pay-button" class="btn-pay-now">Bayar Sekarang</button>
                    <p class="payment-note">
                        <small>Dengan mengklik "Bayar Sekarang", Anda menyetujui syarat & ketentuan.</small>
                    </p>
                </div>
            </div>
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

    <script>
        // Simulasi pembayaran Midtrans Snap
        document.getElementById('pay-button').addEventListener('click', function() {
            if (confirm('Simulasi pembayaran berhasil?')) {
                window.location.href = 'payment_callback.php?booking_id=<?= $booking_id ?>&status=settlement&order_id=<?= $order_id ?>';
            }
        });
    </script>
</body>
</html>