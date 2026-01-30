<?php
require_once 'config/config.php';

// Get slug dari URL
$slug = isset($_GET['slug']) ? escape($_GET['slug']) : '';

if (empty($slug)) {
    redirect('game.php');
}

// Get detail game
$sqlGame = "SELECT g.*, k.nama_kategori 
            FROM game g 
            JOIN kategori_game k ON g.id_kategori = k.id_kategori
            WHERE g.slug = '$slug' AND g.status = 'aktif'";
$resultGame = query($sqlGame);

if (numRows($resultGame) == 0) {
    redirect('game.php');
}

$game = fetch($resultGame);

// Get produk
$sqlProduk = "SELECT * FROM produk 
              WHERE id_game = {$game['id_game']} AND status = 'aktif' 
              ORDER BY urutan ASC, harga_jual ASC";
$resultProduk = query($sqlProduk);

// Get metode pembayaran
$sqlMetode = "SELECT * FROM metode_pembayaran WHERE status = 'aktif' ORDER BY urutan ASC";
$resultMetode = query($sqlMetode);
$metodeByKategori = [];
while ($metode = fetch($resultMetode)) {
    $metodeByKategori[$metode['kategori']][] = $metode;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up <?= $game['nama_game'] ?> - <?= getSetting('nama_website') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .detail-game {
            padding: 2rem 0;
            min-height: 100vh;
        }
        .game-banner {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .order-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .order-form {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
        }
        .order-summary {
            background: var(--dark-card);
            padding: 2rem;
            border-radius: 15px;
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .produk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .produk-item {
            background: var(--dark-bg);
            padding: 1rem;
            border-radius: 10px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        .produk-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }
        .produk-item.selected {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }
        .produk-item input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        .produk-name {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .produk-price {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: bold;
        }
        .produk-discount {
            background: var(--danger);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 5px;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 0.3rem;
        }
        .metode-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .metode-kategori {
            background: var(--dark-bg);
            padding: 1rem;
            border-radius: 10px;
        }
        .metode-kategori h4 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        .metode-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.8rem;
        }
        .metode-item {
            background: var(--dark-card);
            padding: 0.8rem;
            border-radius: 8px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        .metode-item:hover {
            border-color: var(--primary-color);
        }
        .metode-item.selected {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }
        .metode-item input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .summary-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .btn-order {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            margin-top: 1rem;
        }
        .info-box {
            background: rgba(99, 102, 241, 0.1);
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .order-container {
                grid-template-columns: 1fr;
            }
            .order-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="detail-game">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <img src="<?= BASE_URL ?>assets/images/games/<?= $game['banner'] ?>" 
                 alt="<?= $game['nama_game'] ?>" 
                 class="game-banner"
                 onerror="this.src='<?= BASE_URL ?>assets/images/default-banner.jpg'">
            
            <div class="order-container">
                <!-- FORM ORDER -->
                <div class="order-form">
                    <h1 style="margin-bottom: 0.5rem;"><?= $game['nama_game'] ?></h1>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                        <?= $game['publisher'] ?> • ⭐ <?= $game['rating'] ?>
                    </p>

                    <?php if ($game['panduan_id']): ?>
                    <div class="info-box">
                        <strong>📌 Cara Menemukan ID:</strong><br>
                        <?= nl2br($game['panduan_id']) ?>
                    </div>
                    <?php endif; ?>

                    <form id="orderForm" method="POST" action="<?= BASE_URL ?>process/checkout_process.php">
                        <input type="hidden" name="id_game" value="<?= $game['id_game'] ?>">
                        
                        <!-- STEP 1: User ID -->
                        <div class="section-title">
                            <span style="background: var(--primary-color); width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">1</span>
                            Masukkan Data Akun
                        </div>
                        
                        <div class="form-group">
                            <label><?= $game['field_id'] ?> <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="user_id_game" class="form-control" required placeholder="Masukkan <?= $game['field_id'] ?>">
                        </div>

                        <?php if ($game['field_server']): ?>
                        <div class="form-group">
                            <label><?= $game['field_server'] ?> <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="server_id_game" class="form-control" required placeholder="Masukkan <?= $game['field_server'] ?>">
                        </div>
                        <?php endif; ?>

                        <!-- STEP 2: Pilih Produk -->
                        <div class="section-title" style="margin-top: 2rem;">
                            <span style="background: var(--primary-color); width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">2</span>
                            Pilih Nominal
                        </div>

                        <div class="produk-grid">
                            <?php 
                            mysqli_data_seek($resultProduk, 0);
                            while ($produk = fetch($resultProduk)): 
                            ?>
                            <div class="produk-item" onclick="selectProduk(<?= $produk['id_produk'] ?>, <?= $produk['harga_jual'] ?>, '<?= $produk['nama_produk'] ?>')">
                                <input type="radio" name="id_produk" value="<?= $produk['id_produk'] ?>" required>
                                <div class="produk-name"><?= $produk['jumlah_item'] ?></div>
                                <?php if ($produk['bonus']): ?>
                                <div style="color: var(--success); font-size: 0.85rem; margin-bottom: 0.3rem;">
                                    +<?= $produk['bonus'] ?>
                                </div>
                                <?php endif; ?>
                                <div class="produk-price"><?= formatRupiah($produk['harga_jual']) ?></div>
                                <?php if ($produk['diskon'] > 0): ?>
                                <span class="produk-discount">-<?= $produk['diskon'] ?>%</span>
                                <?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- STEP 3: Metode Pembayaran -->
                        <div class="section-title" style="margin-top: 2rem;">
                            <span style="background: var(--primary-color); width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">3</span>
                            Pilih Pembayaran
                        </div>

                        <div class="metode-grid">
                            <?php foreach ($metodeByKategori as $kategori => $metodes): ?>
                            <div class="metode-kategori">
                                <h4><?= $kategori ?></h4>
                                <div class="metode-items">
                                    <?php foreach ($metodes as $metode): ?>
                                    <div class="metode-item" onclick="selectMetode(<?= $metode['id_metode'] ?>, <?= $metode['biaya_admin'] ?>, '<?= $metode['nama_metode'] ?>')">
                                        <input type="radio" name="id_metode" value="<?= $metode['id_metode'] ?>" required>
                                        <div style="font-weight: 600; margin-bottom: 0.3rem;"><?= $metode['nama_metode'] ?></div>
                                        <?php if ($metode['biaya_admin'] > 0): ?>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                            Fee: <?= formatRupiah($metode['biaya_admin']) ?>
                                        </div>
                                        <?php else: ?>
                                        <div style="font-size: 0.8rem; color: var(--success);">
                                            Tanpa Biaya Admin
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- STEP 4: Data Pembeli -->
                        <div class="section-title" style="margin-top: 2rem;">
                            <span style="background: var(--primary-color); width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">4</span>
                            Informasi Kontak
                        </div>

                        <div class="form-group">
                            <label>Email <span style="color: var(--danger);">*</span></label>
                            <input type="email" name="email_pembeli" class="form-control" required placeholder="email@example.com" value="<?= isLoggedIn() ? $_SESSION['email'] : '' ?>">
                        </div>

                        <div class="form-group">
                            <label>No. WhatsApp <span style="color: var(--danger);">*</span></label>
                            <input type="tel" name="no_hp_pembeli" class="form-control" required placeholder="08xxxxxxxxxx" value="<?= isLoggedIn() ? $_SESSION['no_hp'] : '' ?>">
                        </div>

                        <!-- Voucher -->
                        <div class="form-group">
                            <label>Kode Voucher (Opsional)</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" id="voucherCode" class="form-control" placeholder="Masukkan kode voucher">
                                <button type="button" class="btn btn-primary" onclick="validateVoucher()" style="white-space: nowrap;">
                                    Gunakan
                                </button>
                            </div>
                            <div id="voucherMessage" style="margin-top: 0.5rem;"></div>
                        </div>
                    </form>
                </div>

                <!-- ORDER SUMMARY -->
                <div class="order-summary">
                    <h3 style="margin-bottom: 1.5rem;">📋 Ringkasan Pesanan</h3>
                    
                    <div class="summary-row">
                        <span>Produk:</span>
                        <span id="summaryProduk">-</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Harga:</span>
                        <span id="summaryHarga">Rp 0</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Biaya Admin:</span>
                        <span id="summaryAdmin">Rp 0</span>
                    </div>
                    
                    <div class="summary-row" id="discountRow" style="display: none; color: var(--success);">
                        <span>Diskon:</span>
                        <span id="summaryDiskon">- Rp 0</span>
                    </div>
                    
                    <div class="summary-row" style="border: none; padding-top: 1rem;">
                        <span style="font-size: 1.1rem; font-weight: bold;">Total:</span>
                        <span class="summary-total" id="summaryTotal">Rp 0</span>
                    </div>

                    <button type="submit" form="orderForm" class="btn btn-primary btn-order">
                        <i class="fas fa-shopping-cart"></i> Beli Sekarang
                    </button>

                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(16, 185, 129, 0.1); border-radius: 8px; font-size: 0.9rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-shield-alt" style="color: var(--success);"></i>
                            <strong>100% Aman & Terpercaya</strong>
                        </div>
                        <ul style="list-style: none; color: var(--text-secondary);">
                            <li>✓ Proses otomatis 24/7</li>
                            <li>✓ Data dijamin aman</li>
                            <li>✓ Garansi uang kembali</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        let selectedProduk = 0;
        let selectedMetode = 0;
        let hargaProduk = 0;
        let biayaAdmin = 0;
        let diskonVoucher = 0;
        let namaProduk = '';
        let namaMetode = '';

        function selectProduk(id, harga, nama) {
            // Remove previous selection
            document.querySelectorAll('.produk-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Add selection
            event.currentTarget.classList.add('selected');
            event.currentTarget.querySelector('input').checked = true;
            
            selectedProduk = id;
            hargaProduk = harga;
            namaProduk = nama;
            
            updateSummary();
        }

        function selectMetode(id, admin, nama) {
            // Remove previous selection
            document.querySelectorAll('.metode-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Add selection
            event.currentTarget.classList.add('selected');
            event.currentTarget.querySelector('input').checked = true;
            
            selectedMetode = id;
            biayaAdmin = admin;
            namaMetode = nama;
            
            updateSummary();
        }

        function updateSummary() {
            document.getElementById('summaryProduk').textContent = namaProduk || '-';
            document.getElementById('summaryHarga').textContent = formatRupiah(hargaProduk);
            document.getElementById('summaryAdmin').textContent = formatRupiah(biayaAdmin);
            
            const total = hargaProduk + biayaAdmin - diskonVoucher;
            document.getElementById('summaryTotal').textContent = formatRupiah(total);
            
            if (diskonVoucher > 0) {
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('summaryDiskon').textContent = '- ' + formatRupiah(diskonVoucher);
            }
        }

        function validateVoucher() {
            const code = document.getElementById('voucherCode').value;
            const total = hargaProduk + biayaAdmin;
            
            if (!code) {
                alert('Masukkan kode voucher!');
                return;
            }
            
            if (total === 0) {
                alert('Pilih produk dan metode pembayaran terlebih dahulu!');
                return;
            }
            
            // AJAX request to validate voucher
            fetch('<?= BASE_URL ?>process/validate_voucher.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'kode_promo=' + code + '&total_transaksi=' + total
            })
            .then(response => response.json())
            .then(data => {
                const msgDiv = document.getElementById('voucherMessage');
                if (data.success) {
                    msgDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    diskonVoucher = data.diskon;
                    updateSummary();
                } else {
                    msgDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    diskonVoucher = 0;
                    updateSummary();
                }
            });
        }

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>
</html>