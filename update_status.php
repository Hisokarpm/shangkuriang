<?php
// Tambahkan 3 baris ini paling atas untuk cek error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'koneksi.php';

// Validasi akses untuk Admin dan Teknisi
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'teknisi'], true)) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $status_baru = $_POST['status'] ?? '';
    $status_valid = ['Pending', 'Diproses', 'Selesai'];

    if ($id <= 0 || !in_array($status_baru, $status_valid, true)) {
        $_SESSION['error'] = "Data status tidak valid!";
        header("Location: dashboard.php");
        exit();
    }

    $status_baru = mysqli_real_escape_string($conn, $status_baru);
    $query = "UPDATE pengaduan SET status = '$status_baru' WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status tiket berhasil diperbarui menjadi: " . $status_baru;
    } else {
        $_SESSION['error'] = "Gagal memperbarui status tiket!";
    }

    // Kembali ke halaman dashboard
    header("Location: dashboard.php");
    exit();
}
?>