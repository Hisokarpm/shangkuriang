<?php
$host     = "localhost";
$user     = "root";
$password = "";          // Default Laragon biasanya kosong ("")
$database = "pengaduan"; // Sesuaikan dengan nama database Anda di phpMyAdmin

// Membuka koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Gagal terhubung ke database: " . mysqli_connect_error());
}
?>