<?php
// ============================================================
// KONEKSI DATABASE
// File ini digunakan untuk menghubungkan PHP ke MySQL
// Sertakan file ini di setiap halaman yang butuh database
// ============================================================

$host     = "localhost";    // Server database (biasanya localhost di XAMPP)
$dbname   = "nusantara_db"; // Nama database yang akan dibuat
$username = "root";         // Username MySQL default XAMPP
$password = "";             // Password MySQL default XAMPP (kosong)

// Buat koneksi menggunakan mysqli
$koneksi = mysqli_connect($host, $username, $password, $dbname);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset agar karakter Indonesia tampil dengan benar
mysqli_set_charset($koneksi, "utf8");
?>
