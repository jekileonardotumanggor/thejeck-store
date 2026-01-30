<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - <?= getSetting('nama_website') ?></title>
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
            max-width: 500px;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        .password-strength {
            height: 4px;
            background: var(--dark-bg);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
        .strength-weak { background: var(--danger); width: 33%; }
        .strength-medium { background: var(--warning); width: 66%; }
        .strength-strong { background: var(--success); width: 100%; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1><i class="fas fa-user-plus"></i> Daftar Akun</h1>
                <p>Bergabunglah dengan ribuan gamers lainnya!</p>
            </div>

            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= $flash['message'] ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>process/register_process.php" id="registerForm">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control" required placeholder="Masukkan nama lengkap">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-at"></i> Username <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="username" class="form-control" required placeholder="Username" pattern="[a-zA-Z0-9_]{4,20}" title="4-20 karakter, huruf, angka, underscore">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> No. HP <span style="color: var(--danger);">*</span></label>
                        <input type="tel" name="no_hp" class="form-control" required placeholder="08xxxxxxxxxx" pattern="[0-9]{10,13}">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" name="email" class="form-control" required placeholder="email@example.com">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" minlength="6" id="password" onkeyup="checkPasswordStrength()">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <small id="strengthText" style="color: var(--text-secondary);"></small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Konfirmasi Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Ulangi password" minlength="6">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-gift"></i> Kode Referral (Opsional)</label>
                    <input type="text" name="referral_code" class="form-control" placeholder="Masukkan kode referral jika ada" value="<?= isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : '' ?>">
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: start; gap: 0.5rem;">
                        <input type="checkbox" name="agree" required style="margin-top: 4px;">
                        <span style="font-size: 0.9rem;">Saya setuju dengan <a href="#" style="color: var(--primary-color);">Syarat & Ketentuan</a> dan <a href="#" style="color: var(--primary-color);">Kebijakan Privasi</a></span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div class="divider">atau</div>

            <div style="text-align: center;">
                <p style="color: var(--text-secondary);">
                    Sudah punya akun? 
                    <a href="<?= BASE_URL ?>login.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">
                        Masuk di sini
                    </a>
                </p>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?= BASE_URL ?>" style="color: var(--primary-color); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            
            if (strength < 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Password lemah';
                strengthText.style.color = 'var(--danger)';
            } else if (strength < 4) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Password sedang';
                strengthText.style.color = 'var(--warning)';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Password kuat';
                strengthText.style.color = 'var(--success)';
            }
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirm = document.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
            }
        });
    </script>
</body>
</html>