<?php
require_once 'config/config.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } elseif (isReseller()) {
        redirect('reseller/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        }
        .auth-box {
            background: var(--dark-card);
            padding: 3rem;
            border-radius: 20px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .auth-header p {
            color: var(--text-secondary);
        }
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            color: var(--text-secondary);
            position: relative;
        }
        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        .divider::before {
            left: 0;
        }
        .divider::after {
            right: 0;
        }
        .back-home {
            text-align: center;
            margin-top: 2rem;
        }
        .back-home a {
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1><i class="fas fa-sign-in-alt"></i> Masuk</h1>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= $flash['message'] ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>process/login_process.php">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email atau Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="email@example.com atau username">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <input type="checkbox" name="remember" value="1">
                        Ingat Saya
                    </label>
                    <a href="#" style="color: var(--primary-color); text-decoration: none;">Lupa Password?</a>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="divider">atau</div>

            <div style="text-align: center;">
                <p style="color: var(--text-secondary);">
                    Belum punya akun? 
                    <a href="<?= BASE_URL ?>register.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">
                        Daftar Sekarang
                    </a>
                </p>
            </div>

            <div class="back-home">
                <a href="<?= BASE_URL ?>">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>