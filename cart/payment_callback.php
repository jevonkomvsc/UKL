<?php
// ============================================================
// PAYMENT_CALLBACK.PHP - Callback Pembayaran (FIXED)
// ============================================================

session_start();
require_once '../koneksi.php';

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if ($booking_id <= 0 || empty($status)) {
    header("Location: tiket_saya.php");
    exit();
}

// Update status booking
if ($status === 'settlement' || $status === 'capture') {
    mysqli_query($koneksi, "UPDATE bookings SET status = 'paid' WHERE id = $booking_id");

    // Simpan data payment
    $order_aman = mysqli_real_escape_string($koneksi, $order_id);
    $q_booking = mysqli_query($koneksi, "SELECT total_harga FROM bookings WHERE id = $booking_id");
    $booking = mysqli_fetch_assoc($q_booking);
    $total = $booking['total_harga'] ?? 0;

    // Cek apakah tabel payments ada
    $cek_payments = mysqli_query($koneksi, "SHOW TABLES LIKE 'payments'");
    if (mysqli_num_rows($cek_payments) > 0) {
        $sql = "INSERT INTO payments (booking_id, metode_pembayaran, order_id, gross_amount, status, payment_type) 
                VALUES ($booking_id, 'Midtrans', '$order_aman', $total, 'settlement', 'snap')";
        mysqli_query($koneksi, $sql);
    }

    // Generate e-ticket
    $kode_tiket = 'TIX-' . strtoupper(substr(uniqid(), -8));

    // Cek apakah tabel e_tickets ada
    $cek_tickets = mysqli_query($koneksi, "SHOW TABLES LIKE 'e_tickets'");
    if (mysqli_num_rows($cek_tickets) > 0) {
        mysqli_query($koneksi, "INSERT INTO e_tickets (booking_id, kode_tiket) VALUES ($booking_id, '$kode_tiket')");
    }

    // Log
    $cek_logs = mysqli_query($koneksi, "SHOW TABLES LIKE 'booking_logs'");
    if (mysqli_num_rows($cek_logs) > 0) {
        mysqli_query($koneksi, "INSERT INTO booking_logs (booking_id, aksi, keterangan) VALUES ($booking_id, 'payment_success', 'Pembayaran berhasil')");
    }

    header("Location: tiket_saya.php?sukses=1");
    exit();
} else {
    mysqli_query($koneksi, "UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id");

    $cek_logs = mysqli_query($koneksi, "SHOW TABLES LIKE 'booking_logs'");
    if (mysqli_num_rows($cek_logs) > 0) {
        mysqli_query($koneksi, "INSERT INTO booking_logs (booking_id, aksi, keterangan) VALUES ($booking_id, 'payment_failed', 'Pembayaran gagal/dibatalkan')");
    }

    header("Location: tiket_saya.php?gagal=1");
    exit();
}