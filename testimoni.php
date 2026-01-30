<?php
require_once 'config/config.php';

// Get testimoni
$sqlTestimoni = "SELECT * FROM testimoni 
                 WHERE status = 'approved' 
                 ORDER BY created_at DESC";
$resultTestimoni = query($sqlTestimoni);

// Get statistics
$sqlStats = "SELECT 
             COUNT(*) as total,
             AVG(rating) as avg_rating,
             SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
             SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
             SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3
             FROM testimoni WHERE status = 'approved'";
$resultStats = query($sqlStats);
$stats = fetch($resultStats);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .rating-summary {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 3rem;
        }
        .rating-number {
            font-size: 4rem;
            font-weight: bold;
            color: var(--warning);
        }
        .rating-bars {
            display: grid;
            gap: 0.5rem;
            max-width: 400px;
            margin: 2rem auto 0;
        }
        .rating-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .rating-bar-fill {
            flex: 1;
            height: 8px;
            background: var(--dark-bg);
            border-radius: 4px;
            overflow: hidden;
        }
        .rating-bar-progress {
            height: 100%;
            background: var(--warning);
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section style="padding: 3rem 0; min-height: 80vh;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">⭐ Testimoni Pelanggan</h1>
                <p style="color: var(--text-secondary);">Ribuan pelanggan puas dengan layanan kami</p>
            </div>

            <!-- Rating Summary -->
            <div class="rating-summary">
                <div class="rating-number"><?= number_format($stats['avg_rating'], 1) ?></div>
                <div style="color: var(--warning); font-size: 1.5rem; margin-bottom: 0.5rem;">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <p style="color: var(--text-secondary);">Dari <?= number_format($stats['total']) ?> ulasan</p>

                <div class="rating-bars">
                    <?php
                    for ($i = 5; $i >= 3; $i--):
                        $count = $stats['rating_' . $i];
                        $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                    ?>
                    <div class="rating-bar">
                        <span style="width: 20px;"><?= $i ?></span>
                        <i class="fas fa-star" style="color: var(--warning);"></i>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: <?= $percentage ?>%;"></div>
                        </div>
                        <span style="color: var(--text-secondary); width: 40px; text-align: right;"><?= $count ?></span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Testimoni Grid -->
            <?php if (numRows($resultTestimoni) > 0): ?>
            <div class="testimoni-grid">
                <?php while ($testimoni = fetch($resultTestimoni)): ?>
                <div class="testimoni-card">
                    <div class="testimoni-header">
                        <div class="testimoni-avatar">
                            <?= strtoupper(substr($testimoni['nama'], 0, 1)) ?>
                        </div>
                        <div class="testimoni-info">
                            <h4><?= $testimoni['nama'] ?></h4>
                            <div class="testimoni-rating">
                                <?php for ($i = 0; $i < $testimoni['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <small style="color: var(--text-secondary);">
                                <?= formatTanggal($testimoni['created_at']) ?>
                            </small>
                        </div>
                    </div>
                    <div class="testimoni-content">
                        "<?= $testimoni['komentar'] ?>"
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 4rem 0;">
                <i class="fas fa-comments" style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                <h3>Belum Ada Testimoni</h3>
                <p style="color: var(--text-secondary);">Jadilah yang pertama memberikan testimoni!</p>
            </div>
            <?php endif; ?>

            <!-- CTA -->
            <div style="background: var(--dark-card); padding: 2rem; border-radius: 15px; text-align: center; margin-top: 3rem;">
                <h3 style="margin-bottom: 1rem;">Punya Pengalaman Berbelanja di Sini?</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Bagikan pengalaman Anda dan bantu pelanggan lain membuat keputusan!
                </p>
                <a href="<?= BASE_URL ?>game.php" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Belanja Sekarang
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>