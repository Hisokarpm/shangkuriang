<?php
require_once 'koneksi.php'; // Menggunakan $conn

if (isset($_POST['kirim_pengaduan'])) {
    $nama_pelapor  = mysqli_real_escape_string($conn, $_POST['nama_pelapor']);
    $jenis_masalah = mysqli_real_escape_string($conn, $_POST['jenis_masalah']);
    $kode_tiket    = "TKT-" . rand(100, 999);

    // Proses Upload Foto
    $nama_file = $_FILES['foto_kerusakan']['name'];
    $tmp_name  = $_FILES['foto_kerusakan']['tmp_name'];
    
    if (!empty($nama_file)) {
        // Beri nama unik agar file foto tidak bentrok
        $ekstensi    = pathinfo($nama_file, PATHINFO_EXTENSION);
        $foto_baru   = "foto_" . time() . "_" . rand(100, 999) . "." . $ekstensi;
        $folder_tujuan = "uploads/" . $foto_baru;

        // Buat folder uploads jika belum ada
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Pindahkan foto ke folder uploads
        move_uploaded_file($tmp_name, $folder_tujuan);
    } else {
        $foto_baru = NULL;
    }

    // Insert ke Database
    $query = "INSERT INTO pengaduan (kode_tiket, nama_pelapor, jenis_masalah, foto, status) 
              VALUES ('$kode_tiket', '$nama_pelapor', '$jenis_masalah', '$foto_baru', 'Pending')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Laporan berhasil dikirim! Kode Tiket: $kode_tiket'); window.location='user.php';</script>";
    } else {
        echo "Gagal mengirim pengaduan: " . mysqli_error($conn);
    }
}
?>