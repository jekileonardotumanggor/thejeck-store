<?php
require_once 'config/config.php';

// Get promo aktif
$sqlPromo = "SELECT * FROM promo 
             WHERE status = 'aktif' 
             AND tanggal_mulai <= CURDATE() 
             AND tanggal_selesai >= CURDATE() 
             ORDER BY created_at DESC";
$resultPromo = query($sqlPromo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .promo-card-detail {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s;
        }
        .promo-card-detail:hover {
            transform: translateX(10px);
            border-color: var(--accent-color);
        }
        .promo-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .promo-code {
            background: var(--gradient-1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            position: relative;
        }
        .promo-code:hover::after {
            content: 'Klik untuk copy';
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark-bg);
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        .promo-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .promo-detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section style="padding: 3rem 0; min-height: 80vh;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎁 Promo Spesial</h1>
                <p style="color: var(--text-secondary);">Dapatkan diskon dan penawaran menarik untuk top up game favoritmu!</p>
            </div>

            <?php if (numRows($resultPromo) > 0): ?>
                <?php while ($promo = fetch($resultPromo)): ?>
                <div class="promo-card-detail">
                    <div class="promo-header">
                        <div>
                            <h2 style="margin-bottom: 0.5rem;">💥 <?= $promo['nama_promo'] ?></h2>
                            <p style="color: var(--text-secondary);">
                                Berlaku: <?= formatTanggal($promo['tanggal_mulai']) ?> - <?= formatTanggal($promo['tanggal_selesai']) ?>
                            </p>
                        </div>
                        <div class="promo-code" onclick="copyCode('<?= $promo['kode_promo'] ?>')" title="Klik untuk copy">
                            <?= $promo['kode_promo'] ?>
                        </div>
                    </div>

                    <div style="margin: 1.5rem 0;">
                        <div style="display: inline-block; background: rgba(99, 102, 241, 0.2); padding: 1rem 2rem; border-radius: 10px;">
                            <h3 style="font-size: 2rem; color: var(--primary-color);">
                                <?php if ($promo['tipe_diskon'] == 'persen'): ?>
                                    DISKON <?= $promo['nilai_diskon'] ?>%
                                <?php else: ?>
                                    POTONGAN <?= formatRupiah($promo['nilai_diskon']) ?>
                                <?php endif; ?>
                            </h3>
                        </div>
                    </div>

                    <div class="promo-details">
                        <div class="promo-detail-item">
                            <i class="fas fa-shopping-cart" style="color: var(--primary-color);"></i>
                            <div>
                                <small>Min. Transaksi</small><br>
                                <strong><?= formatRupiah($promo['min_transaksi']) ?></strong>
                            </div>
                        </div>

                        <?php if ($promo['max_diskon'] > 0): ?>
                        <div class="promo-detail-item">
                            <i class="fas fa-tag" style="color: var(--warning);"></i>
                            <div>
                                <small>Max. Diskon</small><br>
                                <strong><?= formatRupiah($promo['max_diskon']) ?></strong>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="promo-detail-item">
                            <i class="fas fa-ticket-alt" style="color: var(--success);"></i>
                            <div>
                                <small>Kuota Tersisa</small><br>
                                <strong><?= number_format($promo['kuota'] - $promo['terpakai']) ?> / <?= number_format($promo['kuota']) ?></strong>
                            </div>
                        </div>

                        <div class="promo-detail-item">
                            <i class="fas fa-users" style="color: var(--accent-color);"></i>
                            <div>
                                <small>Untuk</small><br>
                                <strong><?= ucfirst($promo['untuk_user']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <a href="<?= BASE_URL ?>game.php" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Belanja Sekarang
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 0;">
                    <i class="fas fa-tags" style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                    <h3>Belum Ada Promo</h3>
                    <p style="color: var(--text-secondary);">Maaf, saat ini belum ada promo yang tersedia. Pantau terus halaman ini!</p>
                    <a href="<?= BASE_URL ?>game.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-gamepad"></i> Lihat Game
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                alert('✅ Kode promo "' + code + '" berhasil dicopy!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</body>
</html>