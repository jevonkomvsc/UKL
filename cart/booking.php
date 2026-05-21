<?php
// ============================================================
// BOOKING.PHP - Form Booking Destinasi (FIXED v2)
// ============================================================

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../koneksi.php';

$destinasi_id = isset($_GET['destinasi_id']) ? (int)$_GET['destinasi_id'] : 0;
if ($destinasi_id <= 0) { header("Location: dashboard.php"); exit(); }

$q = mysqli_query($koneksi, "SELECT d.*, p.nama_provinsi FROM destinasi d 
    LEFT JOIN provinsi p ON d.provinsi_id = p.id 
    WHERE d.id = $destinasi_id");
$destinasi = ($q && mysqli_num_rows($q) > 0) ? mysqli_fetch_assoc($q) : null;
if (!$destinasi) { header("Location: dashboard.php"); exit(); }

$error = "";
$debug_info = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan = trim($_POST['nama_pemesan'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $tanggal = $_POST['tanggal_kunjungan'] ?? '';
    $jumlah = (int)($_POST['jumlah_tamu'] ?? 1);
    $catatan = trim($_POST['catatan'] ?? '');

    // Validasi input
    if (empty($nama_pemesan)) {
        $error = "Nama lengkap wajib diisi!";
    } elseif (empty($email)) {
        $error = "Email wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (empty($no_hp)) {
        $error = "No. HP wajib diisi!";
    } elseif (empty($tanggal)) {
        $error = "Tanggal kunjungan wajib diisi!";
    } elseif ($jumlah < 1) {
        $error = "Jumlah tamu minimal 1!";
    } elseif ($jumlah > 20) {
        $error = "Jumlah tamu maksimal 20!";
    } else {
        $total = $destinasi['harga'] * $jumlah;

        $nama_aman = mysqli_real_escape_string($koneksi, $nama_pemesan);
        $email_aman = mysqli_real_escape_string($koneksi, $email);
        $no_hp_aman = mysqli_real_escape_string($koneksi, $no_hp);
        $catatan_aman = mysqli_real_escape_string($koneksi, $catatan);
        $tanggal_aman = mysqli_real_escape_string($koneksi, $tanggal);
        $user_id = (int)$_SESSION['user_id'];

        // Cek struktur tabel bookings
        $cek_struktur = mysqli_query($koneksi, "SHOW COLUMNS FROM bookings");
        $kolom_tersedia = [];
        if ($cek_struktur) {
            while ($col = mysqli_fetch_assoc($cek_struktur)) {
                $kolom_tersedia[] = $col['Field'];
            }
        }

        // Build query dinamis berdasarkan kolom yang tersedia
        $kolom_insert = [];
        $nilai_insert = [];

        // Kolom wajib
        $kolom_insert[] = "user_id";
        $nilai_insert[] = $user_id;

        $kolom_insert[] = "destinasi_id";
        $nilai_insert[] = $destinasi_id;

        // Kode booking - hanya jika kolom ada
        if (in_array('kode_booking', $kolom_tersedia)) {
            $kode = 'NJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $kode_aman = mysqli_real_escape_string($koneksi, $kode);
            $kolom_insert[] = "kode_booking";
            $nilai_insert[] = "'$kode_aman'";
        }

        // Email
        if (in_array('email_pemesan', $kolom_tersedia)) {
            $kolom_insert[] = "email_pemesan";
        } elseif (in_array('email', $kolom_tersedia)) {
            $kolom_insert[] = "email";
        }
        $nilai_insert[] = "'$email_aman'";

        // Nama
        if (in_array('nama_pemesan', $kolom_tersedia)) {
            $kolom_insert[] = "nama_pemesan";
        } elseif (in_array('nama', $kolom_tersedia)) {
            $kolom_insert[] = "nama";
        }
        $nilai_insert[] = "'$nama_aman'";

        // No HP
        if (in_array('no_hp', $kolom_tersedia)) {
            $kolom_insert[] = "no_hp";
            $nilai_insert[] = "'$no_hp_aman'";
        }

        // Tanggal
        if (in_array('tanggal_kunjungan', $kolom_tersedia)) {
            $kolom_insert[] = "tanggal_kunjungan";
            $nilai_insert[] = "'$tanggal_aman'";
        }

        // Jumlah tamu/tiket
        if (in_array('jumlah_tiket', $kolom_tersedia)) {
            $kolom_insert[] = "jumlah_tiket";
        } elseif (in_array('jumlah_tamu', $kolom_tersedia)) {
            $kolom_insert[] = "jumlah_tamu";
        }
        $nilai_insert[] = $jumlah;

        // Total harga
        if (in_array('total_harga', $kolom_tersedia)) {
            $kolom_insert[] = "total_harga";
            $nilai_insert[] = $total;
        }

        // Catatan
        if (in_array('catatan', $kolom_tersedia)) {
            $kolom_insert[] = "catatan";
            $nilai_insert[] = "'$catatan_aman'";
        }

        // Status default
        if (in_array('status', $kolom_tersedia)) {
            $kolom_insert[] = "status";
            $nilai_insert[] = "'pending'";
        }

        $sql = "INSERT INTO bookings (" . implode(", ", $kolom_insert) . ") 
                VALUES (" . implode(", ", $nilai_insert) . ")";

        $debug_info = "Query: " . substr($sql, 0, 250) . "...<br>Kolom: " . implode(", ", $kolom_tersedia);

        if (mysqli_query($koneksi, $sql)) {
            $booking_id = mysqli_insert_id($koneksi);
            header("Location: checkout.php?booking_id=" . $booking_id);
            exit();
        } else {
            $mysql_error = mysqli_error($koneksi);
            $error = "Gagal membuat booking. Error: " . htmlspecialchars($mysql_error);

            $log_file = 'booking_errors.log';
            $log_entry = date('Y-m-d H:i:s') . " | User: " . $_SESSION['user_id'] . " | Destinasi: $destinasi_id | Error: $mysql_error\n";
            @file_put_contents($log_file, $log_entry, FILE_APPEND);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking - <?= htmlspecialchars($destinasi['nama_destinasi']) ?></title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="assets/css/booking.css">
    <style>
        .debug-box {
            background: #fff3e0;
            border: 1px solid #ff9800;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 0.8rem;
            color: #e65100;
            font-family: monospace;
            word-break: break-all;
        }
        .debug-box strong { color: #bf360c; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <a href="../index.php" class="nav-brand">
            <img src="../uploads/logo.png" alt="Logo" class="nav-logo-leaf" onerror="this.style.display='none'">
            <span class="nav-brand-name">Nusantara Journey</span>
        </a>
        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../user/dashboard.php">Destination</a>
            <a href="tiket_saya.php">Tiket Saya</a>
        </div>
        <div class="nav-hamburger"><span></span><span></span><span></span></div>
    </nav>

    <section class="booking-section">
        <div class="booking-container">
            <div class="booking-layout">
                <div class="booking-form-wrap">
                    <a href="../user/detail.php?id=<?= $destinasi_id ?>" class="btn-kembali">&larr; Kembali</a>
                    <h1>Booking Destinasi</h1>

                    <?php if ($error): ?>
                        <div class="alert alert-error"><?= $error ?></div>
                        <?php if (!empty($debug_info)): ?>
                            <div class="debug-box">
                                <strong>Debug:</strong><br><?= $debug_info ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form method="POST" action="booking.php?destinasi_id=<?= $destinasi_id ?>" class="booking-form">
                        <div class="form-group">
                            <label>Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama_pemesan" required placeholder="Nama sesuai KTP"
                                   value="<?= isset($_POST['nama_pemesan']) ? htmlspecialchars($_POST['nama_pemesan']) : '' ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" required placeholder="email@contoh.com"
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : $_SESSION['email'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>No. HP <span class="required">*</span></label>
                                <input type="tel" name="no_hp" required placeholder="0812xxxxxxx"
                                       value="<?= isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : '' ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tanggal Kunjungan <span class="required">*</span></label>
                                <input type="date" name="tanggal_kunjungan" required 
                                       min="<?= date('Y-m-d') ?>"
                                       value="<?= isset($_POST['tanggal_kunjungan']) ? htmlspecialchars($_POST['tanggal_kunjungan']) : '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Jumlah Tamu <span class="required">*</span></label>
                                <input type="number" name="jumlah_tamu" min="1" max="20" required value="<?= isset($_POST['jumlah_tamu']) ? htmlspecialchars($_POST['jumlah_tamu']) : '1' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Catatan (Opsional)</label>
                            <textarea name="catatan" rows="3" placeholder="Permintaan khusus..."><?= isset($_POST['catatan']) ? htmlspecialchars($_POST['catatan']) : '' ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit-booking">Lanjut ke Pembayaran</button>
                    </form>
                </div>

                <div class="booking-summary">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="summary-item">
                        <span>Destinasi</span>
                        <strong><?= htmlspecialchars($destinasi['nama_destinasi']) ?></strong>
                    </div>
                    <div class="summary-item">
                        <span>Lokasi</span>
                        <strong><?= htmlspecialchars($destinasi['nama_provinsi'] ?? 'Indonesia') ?></strong>
                    </div>
                    <div class="summary-item">
                        <span>Harga per Tiket</span>
                        <strong>Rp <?= number_format($destinasi['harga'], 0, ',', '.') ?></strong>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-item summary-total">
                        <span>Total (1 orang)</span>
                        <strong id="totalHarga">Rp <?= number_format($destinasi['harga'], 0, ',', '.') ?></strong>
                    </div>
                    <div class="summary-note">
                        <small>* Harga belum termasuk pajak & biaya layanan</small>
                    </div>
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
        document.querySelector('input[name="jumlah_tamu"]').addEventListener('input', function() {
            var harga = <?= $destinasi['harga'] ?>;
            var jumlah = parseInt(this.value) || 1;
            var total = harga * jumlah;
            document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    </script>
</body>
</html>