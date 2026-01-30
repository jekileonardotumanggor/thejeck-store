<?php
require_once 'config/config.php';

// Get invoice dari URL
$invoice = isset($_GET['invoice']) ? escape($_GET['invoice']) : '';

if (empty($invoice)) {
    redirect('index.php');
}

// Get detail transaksi
$sql = "SELECT * FROM transaksi WHERE kode_invoice = '$invoice'";
$result = query($sql);

if (numRows($result) == 0) {
    setFlash('danger', 'Transaksi tidak ditemukan!');
    redirect('cek_transaksi.php');
}

$transaksi = fetch($result);

// Cek jika transaksi sudah expired
if (strtotime($transaksi['expired_at']) < time() && $transaksi['status_pembayaran'] == 'pending') {
    $updateSql = "UPDATE transaksi SET status_pembayaran = 'expired', status_transaksi = 'failed' WHERE id_transaksi = {$transaksi['id_transaksi']}";
    query($updateSql);
    $transaksi['status_pembayaran'] = 'expired';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran <?= $invoice ?> - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 4rem auto;
            padding: 0 20px;
        }
        .payment-box {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }
        .status-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }
        .status-expired {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }
        .payment-method-box {
            background: var(--dark-bg);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .countdown {
            font-size: 2rem;
            font-weight: bold;
            color: var(--warning);
            text-align: center;
            padding: 1rem;
            background: rgba(245, 158, 11, 0.1);
            border-radius: 10px;
            margin: 1rem 0;
        }
        .payment-steps {
            background: var(--dark-bg);
            padding: 1.5rem;
            border-radius: 10px;
        }
        .payment-steps ol {
            padding-left: 1.5rem;
            color: var(--text-secondary);
        }
        .payment-steps ol li {
            margin-bottom: 0.8rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="checkout-container">
        <?php if ($transaksi['status_pembayaran'] == 'pending'): ?>
        <!-- PENDING PAYMENT -->
        <div class="payment-box">
            <div style="text-align: center;">
                <i class="fas fa-clock" style="font-size: 4rem; color: var(--warning); margin-bottom: 1rem;"></i>
                <h2>Menunggu Pembayaran</h2>
                <span class="status-badge status-pending">
                    <i class="fas fa-clock"></i> Menunggu Pembayaran
                </span>
            </div>

            <div class="countdown" id="countdown"></div>

            <div style="text-align: center; margin: 2rem 0;">
                <h3 style="margin-bottom: 0.5rem;">Invoice: <?= $transaksi['kode_invoice'] ?></h3>
                <h2 style="color: var(--primary-color); font-size: 2.5rem;"><?= formatRupiah($transaksi['total_bayar']) ?></h2>
            </div>

            <div class="payment-method-box">
                <h4 style="margin-bottom: 1rem;">💳 Metode Pembayaran</h4>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 1.2rem; font-weight: bold;"><?= $transaksi['metode_pembayaran'] ?></span>
                    <?php if ($transaksi['biaya_admin'] > 0): ?>
                    <span style="color: var(--text-secondary);">Biaya Admin: <?= formatRupiah($transaksi['biaya_admin']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="payment-steps">
                <h4 style="margin-bottom: 1rem;">📋 Cara Pembayaran</h4>
                <?php
                // Instruksi pembayaran berdasarkan metode
                $kategoriMetode = '';
                $sqlMetodeInfo = "SELECT kategori FROM metode_pembayaran WHERE nama_metode = '{$transaksi['metode_pembayaran']}'";
                $resultMetodeInfo = query($sqlMetodeInfo);
                if (numRows($resultMetodeInfo) > 0) {
                    $metodeInfo = fetch($resultMetodeInfo);
                    $kategoriMetode = $metodeInfo['kategori'];
                }

                if ($kategoriMetode == 'E-Wallet'):
                ?>
                <ol>
                    <li>Buka aplikasi <?= $transaksi['metode_pembayaran'] ?> Anda</li>
                    <li>Pilih menu Transfer/Kirim Uang</li>
                    <li>Masukkan nomor tujuan: <strong>081234567890</strong></li>
                    <li>Masukkan nominal: <strong><?= formatRupiah($transaksi['total_bayar']) ?></strong></li>
                    <li>Masukkan keterangan: <strong><?= $transaksi['kode_invoice'] ?></strong></li>
                    <li>Konfirmasi dan selesaikan pembayaran</li>
                    <li>Item akan otomatis masuk dalam 1-15 menit</li>
                </ol>
                <?php elseif ($kategoriMetode == 'Virtual Account'): ?>
                <ol>
                    <li>Login ke mobile banking atau ATM</li>
                    <li>Pilih menu Transfer ke Virtual Account</li>
                    <li>Masukkan nomor VA: <strong>8808<?= sprintf("%012d", $transaksi['id_transaksi']) ?></strong></li>
                    <li>Pastikan nominal: <strong><?= formatRupiah($transaksi['total_bayar']) ?></strong></li>
                    <li>Konfirmasi dan selesaikan pembayaran</li>
                    <li>Item akan otomatis masuk dalam 1-15 menit</li>
                </ol>
                <?php elseif ($kategoriMetode == 'Retail'): ?>
                <ol>
                    <li>Kunjungi gerai <?= $transaksi['metode_pembayaran'] ?> terdekat</li>
                    <li>Berikan kode pembayaran: <strong><?= $transaksi['kode_invoice'] ?></strong></li>
                    <li>Bayar sejumlah: <strong><?= formatRupiah($transaksi['total_bayar']) ?></strong></li>
                    <li>Simpan struk pembayaran</li>
                    <li>Item akan otomatis masuk dalam 1-15 menit</li>
                </ol>
                <?php else: ?>
                <ol>
                    <li>Scan QR Code yang muncul di layar</li>
                    <li>Atau transfer ke nomor yang tertera</li>
                    <li>Masukkan nominal: <strong><?= formatRupiah($transaksi['total_bayar']) ?></strong></li>
                    <li>Konfirmasi pembayaran</li>
                    <li>Item akan otomatis masuk dalam 1-15 menit</li>
                </ol>
                <?php endif; ?>
            </div>

            <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(99, 102, 241, 0.1); border-radius: 10px; text-align: center;">
                <strong>📱 Butuh Bantuan?</strong><br>
                <a href="https://wa.me/6289531036563/<?= getSetting('whatsapp') ?>?text=Halo, saya butuh bantuan untuk pembayaran invoice <?= $transaksi['kode_invoice'] ?>" 
                   target="_blank" 
                   class="btn btn-success" 
                   style="margin-top: 0.5rem; display: inline-block;">
                    <i class="fab fa-whatsapp"></i> Hubungi CS
                </a>
            </div>

            <!-- SIMULASI PEMBAYARAN (UNTUK DEMO) -->
            <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(16, 185, 129, 0.1); border: 2px dashed var(--success); border-radius: 10px; text-align: center;">
                <p style="color: var(--success); margin-bottom: 1rem;">
                    <strong>🎮 MODE DEMO - SIMULASI PEMBAYARAN</strong><br>
                    <small>Klik tombol di bawah untuk mensimulasikan pembayaran berhasil</small>
                </p>
                <button onclick="simulatePembayaran()" class="btn btn-success">
                    <i class="fas fa-check"></i> Simulasi Pembayaran Berhasil
                </button>
            </div>
        </div>

        <?php elseif ($transaksi['status_pembayaran'] == 'success'): ?>
        <!-- SUCCESS PAYMENT -->
        <div class="payment-box" style="text-align: center;">
            <i class="fas fa-check-circle" style="font-size: 5rem; color: var(--success); margin-bottom: 1rem;"></i>
            <h2>Pembayaran Berhasil!</h2>
            <span class="status-badge status-success">
                <i class="fas fa-check"></i> Pembayaran Berhasil
            </span>
            
            <div style="margin: 2rem 0;">
                <h3>Invoice: <?= $transaksi['kode_invoice'] ?></h3>
                <p style="color: var(--text-secondary);">
                    Item telah dikirim ke akun game Anda
                </p>
            </div>

            <div style="background: var(--dark-bg); padding: 1.5rem; border-radius: 10px; text-align: left;">
                <h4 style="margin-bottom: 1rem;">Detail Pesanan</h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Game:</span>
                    <strong><?= $transaksi['nama_game'] ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Produk:</span>
                    <strong><?= $transaksi['nama_produk'] ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>User ID:</span>
                    <strong><?= $transaksi['user_id_game'] ?><?= $transaksi['server_id_game'] ? ' (' . $transaksi['server_id_game'] . ')' : '' ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Bayar:</span>
                    <strong style="color: var(--primary-color);"><?= formatRupiah($transaksi['total_bayar']) ?></strong>
                </div>
            </div>

            <a href="<?= BASE_URL ?>" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-block;">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
        </div>

        <?php else: ?>
        <!-- EXPIRED/FAILED -->
        <div class="payment-box" style="text-align: center;">
            <i class="fas fa-times-circle" style="font-size: 5rem; color: var(--danger); margin-bottom: 1rem;"></i>
            <h2>Pembayaran <?= $transaksi['status_pembayaran'] == 'expired' ? 'Kadaluarsa' : 'Gagal' ?></h2>
            <span class="status-badge status-expired">
                <i class="fas fa-times"></i> <?= $transaksi['status_pembayaran'] == 'expired' ? 'Kadaluarsa' : 'Gagal' ?>
            </span>
            
            <p style="color: var(--text-secondary); margin: 1.5rem 0;">
                Maaf, pembayaran Anda telah <?= $transaksi['status_pembayaran'] == 'expired' ? 'kadaluarsa' : 'gagal' ?>.<br>
                Silakan lakukan pemesanan ulang.
            </p>

            <a href="<?= BASE_URL ?>detail_game.php?slug=<?= strtolower(str_replace(' ', '-', $transaksi['nama_game'])) ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i> Pesan Ulang
            </a>
        </div>
        <?php endif; ?>

        <!-- Detail Transaksi -->
        <div class="payment-box">
            <h3 style="margin-bottom: 1.5rem;">📄 Detail Transaksi</h3>
            <table style="width: 100%; color: var(--text-secondary);">
                <tr>
                    <td style="padding: 0.5rem 0;">Invoice</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><strong><?= $transaksi['kode_invoice'] ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0;">Tanggal</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><?= formatWaktu($transaksi['created_at']) ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0;">Game</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><strong><?= $transaksi['nama_game'] ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0;">Produk</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><?= $transaksi['nama_produk'] ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0;">User ID</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><?= $transaksi['user_id_game'] ?><?= $transaksi['server_id_game'] ? ' (' . $transaksi['server_id_game'] . ')' : '' ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0;">Harga Produk</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><?= formatRupiah($transaksi['harga_produk']) ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0;">Biaya Admin</td>
                    <td style="padding: 0.5rem 0; text-align: right;"><?= formatRupiah($transaksi['biaya_admin']) ?></td>
                </tr>
                <tr style="border-top: 2px solid rgba(255, 255, 255, 0.1);">
                    <td style="padding: 1rem 0; font-size: 1.2rem;"><strong>Total</strong></td>
                    <td style="padding: 1rem 0; text-align: right; font-size: 1.2rem; color: var(--primary-color);"><strong><?= formatRupiah($transaksi['total_bayar']) ?></strong></td>
                </tr>
            </table>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Countdown timer
        <?php if ($transaksi['status_pembayaran'] == 'pending'): ?>
        const expiredTime = new Date('<?= $transaksi['expired_at'] ?>').getTime();
        
        const countdownTimer = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiredTime - now;
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').innerHTML = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
            
            if (distance < 0) {
                clearInterval(countdownTimer);
                document.getElementById('countdown').innerHTML = '⏰ EXPIRED';
                location.reload();
            }
        }, 1000);
        <?php endif; ?>

        // Simulasi pembayaran (DEMO MODE)
        function simulatePembayaran() {
            if (confirm('Simulasikan pembayaran berhasil untuk invoice <?= $transaksi['kode_invoice'] ?>?')) {
                fetch('<?= BASE_URL ?>process/simulate_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'invoice=<?= $transaksi['kode_invoice'] ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Pembayaran berhasil disimulasikan!');
                        location.reload();
                    } else {
                        alert('❌ Gagal: ' + data.message);
                    }
                });
            }
        }

        // Auto refresh untuk cek status pembayaran
        <?php if ($transaksi['status_pembayaran'] == 'pending'): ?>
        setInterval(function() {
            location.reload();
        }, 30000); // Refresh setiap 30 detik
        <?php endif; ?>
    </script>
</body>
</html>