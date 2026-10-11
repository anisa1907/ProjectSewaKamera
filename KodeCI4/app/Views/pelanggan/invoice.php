<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- QRCode.js untuk render barcode QRIS dinamis di browser -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }
        .invoice-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 40px auto;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .invoice-header {
            background: #0f172a;
            color: #ffffff;
            padding: 30px 40px;
        }
        .qris-box {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            display: inline-block;
            text-align: center;
        }
        @media print {
            body {
                background: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                border: none;
            }
        }
    </style>
</head>
<body>

    <div class="container py-3">
        <div class="text-center mb-3 no-print">
            <a href="<?= base_url('pelanggan/status-sewa') ?>" class="btn btn-outline-secondary btn-sm me-2 rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
            </button>
        </div>

        <div class="invoice-card">
            <!-- Header -->
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-camera-fill me-2 text-primary"></i> CameraRent.Pro</h3>
                    <p class="mb-0 text-white-50 small">Kelompok 9 - Manajemen Inventory Sistem Sewa Camera</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary px-3 py-2 fs-6">INVOICE PEMBAYARAN</span>
                    <div class="font-monospace mt-1 text-white-50 small">#INV-<?= str_pad($sewa['id_sewa'], 6, '0', STR_PAD_LEFT) ?></div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-4 p-md-5">
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-bold">Penyewa / Pelanggan:</small>
                        <h5 class="fw-bold text-dark mt-1 mb-1"><?= esc($sewa['nama_pelanggan']) ?></h5>
                        <p class="text-muted small mb-0">Jaminan: <b class="text-dark"><?= esc($sewa['jaminan']) ?> (Fisik)</b></p>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <small class="text-muted text-uppercase fw-bold">Detail Jadwal Sewa:</small>
                        <div class="small mt-1">Mulai: <b><?= date('d M Y', strtotime($sewa['tanggal_sewa'])) ?></b></div>
                        <div class="small">Selesai: <b><?= date('d M Y', strtotime($sewa['tanggal_kembali'])) ?></b></div>
                        <div class="small mt-1">
                            Status Bayar: 
                            <span class="badge <?= ($pembayaran['status'] ?? '') === 'terbayar' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= strtoupper($pembayaran['status'] ?? 'MENUNGGU') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table Items -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered border-light-subtle">
                        <thead class="table-light">
                            <tr>
                                <th>Deskripsi Unit Kamera</th>
                                <th>Kode Unit</th>
                                <th>Harga / Hari</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= esc($it['nama_barang']) ?></div>
                                        <small class="text-muted"><?= esc($it['merk']) ?> • <?= esc($it['nama_kategori']) ?></small>
                                    </td>
                                    <td class="font-monospace"><?= esc($it['kode_unit']) ?></td>
                                    <td>Rp <?= number_format($it['harga_sewa'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold">Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end py-3 fs-5">TOTAL PEMBAYARAN:</th>
                                <th class="text-end py-3 fs-5 text-primary fw-bolder">
                                    Rp <?= number_format($sewa['total_harga'], 0, ',', '.') ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Bagian Pembayaran QRIS / Tunai -->
                <div class="p-4 rounded-4 bg-light border text-center">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-qr-code-scan text-primary me-2"></i>
                        Metode Pembayaran: <?= esc($pembayaran['metode'] ?? 'QRIS') ?>
                    </h5>

                    <?php if (($pembayaran['metode'] ?? 'QRIS') === 'QRIS'): ?>
                        <p class="text-muted small mb-3">Scan kode QRIS di bawah ini dengan BCA, Mandiri, GoPay, OVO, ShopeePay, atau DANA Anda:</p>
                        
                        <div class="qris-box shadow-sm mb-3">
                            <div id="qrcode"></div>
                            <div class="fw-bold font-monospace mt-2 text-dark" style="font-size: 0.85rem;">
                                QRIS STANDAR PEMBAYARAN NASIONAL
                            </div>
                        </div>

                        <div class="alert alert-info py-2 small d-inline-block text-start mb-0">
                            <i class="bi bi-info-circle-fill me-1"></i> Setelah transfer sukses, kasir akan memverifikasi pembayaran dan mengonfirmasi unit untuk diambil.
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Silakan lakukan pembayaran tunai (Cash) sebesar <b>Rp <?= number_format($sewa['total_harga'], 0, ',', '.') ?></b> di meja kasir saat mengambil unit kamera.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
                    <div>Kasir Penanggung Jawab: <b><?= esc($sewa['nama_kasir'] ?? 'Petugas Kasir') ?></b></div>
                    <div>Dicetak pada: <?= date('d/m/Y H:i') ?> WIB</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if (($pembayaran['metode'] ?? 'QRIS') === 'QRIS'): ?>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= esc($qrisData) ?>",
            width: 180,
            height: 180,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
        <?php endif; ?>
    </script>
</body>
</html>
