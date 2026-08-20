<?php
// Proteksi halaman login & koneksi database
require_once 'cek_session.php';
require_once 'koneksi.php';

// Hitung data statistik dari database
$total_tiket   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan"))['total'] ?? 0;
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'Pending'"))['total'] ?? 0;
$total_proses  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'Diproses'"))['total'] ?? 0;
$total_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'Selesai'"))['total'] ?? 0;

// Ambil data pengaduan masuk (dibatasi 10 terbaru untuk tampilan dashboard)
$result_pengaduan = mysqli_query($conn, "SELECT * FROM pengaduan ORDER BY id DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaduan Jaringan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar-width: 260px; 
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f6f9; 
            overflow-x: hidden;
        }
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            background-color: #1e293b; 
            color: #fff; 
            padding-top: 20px; 
            z-index: 1000; 
            transition: all 0.3s ease; 
            overflow-y: auto;
        }
        .sidebar .brand-title { 
            font-size: 1.1rem; 
            font-weight: bold; 
            padding: 0 20px 20px 20px; 
            border-bottom: 1px solid #334155; 
            color: #38bdf8; 
        }
        .sidebar a, .sidebar button.nav-link-btn { 
            padding: 12px 20px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            width: 100%;
            color: #94a3b8; 
            text-decoration: none; 
            font-size: 0.95rem; 
            transition: 0.2s; 
            background: none;
            border: none;
            text-align: left;
        }
        .sidebar a:hover, .sidebar button.nav-link-btn:hover,
        .sidebar a.active { 
            background-color: #0f172a; 
            color: #38bdf8; 
            border-left: 4px solid #38bdf8; 
        }
        .sidebar .submenu {
            background-color: #0f172a;
            padding-left: 15px;
        }
        .sidebar .submenu a {
            font-size: 0.88rem;
            padding: 10px 20px;
            border-left: none;
        }
        .sidebar .submenu a:hover {
            color: #38bdf8;
            background-color: #1e293b;
        }
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 25px; 
            transition: all 0.3s ease;
        }
        .card-metric { 
            border: none; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
            transition: transform 0.2s; 
        }
        .card-metric:hover { 
            transform: translateY(-3px); 
        }
        .icon-box { 
            width: 48px; 
            height: 48px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
        }

        /* Custom Styling Tombol Header Administrator Network */
        .btn-admin-header {
            background-color: #ffffff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-admin-header:hover,
        .btn-admin-header:focus,
        .btn-admin-header.show {
            background-color: #e2e8f0;
            border-color: #94a3b8;
            color: #0f172a;
        }
        
        /* Media Query untuk Tampilan Mobile */
        @media (max-width: 768px) {
            .sidebar { 
                margin-left: 0;
                transform: translateX(-100%);
            }
            .sidebar.active { 
                transform: translateX(0);
            }
            .main-content { 
                margin-left: 0; 
                padding: 16px;
            }

            .top-header {
                align-items: flex-start !important;
                gap: 12px;
            }

            .top-header h4 {
                font-size: 1.1rem;
            }

            .btn-admin-header {
                padding: 7px 9px;
            }

            .btn-admin-header span {
                display: none;
            }

            .table-responsive table {
                min-width: 640px;
            }

            .sidebar-backdrop {
                display: block;
            }
        }

        .sidebar-backdrop {
            display: none;
        }

        .sidebar-backdrop.active {
            position: fixed;
            inset: 0;
            z-index: 999;
            display: block;
            background: rgba(15, 23, 42, 0.45);
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="brand-title">
            <i class="bi bi-wifi me-2"></i>PT. Shangkuriang Telekomunikasi Indonesia
        </div>
        <div class="mt-3">
            <a href="dashboard.php" class="active">
                <span><i class="bi bi-speedometer2 me-2"></i> Dashboard</span>
            </a>
            
            <a href="data_pengaduan.php">
                <span><i class="bi bi-ticket-detailed me-2"></i> Data Pengaduan</span>
            </a>

            <a href="data_teknisi.php">
                <span><i class="bi bi-person-badge me-2"></i> Data Teknisi</span>
            </a>
            
            <a href="laporan_rekap.php">
                <span><i class="bi bi-bar-chart me-2"></i> Laporan Rekap</span>
            </a>
            
            <hr class="dropdown-divider border-secondary mx-3 my-3">
            
            <a href="logout.php" class="text-danger">
                <span><i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)</span>
            </a>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Alert Flash Message -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Top Header & Navbar -->
        <div class="d-flex justify-content-between align-items-center mb-4 top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-dark d-md-none" id="sidebarToggle" aria-label="Toggle Navigation Sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h4 class="fw-bold mb-0">Dashboard Layanan Jaringan</h4>
                    <p class="text-muted small mb-0">Kelola dan pantau tiket gangguan jaringan secara realtime.</p>
                </div>
            </div>

            <!-- TOMBOL DROPDOWN ADMINISTRATOR NETWORK (HEADER BAR) -->
            <div class="dropdown">
                <button class="btn btn-admin-header dropdown-toggle d-flex align-items-center gap-2 shadow-sm" 
                        type="button" 
                        id="dropdownAdminHeader" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="bi bi-person-circle text-primary fs-5"></i>
                    <span><?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator Network'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownAdminHeader">
                    <li class="px-3 py-2 text-muted small border-bottom">
                        Status Logged-in:<br>
                        <strong class="text-dark"><?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator Network'); ?></strong>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="profile.php">
                            <i class="bi bi-person me-2 text-secondary"></i> Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="pengaturan.php">
                            <i class="bi bi-gear me-2 text-secondary"></i> Pengaturan Akun
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger fw-semibold" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-metric p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold">TOTAL TIKET</span>
                            <h4 class="fw-bold mt-1 mb-0"><?= $total_tiket; ?></h4>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-inbox"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-metric p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold">MENUNGGU (PENDING)</span>
                            <h4 class="fw-bold mt-1 mb-0 text-warning"><?= $total_pending; ?></h4>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-metric p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold">SEDANG DIPROSES</span>
                            <h4 class="fw-bold mt-1 mb-0 text-info"><?= $total_proses; ?></h4>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-metric p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold">SELESAI DITANGANI</span>
                            <h4 class="fw-bold mt-1 mb-0 text-success"><?= $total_selesai; ?></h4>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Pengaduan Masuk -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Pengaduan Terbaru</h5>
                <a href="data_pengaduan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Kode Tiket</th>
                            <th>Pelapor</th>
                            <th>Masalah</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if ($result_pengaduan && mysqli_num_rows($result_pengaduan) > 0):
                            while ($row = mysqli_fetch_assoc($result_pengaduan)):
                        ?>
                            <tr>
                                <td class="ps-3"><?= $no++; ?></td>
                                <td><span class="fw-bold text-primary">#<?= htmlspecialchars($row['kode_tiket']); ?></span></td>
                                <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                                <td><?= htmlspecialchars($row['jenis_masalah']); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif ($row['status'] === 'Diproses'): ?>
                                        <span class="badge bg-info">Sedang Diproses</span>
                                    <?php elseif ($row['status'] === 'Selesai'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php elseif ($row['status'] === 'Ditolak'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($row['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="data_pengaduan.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengaduan masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (Pastikan file ini termuat agar Dropdown & Collapse berjalan) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Toggle Sidebar untuk Mobile -->
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        let touchStartX = 0;

        function setSidebarOpen(isOpen) {
            sidebar.classList.toggle('active', isOpen);
            sidebarBackdrop.classList.toggle('active', isOpen);
        }

        if (sidebarToggle && sidebar && sidebarBackdrop) {
            sidebarToggle.addEventListener('click', () => setSidebarOpen(!sidebar.classList.contains('active')));
            sidebarBackdrop.addEventListener('click', () => setSidebarOpen(false));

            document.addEventListener('touchstart', (event) => {
                touchStartX = event.changedTouches[0].screenX;
            }, { passive: true });

            document.addEventListener('touchend', (event) => {
                const touchEndX = event.changedTouches[0].screenX;
                const swipeDistance = touchEndX - touchStartX;
                const startedAtEdge = touchStartX <= 30;

                if (!sidebar.classList.contains('active') && startedAtEdge && swipeDistance > 60) {
                    setSidebarOpen(true);
                } else if (sidebar.classList.contains('active') && swipeDistance < -60) {
                    setSidebarOpen(false);
                }
            }, { passive: true });
        }
    </script>
</body>
</html>