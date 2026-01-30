<?php
require_once 'config/config.php';

// Get data kategori
$sqlKategori = "SELECT * FROM kategori_game WHERE status = 'aktif' ORDER BY urutan ASC";
$resultKategori = query($sqlKategori);

// Get game populer
$sqlGamePopuler = "SELECT g.*, k.nama_kategori, 
                   (SELECT MIN(harga_jual) FROM produk WHERE id_game = g.id_game AND status = 'aktif') as min_price,
                   (SELECT MAX(harga_jual) FROM produk WHERE id_game = g.id_game AND status = 'aktif') as max_price
                   FROM game g 
                   JOIN kategori_game k ON g.id_kategori = k.id_kategori
                   WHERE g.status = 'aktif' AND g.populer = 1
                   ORDER BY g.total_transaksi DESC LIMIT 12";
$resultGamePopuler = query($sqlGamePopuler);

// Get testimoni
$sqlTestimoni = "SELECT * FROM testimoni WHERE status = 'approved' ORDER BY created_at DESC LIMIT 6";
$resultTestimoni = query($sqlTestimoni);

// Get promo
$sqlPromo = "SELECT * FROM promo WHERE status = 'aktif' AND tanggal_mulai <= CURDATE() AND tanggal_selesai >= CURDATE() LIMIT 3";
$resultPromo = query($sqlPromo);

// Get banner
$sqlBanner = "SELECT * FROM banner WHERE status = 'aktif' ORDER BY urutan ASC LIMIT 5";
$resultBanner = query($sqlBanner);

// Get stats
$sqlStats = "SELECT 
             (SELECT COUNT(*) FROM transaksi WHERE status_pembayaran = 'success') as total_transaksi,
             (SELECT COUNT(*) FROM users WHERE role IN ('user', 'reseller')) as total_member,
             (SELECT COUNT(*) FROM game WHERE status = 'aktif') as total_game";
$resultStats = query($sqlStats);
$stats = fetch($resultStats);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getSetting('nama_website') ?> - <?= getSetting('tagline') ?></title>
    <meta name="description" content="<?= getSetting('deskripsi_website') ?>">
    <meta name="keywords" content="<?= getSetting('meta_keywords') ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container">
            <a href="<?= BASE_URL ?>" class="logo">
                <i class="fas fa-gamepad"></i> <?= getSetting('nama_website') ?>
            </a>
            <ul class="nav-links">
                <li><a href="<?= BASE_URL ?>"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="<?= BASE_URL ?>game.php"><i class="fas fa-fire"></i> Game</a></li>
                <li><a href="<?= BASE_URL ?>promo.php"><i class="fas fa-tags"></i> Promo</a></li>
                <li><a href="<?= BASE_URL ?>cek_transaksi.php"><i class="fas fa-receipt"></i> Cek Transaksi</a></li>
                <li><a href="<?= BASE_URL ?>faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                <li><a href="<?= BASE_URL ?>kontak.php"><i class="fas fa-envelope"></i> Kontak</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= BASE_URL ?>admin/dashboard.php"><i class="fas fa-user-shield"></i> Dashboard</a></li>
                    <?php elseif (isReseller()): ?>
                        <li><a href="<?= BASE_URL ?>reseller/dashboard.php"><i class="fas fa-store"></i> Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>user/dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Masuk</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <h1>🎮 Top Up Game Termurah & Tercepat!</h1>
            <p>Proses otomatis dalam hitungan detik. Harga terjangkau, aman dan terpercaya!</p>
            <div class="search-box">
                <input type="text" id="searchGame" placeholder="🔍 Cari game favoritmu..." onkeyup="searchGame()">
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="stats">
        <div class="container">
            <div class="stat-item">
                <h3><?= number_format($stats['total_transaksi']) ?>+</h3>
                <p>Transaksi Berhasil</p>
            </div>
            <div class="stat-item">
                <h3><?= number_format($stats['total_member']) ?>+</h3>
                <p>Member Terdaftar</p>
            </div>
            <div class="stat-item">
                <h3><?= number_format($stats['total_game']) ?>+</h3>
                <p>Game Tersedia</p>
            </div>
            <div class="stat-item">
                <h3>4.9⭐</h3>
                <p>Rating Pelanggan</p>
            </div>
        </div>
    </section>

    <!-- GAME SECTION -->
    <section class="games-section">
        <div class="container">
            <div class="section-header">
                <h2>🔥 Game Populer</h2>
                <p>Pilih game favoritmu dan top up sekarang!</p>
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs">
                <button class="category-tab active" onclick="filterCategory('all')">
                    <i class="fas fa-th"></i> Semua Game
                </button>
                <?php while ($kategori = fetch($resultKategori)): ?>
                <button class="category-tab" onclick="filterCategory('<?= $kategori['id_kategori'] ?>')">
                    <i class="<?= $kategori['icon'] ?>"></i> <?= $kategori['nama_kategori'] ?>
                </button>
                <?php endwhile; ?>
            </div>

            <!-- Game Grid -->
            <div class="game-grid" id="gameGrid">
                <?php while ($game = fetch($resultGamePopuler)): ?>
                <div class="game-card" data-category="<?= $game['id_kategori'] ?>" onclick="location.href='<?= BASE_URL ?>detail_game.php?slug=<?= $game['slug'] ?>'">
                    <img src="<?= BASE_URL ?>assets/images/games/<?= $game['thumbnail'] ?>" alt="<?= $game['nama_game'] ?>" 
                         onerror="this.src='<?= BASE_URL ?>assets/images/default-game.jpg'">
                    <?php if ($game['populer']): ?>
                    <span class="badge">🔥 POPULER</span>
                    <?php endif; ?>
                    <div class="game-info">
                        <h3><?= $game['nama_game'] ?></h3>
                        <p class="publisher"><?= $game['publisher'] ?></p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span><?= $game['rating'] ?></span>
                            <span style="color: var(--text-secondary); margin-left: 5px;">
                                (<?= number_format($game['total_transaksi']) ?> transaksi)
                            </span>
                        </div>
                        <?php if ($game['min_price']): ?>
                        <div class="price-range">
                            💰 Mulai dari <?= formatRupiah($game['min_price']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?= BASE_URL ?>game.php" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> Lihat Semua Game
                </a>
            </div>
        </div>
    </section>

    <!-- PROMO SECTION -->
    <?php if (numRows($resultPromo) > 0): ?>
    <section class="promo-section">
        <div class="container">
            <div class="section-header">
                <h2>🎁 Promo Spesial</h2>
                <p>Dapatkan diskon menarik untuk top up favoritmu!</p>
            </div>
            <div class="promo-grid">
                <?php while ($promo = fetch($resultPromo)): ?>
                <div class="promo-card">
                    <h3>💥 <?= $promo['nama_promo'] ?></h3>
                    <p>
                        <?php if ($promo['tipe_diskon'] == 'persen'): ?>
                            Diskon <?= $promo['nilai_diskon'] ?>%
                        <?php else: ?>
                            Potongan <?= formatRupiah($promo['nilai_diskon']) ?>
                        <?php endif; ?>
                    </p>
                    <p style="font-size: 0.9rem;">
                        Min. Transaksi: <?= formatRupiah($promo['min_transaksi']) ?>
                    </p>
                    <p style="font-size: 0.9rem; margin-bottom: 1rem;">
                        Kode: <strong><?= $promo['kode_promo'] ?></strong>
                    </p>
                    <a href="<?= BASE_URL ?>game.php" class="btn">
                        <i class="fas fa-shopping-cart"></i> Belanja Sekarang
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- TESTIMONI SECTION -->
    <?php if (numRows($resultTestimoni) > 0): ?>
    <section class="testimoni-section">
        <div class="container">
            <div class="section-header">
                <h2>💬 Testimoni Pelanggan</h2>
                <p>Apa kata mereka tentang layanan kami?</p>
            </div>
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
                        </div>
                    </div>
                    <div class="testimoni-content">
                        "<?= $testimoni['komentar'] ?>"
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= getSetting('nama_website') ?></h3>
                    <p><?= getSetting('tagline') ?></p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/kii_leonrdo/<?= getSetting('instagram') ?>" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/share/1DLnBWue57/?mibextid=wwXIfr/<?= getSetting('facebook') ?>" target="_blank">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://wa.me/6289531036563/<?= getSetting('whatsapp') ?>" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="<?= getSetting('discord') ?>" target="_blank">
                            <i class="fab fa-discord"></i>
                        </a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Layanan</h3>
                    <ul>
                        <li><a href="<?= BASE_URL ?>game.php">Top Up Game</a></li>
                        <li><a href="<?= BASE_URL ?>promo.php">Promo</a></li>
                        <li><a href="<?= BASE_URL ?>cek_transaksi.php">Cek Transaksi</a></li>
                        <li><a href="<?= BASE_URL ?>testimoni.php">Testimoni</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Bantuan</h3>
                    <ul>
                        <li><a href="<?= BASE_URL ?>faq.php">FAQ</a></li>
                        <li><a href="<?= BASE_URL ?>kontak.php">Hubungi Kami</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Kontak</h3>
                    <ul>
                        <li><i class="fas fa-envelope"></i> <?= getSetting('email') ?></li>
                        <li><i class="fas fa-phone"></i> <?= getSetting('no_hp') ?></li>
                        <li><i class="fas fa-clock"></i> <?= getSetting('jam_operasional') ?></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= getSetting('nama_website') ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
    <script>
        // Filter by category
        function filterCategory(category) {
            const cards = document.querySelectorAll('.game-card');
            const tabs = document.querySelectorAll('.category-tab');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.closest('.category-tab').classList.add('active');
            
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Search game
        function searchGame() {
            const input = document.getElementById('searchGame').value.toLowerCase();
            const cards = document.querySelectorAll('.game-card');
            
            cards.forEach(card => {
                const gameName = card.querySelector('h3').textContent.toLowerCase();
                if (gameName.includes(input)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>