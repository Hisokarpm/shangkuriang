<?php
// Buka koneksi
require_once 'koneksi.php';

// Cek apakah $conn berhasil terhubung
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Hitung rekap statistik dari DB menggunakan $conn
$total   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM pengaduan"))['jml'] ?? 0;
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM pengaduan WHERE status='Pending'"))['jml'] ?? 0;
$proses  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM pengaduan WHERE status='Proses'"))['jml'] ?? 0;
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM pengaduan WHERE status='Selesai'"))['jml'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar-custom {
            width: 280px;
            min-height: 100vh;
            background-color: #1a2332;
            color: #8a99ad;
        }

        .brand-title {
            color: #38bdf8;
            font-weight: 700;
            font-size: 1.15rem;
            line-height: 1.3;
        }

        .brand-section {
            padding: 20px 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-nav {
            padding-top: 10px;
        }

        .sidebar-nav .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #38bdf8;
            background-color: #0f172a;
            border-left-color: #38bdf8;
        }

        .sidebar-nav .nav-link-logout {
            color: #ef4444;
        }

        .sidebar-nav .nav-link-logout:hover {
            color: #f87171;
            background-color: rgba(239, 68, 68, 0.05);
        }

        .content-area {
            min-width: 0;
        }

        @media (max-width: 768px) {
            .sidebar-custom {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 1050;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar-custom.active {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                display: block;
            }

            .content-area {
                width: 100%;
                padding: 1rem !important;
            }

            .page-header {
                align-items: flex-start !important;
                gap: 0.75rem;
            }

            .page-header h4 {
                font-size: 1.1rem;
            }
        }

        .mobile-menu-button {
            display: none;
        }

        .sidebar-backdrop {
            display: none;
        }

        .sidebar-backdrop.active {
            position: fixed;
            inset: 0;
            z-index: 1040;
            display: block;
            background: rgba(15, 23, 42, 0.45);
        }

        @media (max-width: 768px) {
            .mobile-menu-button {
                display: inline-flex;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="p-4 flex-grow-1 content-area">
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-dark mobile-menu-button" id="sidebarToggle" type="button" aria-label="Buka menu navigasi">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="fw-bold mb-0">Laporan Rekapitulasi Pengaduan</h4>
            </div>
            <button onclick="window.print()" class="btn btn-sm btn-success">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-start border-primary border-4">
                    <span class="text-muted fw-bold" style="font-size: 0.75rem;">TOTAL TIKET</span>
                    <h3 class="fw-bold mt-1 mb-0"><?= $total; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-start border-warning border-4">
                    <span class="text-warning fw-bold" style="font-size: 0.75rem;">MENUNGGU (PENDING)</span>
                    <h3 class="fw-bold mt-1 mb-0 text-warning"><?= $pending; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-start border-info border-4">
                    <span class="text-info fw-bold" style="font-size: 0.75rem;">SEDANG DIPROSES</span>
                    <h3 class="fw-bold mt-1 mb-0 text-info"><?= $proses; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-start border-success border-4">
                    <span class="text-success fw-bold" style="font-size: 0.75rem;">SELESAI DITANGANI</span>
                    <h3 class="fw-bold mt-1 mb-0 text-success"><?= $selesai; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    let touchStartX = 0;
    if (sidebarToggle && sidebar) {
        const setSidebarOpen = (isOpen) => {
            sidebar.classList.toggle('active', isOpen);
            sidebarBackdrop.classList.toggle('active', isOpen);
        };

        sidebarToggle.addEventListener('click', () => setSidebarOpen(!sidebar.classList.contains('active')));
        sidebarBackdrop.addEventListener('click', () => setSidebarOpen(false));
        document.addEventListener('touchstart', (event) => {
            touchStartX = event.changedTouches[0].screenX;
        }, { passive: true });
        document.addEventListener('touchend', (event) => {
            const swipeDistance = event.changedTouches[0].screenX - touchStartX;
            if (!sidebar.classList.contains('active') && touchStartX <= 30 && swipeDistance > 60) {
                setSidebarOpen(true);
            } else if (sidebar.classList.contains('active') && swipeDistance < -60) {
                setSidebarOpen(false);
            }
        }, { passive: true });
    }
</script>
</body>
</html>