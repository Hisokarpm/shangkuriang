<?php
session_start();
require_once 'koneksi.php';

// Jika admin sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// Proses formulir saat tombol login diklik
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } else {
        // Mencegah SQL Injection dengan Prepared Statement
        $stmt = $conn->prepare("SELECT id, username, password, nama, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi Password (jika menggunakan hash) atau teks biasa
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                // Set Session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['user_id']         = $user['id'];
                $_SESSION['username']        = $user['username'];
                $_SESSION['nama']            = $user['nama'];
                $_SESSION['role']            = $user['role'];

                // Redirect ke Dashboard Admin
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PT. Shangkuriang Telekomunikasi Indonesia</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 30px;
        }

        /* Container Logo Perusahaan */
        .company-logo {
            max-width: 130px;
            height: auto;
            margin-bottom: 15px;
            object-fit: contain;
        }

        .btn-primary {
            background-color: #0284c7;
            border-color: #0284c7;
            padding: 10px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #0369a1;
            border-color: #0369a1;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Header / Logo PT. Shangkuriang Telekomunikasi Indonesia -->
        <div class="text-center mb-3">
            <img src="logo.png" alt="Logo PT. Shangkuriang Telekomunikasi Indonesia" class="company-logo" onerror="this.style.display='none'; this.onerror=null; document.getElementById('fallback-icon').classList.remove('d-none'); document.getElementById('fallback-icon').style.display='flex';">
            
            <div id="fallback-icon" class="brand-icon d-none mx-auto mb-2 align-items-center justify-content-center bg-light text-primary rounded-circle" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="bi bi-building"></i>
            </div>

            <h5 class="fw-bold text-dark mb-1">PT. Shangkuriang Telekomunikasi Indonesia</h5>
            <p class="text-muted small mb-0">Sistem Pengaduan Layanan Jaringan</p>
        </div>

        <!-- Pesan Error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show p-2 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $error; ?>
                <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="login.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus autocomplete="off">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 mt-2">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
            </button>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>