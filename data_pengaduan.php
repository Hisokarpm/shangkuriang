<?php
session_start();
require_once 'cek_session.php';
include 'koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'teknisi'], true)) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data pengaduan
$query = mysqli_query($conn, "SELECT * FROM pengaduan ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengaduan - PT. Shangkuriang Telekomunikasi Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            overflow-x: hidden;
        }

        /* Desain Sidebar */
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
            padding: 20px 20px 15px 20px;
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

        .sidebar-nav .nav-link:hover {
            color: #38bdf8;
            background-color: rgba(255, 255, 255, 0.03);
        }

        .sidebar-nav .nav-link.active {
            color: #38bdf8;
            background-color: #0f172a;
            border-left: 4px solid #38bdf8;
            font-weight: 600;
        }

        .sidebar-nav .nav-link-logout {
            color: #ef4444;
        }
        
        .sidebar-nav .nav-link-logout:hover {
            color: #f87171;
            background-color: rgba(239, 68, 68, 0.05);
        }

        .foto-bukti-thumbnail {
            width: 56px;
            height: 56px;
            object-fit: cover;
        }

        .content-area {
            min-width: 0;
        }

        .table-responsive {
            -webkit-overflow-scrolling: touch;
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

            .mobile-menu-button {
                display: inline-flex !important;
            }

            .page-header {
                align-items: flex-start !important;
                gap: 0.75rem;
            }

            .page-header h4 {
                font-size: 1.1rem;
            }

            .admin-label {
                display: none;
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
    </style>
</head>
<body class="bg-light">

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar-custom flex-shrink-0" id="sidebar">
        <div class="brand-section">
            <h5 class="brand-title mb-0 d-flex gap-2">
                <i class="bi bi-wifi fs-4"></i>
                <span>PT. Shangkuriang Telekomunikasi Indonesia</span>
            </h5>
        </div>
        
        <nav class="nav flex-column sidebar-nav">
            <a class="nav-link" href="dashboard.php">
                <i class="bi bi-speedometer2 fs-5"></i> Dashboard
            </a>
            <a class="nav-link active" href="data_pengaduan.php">
                <i class="bi bi-hdd-stack fs-5"></i> Data Pengaduan
            </a>
            <a class="nav-link" href="data_teknisi.php">
                <i class="bi bi-person-badge fs-5"></i> Data Teknisi
            </a>
            <a class="nav-link" href="laporan_rekap.php">
                <i class="bi bi-bar-chart-line fs-5"></i> Laporan Rekap
            </a>
            <a class="nav-link nav-link-logout mt-3" href="logout.php">
                <i class="bi bi-box-arrow-right fs-5"></i> Keluar (Logout)
            </a>
        </nav>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Content Utama -->
    <div class="p-4 flex-grow-1 content-area">
        
        <!-- HEADER TOPBAR (Tombol Administrator Network + Dropdown) -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom page-header">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-dark mobile-menu-button" id="sidebarToggle" type="button" aria-label="Buka menu navigasi">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="fw-bold m-0 text-dark">Data Pengaduan Masalah Jaringan</h4>
            </div>

            <!-- Dropdown User -->
            <div class="dropdown">
                <button class="btn btn-white border bg-white rounded-3 dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 shadow-sm" 
                        type="button" 
                        id="dropdownAdmin" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 text-dark"></i>
                    <span class="fw-medium text-dark admin-label">Administrator Network</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownAdmin">
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2"></i> Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)</a></li>
                </ul>
            </div>
        </div>

        <!-- Tabel Data Pengaduan -->
        <div class="card border-0 shadow-sm p-3 rounded-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Tiket</th>
                            <th>Pelapor</th>
                            <th>Masalah</th>
                            <th>Foto Bukti</th>
                            <th>Status</th>
                            <th class="text-center">Aksi (Admin)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="fw-bold text-primary">#<?= $row['kode_tiket']; ?></td>
                            <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                            <td><?= htmlspecialchars($row['jenis_masalah']); ?></td>
                            <td>
                                <?php if (!empty($row['foto'])): ?>
                                    <button type="button" class="btn p-0 border-0" data-bs-toggle="modal" data-bs-target="#modalFoto<?= $row['id']; ?>" aria-label="Lihat foto bukti">
                                        <img src="uploads/<?= htmlspecialchars($row['foto']); ?>" alt="Foto bukti tiket <?= htmlspecialchars($row['kode_tiket']); ?>" class="foto-bukti-thumbnail rounded-2 border">
                                    </button>

                                    <div class="modal fade" id="modalFoto<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Foto Bukti #<?= htmlspecialchars($row['kode_tiket']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="uploads/<?= htmlspecialchars($row['foto']); ?>" alt="Foto bukti tiket <?= htmlspecialchars($row['kode_tiket']); ?>" class="img-fluid rounded-2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada foto</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif ($row['status'] == 'Diproses'): ?>
                                    <span class="badge bg-info text-dark">Sedang Diproses</span>
                                <?php elseif ($row['status'] == 'Selesai'): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <!-- Tombol Edit Status -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary rounded-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditStatus<?= $row['id']; ?>">
                                    <i class="bi bi-pencil-square"></i> Edit Status
                                </button>

                                <!-- MODAL EDIT STATUS -->
                                <div class="modal fade" id="modalEditStatus<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Ubah Status Tiket #<?= $row['kode_tiket']; ?></h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="update_status.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Pilih Status Baru:</label>
                                                        <select name="status" class="form-select form-select-sm" required>
                                                            <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending (Menunggu)</option>
                                                            <option value="Diproses" <?= ($row['status'] == 'Diproses') ? 'selected' : ''; ?>>Sedang Diproses</option>
                                                            <option value="Selesai" <?= ($row['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai Ditangani</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- END MODAL -->
                                <?php if ($row['status'] === 'Selesai'): ?>
                                    <form action="hapus_pengaduan.php" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengaduan yang sudah selesai ini?');">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pastikan Script JS Bootstrap dipanggil di akhir body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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