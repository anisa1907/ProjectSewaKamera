<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Dashboard Kasir & Operasional</h2>
    <p class="text-muted">Pantau status armada kamera, transaksi aktif, dan antrean verifikasi pembayaran.</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Unit Tersedia</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalTersedia ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-4">
                    <i class="bi bi-camera"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4 border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Sedang Disewa</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalDisewa ?></h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3 fs-4">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Dalam Perawatan</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $totalMaintenance ?></h3>
                </div>
                <div class="p-3 bg-danger-subtle text-danger rounded-3 fs-4">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4 border-start border-info border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Menunggu Verifikasi</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $sewaMenungguBayar ?></h3>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3 fs-4">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Navigation & Latest Transactions -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Transaksi Terbaru</h5>
                <a href="<?= base_url('kasir/transaksi') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Kelola Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transaksiTerbaru)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi masuk.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transaksiTerbaru as $tx): ?>
                                <tr>
                                    <td class="font-monospace fw-bold">#INV-<?= str_pad($tx['id_sewa'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= esc($tx['nama_pelanggan']) ?></td>
                                    <td>Rp <?= number_format($tx['total_harga'], 0, ',', '.') ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= esc($tx['metode'] ?? '-') ?></span></td>
                                    <td>
                                        <span class="badge <?= ($tx['status_pembayaran'] ?? '') === 'terbayar' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= esc($tx['status_pembayaran'] ?? 'menunggu') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4">
            <h5 class="fw-bold text-dark mb-3">Aksi Cepat Kasir</h5>
            <div class="d-grid gap-2">
                <a href="<?= base_url('kasir/master-data') ?>" class="btn btn-light text-start p-3 rounded-3 border">
                    <i class="bi bi-plus-circle text-primary me-2"></i> Tambah Master Barang & Unit
                </a>
                <a href="<?= base_url('kasir/transaksi') ?>" class="btn btn-light text-start p-3 rounded-3 border">
                    <i class="bi bi-check2-circle text-success me-2"></i> Verifikasi Pembayaran & Jaminan
                </a>
                <a href="<?= base_url('kasir/maintenance') ?>" class="btn btn-light text-start p-3 rounded-3 border">
                    <i class="bi bi-wrench-adjustable text-danger me-2"></i> Catat & Selesaikan Perawatan
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
