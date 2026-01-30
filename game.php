<?php
require_once 'config/config.php';

// Get kategori
$sqlKategori = "SELECT * FROM kategori_game WHERE status = 'aktif' ORDER BY urutan ASC";
$resultKategori = query($sqlKategori);

// Get semua game
$filter = isset($_GET['kategori']) ? escape($_GET['kategori']) : '';
$search = isset($_GET['search']) ? escape($_GET['search']) : '';

$whereClause = "WHERE g.status = 'aktif'";
if (!empty($filter)) {
    $whereClause .= " AND g.id_kategori = '$filter'";
}
if (!empty($search)) {
    $whereClause .= " AND g.nama_game LIKE '%$search%'";
}

$sqlGame = "SELECT g.*, k.nama_kategori,
            (SELECT MIN(harga_jual) FROM produk WHERE id_game = g.id_game AND status = 'aktif') as min_price
            FROM game g 
            JOIN kategori_game k ON g.id_kategori = k.id_kategori
            $whereClause
            ORDER BY g.populer DESC, g.total_transaksi DESC";
$resultGame = query($sqlGame);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Game - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section style="padding: 3rem 0; min-height: 80vh;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎮 Semua Game</h1>
                <p style="color: var(--text-secondary);">Pilih game favoritmu dan top up sekarang juga!</p>
            </div>

            <!-- Filter & Search -->
            <div style="margin-bottom: 2rem;">
                <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="search" class="form-control" placeholder="🔍 Cari game..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div style="min-width: 200px;">
                        <select name="kategori" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php 
                            mysqli_data_seek($resultKategori, 0);
                            while ($kat = fetch($resultKategori)): 
                            ?>
                            <option value="<?= $kat['id_kategori'] ?>" <?= $filter == $kat['id_kategori'] ? 'selected' : '' ?>>
                                <?= $kat['nama_kategori'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <?php if ($filter || $search): ?>
                    <a href="<?= BASE_URL ?>game.php" class="btn" style="background: var(--dark-card);">
                        <i class="fas fa-times"></i> Reset
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Game Grid -->
            <?php if (numRows($resultGame) > 0): ?>
            <div class="game-grid">
                <?php while ($game = fetch($resultGame)): ?>
                <div class="game-card" onclick="location.href='<?= BASE_URL ?>detail_game.php?slug=<?= $game['slug'] ?>'">
                    <img src="<?= BASE_URL ?>assets/images/games/<?= $game['thumbnail'] ?>" 
                         alt="<?= $game['nama_game'] ?>" 
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
                                (<?= number_format($game['total_transaksi']) ?>)
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
            <?php else: ?>
            <div style="text-align: center; padding: 4rem 0;">
                <i class="fas fa-search" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                <h3>Game tidak ditemukan</h3>
                <p style="color: var(--text-secondary);">Coba kata kunci atau kategori lain</p>
                <a href="<?= BASE_URL ?>game.php" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Semua Game
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>