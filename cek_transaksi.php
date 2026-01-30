<?php
require_once 'config/config.php';

$transaksi = null;
$searched = false;

if (isset($_GET['invoice']) || isset($_POST['search'])) {
    $searched = true;
    $invoice = isset($_GET['invoice']) ? escape($_GET['invoice']) : escape($_POST['invoice']);
    
    $sql = "SELECT * FROM transaksi WHERE kode_invoice = '$invoice'";
    $result = query($sql);
    
    if (numRows($result) > 0) {
        $transaksi = fetch($result);
        
        // Update status jika expired
        if (strtotime($transaksi['expired_at']) < time() && $transaksi['status_pembayaran'] == 'pending') {
            $updateSql = "UPDATE transaksi SET status_pembayaran = 'expired', status_transaksi = 'failed' WHERE id_transaksi = {$transaksi['id_transaksi']}";
            query($updateSql);
            $transaksi['status_pembayaran'] = 'expired';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Transaksi - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-section {
            max-width: 600px;
            margin: 3rem auto;
            padding: 0 20px;
        }
        .search-box-large {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }
        .result-box {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        .status-card {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .status-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        .detail-table {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section style="padding: 3rem 0; min-height: 80vh;">
        <!-- Search Section -->
        <div class="search-section">
            <div class="search-box-large">
                <i class="fas fa-receipt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h2 style="margin-bottom: 0.5rem;">Cek Status Transaksi</h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    Masukkan kode invoice untuk mengecek status pesanan Anda
                </p>
                
                <form method="POST">
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="invoice" class="form-control" placeholder="Masukkan Kode Invoice (contoh: INV-20260114-0001)" required style="flex: 1;">
                        <button type="submit" name="search" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cek
                        </button>
                    </div>
                </form>

                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 1rem;">
                    <i class="fas fa-info-circle"></i> Kode invoice dikirim ke email Anda saat melakukan pemesanan
                </p>
            </div>
        </div>

        <!-- Result Section -->
        <?php if ($searched): ?>
        <div class="result-box">
            <?php if ($transaksi): ?>
                <!-- Status Card -->
                <div class="status-card">
                    <?php if ($transaksi['status_pembayaran'] == 'success'): ?>
                        <i class="fas fa-check-circle status-icon" style="color: var(--success);"></i>
                        <h2>Pembayaran Berhasil</h2>
                        <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                            <i class="fas fa-check"></i> SUKSES
                        </span>
                        <p style="color: var(--text-secondary); margin-top: 1rem;">
                            Item telah dikirim ke akun game Anda
                        </p>
                    <?php elseif ($transaksi['status_pembayaran'] == 'pending'): ?>
                        <i class="fas fa-clock status-icon" style="color: var(--warning);"></i>
                        <h2>Menunggu Pembayaran</h2>
                        <span class="badge badge-warning" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                            <i class="fas fa-clock"></i> PENDING
                        </span>
                        <p style="color: var(--text-secondary); margin-top: 1rem;">
                            Silakan selesaikan pembayaran Anda
                        </p>
                        <a href="<?= BASE_URL ?>checkout.php?invoice=<?= $transaksi['kode_invoice'] ?>" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-credit-card"></i> Bayar Sekarang
                        </a>
                    <?php else: ?>
                        <i class="fas fa-times-circle status-icon" style="color: var(--danger);"></i>
                        <h2>Transaksi <?= $transaksi['status_pembayaran'] == 'expired' ? 'Kadaluarsa' : 'Gagal' ?></h2>
                        <span class="badge badge-danger" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                            <i class="fas fa-times"></i> <?= strtoupper($transaksi['status_pembayaran']) ?>
                        </span>
                        <p style="color: var(--text-secondary); margin-top: 1rem;">
                            Mohon maaf, transaksi Anda tidak dapat diproses
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Detail Table -->
                <div class="detail-table">
                    <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Detail Transaksi
                    </h3>
                    
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Invoice</span>
                        <strong><?= $transaksi['kode_invoice'] ?></strong>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Tanggal</span>
                        <span><?= formatWaktu($transaksi['created_at']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Game</span>
                        <strong><?= $transaksi['nama_game'] ?></strong>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Produk</span>
                        <span><?= $transaksi['nama_produk'] ?></span>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Jumlah Item</span>
                        <span><?= $transaksi['jumlah_item'] ?></span>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">User ID</span>
                        <span><?= $transaksi['user_id_game'] ?><?= $transaksi['server_id_game'] ? ' (' . $transaksi['server_id_game'] . ')' : '' ?></span>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Metode Pembayaran</span>
                        <span><?= $transaksi['metode_pembayaran'] ?></span>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Harga Produk</span>
                        <span><?= formatRupiah($transaksi['harga_produk']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Biaya Admin</span>
                        <span><?= formatRupiah($transaksi['biaya_admin']) ?></span>
                    </div>
                    <div class="detail-row" style="border-top: 2px solid rgba(99, 102, 241, 0.3); padding-top: 1.5rem; margin-top: 0.5rem;">
                        <span style="font-size: 1.2rem; font-weight: bold;">Total</span>
                        <strong style="font-size: 1.2rem; color: var(--primary-color);"><?= formatRupiah($transaksi['total_bayar']) ?></strong>
                    </div>
                    
                    <?php if ($transaksi['status_pembayaran'] == 'success' && $transaksi['paid_at']): ?>
                    <div class="detail-row">
                        <span style="color: var(--text-secondary);">Dibayar Pada</span>
                        <span style="color: var(--success);"><?= formatWaktu($transaksi['paid_at']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div style="text-align: center; margin-top: 2rem;">
                    <?php if ($transaksi['status_pembayaran'] == 'pending'): ?>
                    <a href="<?= BASE_URL ?>checkout.php?invoice=<?= $transaksi['kode_invoice'] ?>" class="btn btn-primary" style="margin: 0.5rem;">
                        <i class="fas fa-credit-card"></i> Lanjutkan Pembayaran
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/6289531036563/<?= getSetting('whatsapp') ?>?text=Halo, saya ingin menanyakan tentang invoice <?= $transaksi['kode_invoice'] ?>" target="_blank" class="btn btn-success" style="margin: 0.5rem;">
                        <i class="fab fa-whatsapp"></i> Hubungi CS
                    </a>
                    
                    <a href="<?= BASE_URL ?>game.php" class="btn" style="background: var(--dark-card); margin: 0.5rem;">
                        <i class="fas fa-shopping-cart"></i> Belanja Lagi
                    </a>
                </div>

            <?php else: ?>
                <!-- Not Found -->
                <div class="status-card">
                    <i class="fas fa-search status-icon" style="color: var(--text-secondary);"></i>
                    <h2>Transaksi Tidak Ditemukan</h2>
                    <p style="color: var(--text-secondary); margin: 1rem 0;">
                        Maaf, transaksi dengan kode invoice yang Anda masukkan tidak ditemukan.
                    </p>
                    <ul style="list-style: none; text-align: left; max-width: 400px; margin: 1.5rem auto; color: var(--text-secondary);">
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: var(--primary-color);"></i> Pastikan kode invoice benar</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: var(--primary-color);"></i> Cek email Anda untuk invoice</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: var(--primary-color);"></i> Format: INV-YYYYMMDD-XXXX</li>
                    </ul>
                    <a href="<?= BASE_URL ?>cek_transaksi.php" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Cari Lagi
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>