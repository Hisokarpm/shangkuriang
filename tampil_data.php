<?php
// Hubungkan ke file koneksi
require_once 'koneksi.php';

// Perintah SQL untuk mengambil semua data dari tabel 'pengaduan'
$sql = "SELECT * FROM pengaduan ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengaduan Jaringan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Daftar Laporan dari Database</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Tiket</th>
                <th>Nama Pelapor</th>
                <th>Lokasi</th>
                <th>Jenis Masalah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            // Cek apakah ada baris data yang ditemukan
            if (mysqli_num_rows($result) > 0) {
                // Looping / Ekstraksi data baris demi baris dari tabel
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['kode_tiket']); ?></td>
                        <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                        <td><?= htmlspecialchars($row['lokasi']); ?></td>
                        <td><?= htmlspecialchars($row['jenis_masalah']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                    </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data pengaduan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>