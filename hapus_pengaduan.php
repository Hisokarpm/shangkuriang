<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'teknisi'], true)) {
    header('Location: login.php');
    exit();
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = 'ID pengaduan tidak valid.';
    header('Location: dashboard.php');
    exit();
}

$stmt = mysqli_prepare($conn, "DELETE FROM pengaduan WHERE id = ? AND status = 'Selesai'");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) === 1) {
    $_SESSION['success'] = 'Pengaduan yang sudah selesai berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Pengaduan hanya dapat dihapus jika statusnya Selesai.';
}

mysqli_stmt_close($stmt);
header('Location: dashboard.php');
exit();
?>
