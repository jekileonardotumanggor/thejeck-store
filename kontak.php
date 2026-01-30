<?php
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = escape($_POST['nama']);
    $email = escape($_POST['email']);
    $no_hp = escape($_POST['no_hp']);
    $subjek = escape($_POST['subjek']);
    $pesan = escape($_POST['pesan']);
    
    if (!empty($nama) && !empty($email) && !empty($pesan)) {
        $sql = "INSERT INTO kontak (nama, email, no_hp, subjek, pesan) 
                VALUES ('$nama', '$email', '$no_hp', '$subjek', '$pesan')";
        
        if (query($sql)) {
            setFlash('success', 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.');
            redirect('kontak.php');
        } else {
            setFlash('danger', 'Gagal mengirim pesan. Silakan coba lagi.');
        }
    } else {
        setFlash('danger', 'Mohon isi semua field yang wajib!');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .kontak-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .kontak-info {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
        }
        .info-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--dark-bg);
            border-radius: 10px;
        }
        .info-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .kontak-form {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
        }
        @media (max-width: 768px) {
            .kontak-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section style="padding: 3rem 0; min-height: 80vh;">
        <div class="container" style="padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">📞 Hubungi Kami</h1>
                <p style="color: var(--text-secondary);">Ada pertanyaan? Kami siap membantu Anda!</p>
            </div>

            <div class="kontak-grid">
                <!-- Informasi Kontak -->
                <div>
                    <div class="kontak-info">
                        <h3 style="margin-bottom: 1.5rem;">Informasi Kontak</h3>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h4>WhatsApp</h4>
                                <p style="color: var(--text-secondary);"><?= getSetting('no_hp') ?></p>
                                <a href="https://wa.me/6289531036563/<?= getSetting('whatsapp') ?>" target="_blank" style="color: var(--success); text-decoration: none;">
                                    <i class="fas fa-external-link-alt"></i> Chat Sekarang
                                </a>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4>Email</h4>
                                <p style="color: var(--text-secondary);"><?= getSetting('email') ?></p>
                                <a href="mailto:<?= getSetting('email') ?>" style="color: var(--primary-color); text-decoration: none;">
                                    <i class="fas fa-paper-plane"></i> Kirim Email
                                </a>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4>Jam Operasional</h4>
                                <p style="color: var(--text-secondary);"><?= getSetting('jam_operasional') ?></p>
                                <small style="color: var(--success);">
                                    <i class="fas fa-check-circle"></i> Online 24/7
                                </small>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4>Alamat</h4>
                                <p style="color: var(--text-secondary);"><?= getSetting('alamat') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="kontak-info" style="margin-top: 1.5rem;">
                        <h3 style="margin-bottom: 1.5rem;">Ikuti Kami</h3>
                        <div class="social-links" style="justify-content: start;">
                            <a href="https://www.instagram.com/kii_leonrdo/<?= getSetting('instagram') ?>" target="_blank" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/share/1DLnBWue57/?mibextid=wwXIfr/<?= getSetting('facebook') ?>" target="_blank" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="<?= getSetting('discord') ?>" target="_blank" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                <i class="fab fa-discord"></i>
                            </a>
                            <a href="https://t.me/<?= getSetting('telegram') ?>" target="_blank" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                <i class="fab fa-telegram"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Kontak -->
                <div class="kontak-form">
                    <h3 style="margin-bottom: 1.5rem;">Kirim Pesan</h3>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Nama <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap Anda">
                        </div>

                        <div class="form-group">
                            <label>Email <span style="color: var(--danger);">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="email@example.com">
                        </div>

                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="tel" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="form-group">
                            <label>Subjek <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="subjek" class="form-control" required placeholder="Subjek pesan">
                        </div>

                        <div class="form-group">
                            <label>Pesan <span style="color: var(--danger);">*</span></label>
                            <textarea name="pesan" class="form-control" required rows="5" placeholder="Tulis pesan Anda di sini..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>