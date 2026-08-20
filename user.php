<?php
require_once 'koneksi.php';

$pesan_sukses = "";
$pesan_gagal  = "";

if (isset($_POST['kirim_pengaduan'])) {
    $nama_pelapor  = mysqli_real_escape_string($conn, $_POST['nama_pelapor']);
    $lokasi        = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $jenis_masalah = mysqli_real_escape_string($conn, $_POST['jenis_masalah']);
    $deskripsi     = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kode_tiket    = "TKT-" . rand(1000, 9999);

    $detail_masalah = "[$lokasi] " . $jenis_masalah . " - " . $deskripsi;

    // Proses Upload Foto Kerusakan dari HP
    $foto_baru = NULL;
    if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] == 0) {
        $nama_file   = $_FILES['foto_kerusakan']['name'];
        $tmp_name    = $_FILES['foto_kerusakan']['tmp_name'];
        $ekstensi    = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_ok = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ekstensi, $ekstensi_ok)) {
            $foto_baru = "foto_" . time() . "_" . rand(100, 999) . "." . $ekstensi;
            $folder_tujuan = "uploads/" . $foto_baru;

            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            move_uploaded_file($tmp_name, $folder_tujuan);
        } else {
            $pesan_gagal = "Format foto harus berupa JPG, JPEG, PNG, atau WEBP!";
        }
    }

    if (empty($pesan_gagal)) {
        $query = "INSERT INTO pengaduan (kode_tiket, nama_pelapor, lokasi, jenis_masalah, deskripsi, foto, status) 
              VALUES ('$kode_tiket', '$nama_pelapor', '$lokasi', '$jenis_masalah', '$deskripsi', '$foto_baru', 'Pending')";

        if (mysqli_query($conn, $query)) {
            $pesan_sukses = "Pengaduan berhasil dikirim! Kode Tiket Anda: <strong>#$kode_tiket</strong>";
        } else {
            $pesan_gagal = "Gagal mengirim pengaduan: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- Viewport agar tampilan responsif dan pas di layar HP -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PT. Shangkuriang Telekomunikasi Indonesia - Pengaduan Jaringan</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        
        .navbar-brand-text {
            font-size: 0.95rem;
            line-height: 1.2;
        }

        @media (min-width: 576px) {
            .navbar-brand-text {
                font-size: 1.25rem;
            }
        }

        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 25px 15px;
            border-bottom: 4px solid #38bdf8;
        }

        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .form-control, .form-select {
            padding: 12px 15px;
            font-size: 16px; /* Mencegah iOS/Android zoom-in otomatis saat ngetik */
            border-radius: 10px;
        }

        .btn-mobile {
            padding: 14px;
            font-size: 1rem;
            border-radius: 10px;
        }

        #preview-foto {
            max-height: 220px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-dark bg-dark sticky-top py-2 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-info d-flex align-items-center gap-2 m-0" href="user.php">
                <img src="logo.png" alt="Logo" height="38" class="rounded" onerror="this.style.display='none'">
                <span class="navbar-brand-text text-wrap">PT. Shangkuriang Telekomunikasi Indonesia</span>
            </a>
        </div>
    </nav>

    <!-- Banner Layanan Ringkas -->
    <div class="hero-section text-center mb-4">
        <div class="container">
            <h4 class="fw-bold mb-1">Layanan Pengaduan Jaringan</h4>
            <p class="text-light opacity-75 small mb-0">Laporkan gangguan internet Anda secara cepat & mudah.</p>
        </div>
    </div>

    <!-- Form Input Pengaduan -->
    <div class="container px-3 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">

                <!-- Alert Pesan Sukses / Gagal -->
                <?php if (!empty($pesan_sukses)): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= $pesan_sukses; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pesan_gagal)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $pesan_gagal; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card card-custom p-3 p-md-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-square text-primary me-2"></i>Form Laporan Gangguan</h5>
                    <hr class="mt-0 mb-3">

                    <form action="user.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap Pelapor</label>
                            <input type="text" name="nama_pelapor" class="form-control" placeholder="Contoh: Budi Santoso" required autocomplete="name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Lokasi / Ruangan Gangguan</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung A - R.302" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jenis Masalah Jaringan</label>
                            <select name="jenis_masalah" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Jenis Gangguan --</option>
                                <option value="WiFi/Sinyal">WiFi Lambat / Sinyal Lemah</option>
                                <option value="Internet Lambat">Koneksi Terputus (No Internet)</option>
                                <option value="LPT/Kabel">Kabel LAN Rusak / Putus</option>
                                <option value="Gagal Login WiFi">Tidak Bisa Login WiFi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Deskripsi Kendala</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan secara rinci kendala yang dialami..." required></textarea>
                        </div>

                        <!-- INPUT AMBIL FOTO LANGSUNG KAMERA HP -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Foto Bukti Kerusakan</label>
                            <input type="file" 
                                   name="foto_kerusakan" 
                                   class="form-control" 
                                   accept="image/*" 
                                   capture="environment" 
                                   onchange="previewFoto(event)">
                            <div class="form-text mt-1"><i class="bi bi-camera me-1"></i>Tekan untuk langsung mengambil foto dari kamera HP.</div>
                            
                            <!-- Preview Gambar -->
                            <div class="mt-3 text-center d-none" id="box-preview">
                                <img id="preview-foto" src="#" alt="Preview Bukti Foto" class="img-fluid border p-1 shadow-sm">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="kirim_pengaduan" class="btn btn-primary btn-mobile fw-bold">
                                <i class="bi bi-send me-1"></i> Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Preview Gambar -->
    <script>
        function previewFoto(event) {
            const input = event.target;
            const preview = document.getElementById('preview-foto');
            const boxPreview = document.getElementById('box-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    boxPreview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                boxPreview.classList.add('d-none');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>