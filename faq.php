<?php
require_once 'config/config.php';

// Get kategori FAQ
$kategoriList = ['umum', 'pembayaran', 'transaksi', 'akun', 'produk'];
$selectedKategori = isset($_GET['kategori']) ? escape($_GET['kategori']) : 'umum';

// Get FAQ berdasarkan kategori
$sqlFAQ = "SELECT * FROM faq 
           WHERE status = 'aktif' AND kategori_faq = '$selectedKategori' 
           ORDER BY urutan ASC, views DESC";
$resultFAQ = query($sqlFAQ);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .faq-categories {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            padding-bottom: 1rem;
        }
        .faq-category {
            padding: 0.8rem 1.5rem;
            background: var(--dark-card);
            border: 2px solid transparent;
            border-radius: 10px;
            color: var(--text-primary);
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.3s;
        }
        .faq-category:hover {
            border-color: var(--primary-color);
        }
        .faq-category.active {
            background: var(--gradient-1);
            border-color: var(--primary-color);
        }
        .faq-item {
            background: var(--dark-card);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .faq-item:hover {
            transform: translateX(5px);
            border-left: 4px solid var(--primary-color);
        }
        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            color: var(--text-secondary);
            padding-top: 0;
        }
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .faq-icon {
            transition: transform 0.3s;
        }
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section style="padding: 3rem 0; min-height: 80vh;">
        <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">❓ FAQ (Pertanyaan Umum)</h1>
                <p style="color: var(--text-secondary);">Temukan jawaban untuk pertanyaan yang sering diajukan</p>
            </div>

            <!-- Category Filter -->
            <div class="faq-categories">
                <?php foreach ($kategoriList as $kat): ?>
                <a href="?kategori=<?= $kat ?>" class="faq-category <?= $selectedKategori == $kat ? 'active' : '' ?>">
                    <i class="fas fa-<?= $kat == 'umum' ? 'info-circle' : ($kat == 'pembayaran' ? 'credit-card' : ($kat == 'transaksi' ? 'receipt' : ($kat == 'akun' ? 'user' : 'box'))) ?>"></i>
                    <?= ucfirst($kat) ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- FAQ List -->
            <?php if (numRows($resultFAQ) > 0): ?>
                <?php $no = 1; while ($faq = fetch($resultFAQ)): ?>
                <div class="faq-item" onclick="toggleFAQ(<?= $faq['id_faq'] ?>)">
                    <div class="faq-question">
                        <span><strong><?= $no ?>.</strong> <?= $faq['pertanyaan'] ?></span>
                        <i class="fas fa-chevron-down faq-icon" id="icon-<?= $faq['id_faq'] ?>"></i>
                    </div>
                    <div class="faq-answer" id="answer-<?= $faq['id_faq'] ?>">
                        <?= nl2br($faq['jawaban']) ?>
                    </div>
                </div>
                <?php $no++; endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 0;">
                    <i class="fas fa-question-circle" style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                    <h3>Belum Ada FAQ</h3>
                    <p style="color: var(--text-secondary);">Kategori ini belum memiliki FAQ</p>
                </div>
            <?php endif; ?>

            <!-- Contact Box -->
            <div style="background: var(--dark-card); padding: 2rem; border-radius: 15px; text-align: center; margin-top: 3rem;">
                <h3 style="margin-bottom: 1rem;">Masih Ada Pertanyaan?</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Jika pertanyaan Anda belum terjawab, silakan hubungi customer service kami
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="https://wa.me/<?= getSetting('whatsapp') ?>" target="_blank" class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="<?= BASE_URL ?>kontak.php" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Kirim Pesan
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleFAQ(id) {
            const item = event.currentTarget;
            const allItems = document.querySelectorAll('.faq-item');
            
            // Close all other items
            allItems.forEach(i => {
                if (i !== item) {
                    i.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
            
            // Update view count (optional - via AJAX)
            if (item.classList.contains('active')) {
                fetch('<?= BASE_URL ?>process/update_faq_view.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id_faq=' + id
                });
            }
        }
    </script>
</body>
</html>