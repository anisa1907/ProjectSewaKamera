<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Executive Dashboard - Owner</h2>
    <p class="text-muted">Ikhtisar performa bisnis penyewaan, arus pendapatan lunas, dan utilitas armada unit.</p>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4 bg-primary text-white border-0 shadow">
            <span class="text-white-50 small fw-semibold text-uppercase">Pendapatan Bersih (Terbayar)</span>
            <h3 class="fw-bolder mb-0 mt-2">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h3>
            <div class="small text-white-50 mt-2"><i class="bi bi-wallet2 me-1"></i> Dari transaksi terkonfirmasi</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4">
            <span class="text-muted small fw-semibold text-uppercase">Total Transaksi</span>
            <h3 class="fw-bold text-dark mb-0 mt-2"><?= $totalTransaksi ?></h3>
            <div class="small text-muted mt-2"><i class="bi bi-receipt me-1"></i> Seluruh riwayat rental</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4">
            <span class="text-muted small fw-semibold text-uppercase">Transaksi Selesai</span>
            <h3 class="fw-bold text-success mb-0 mt-2"><?= $transaksiSelesai ?></h3>
            <div class="small text-muted mt-2"><i class="bi bi-check-circle me-1"></i> Unit berhasil dikembalikan</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-custom p-4">
            <span class="text-muted small fw-semibold text-uppercase">Total Unit Kamera & Alat</span>
            <h3 class="fw-bold text-info mb-0 mt-2"><?= $totalUnit ?></h3>
            <div class="small text-muted mt-2"><i class="bi bi-camera me-1"></i> Aset peralatan terdaftar</div>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">Aktivitas Transaksi Terkini</h5>
        <a href="<?= base_url('owner/laporan') ?>" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Buka Laporan Lengkap
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Jadwal</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                    <th>Status Rental</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksiTerbaru)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada aktivitas transaksi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transaksiTerbaru as $tx): ?>
                        <tr>
                            <td class="font-monospace fw-bold">#INV-<?= str_pad($tx['id_sewa'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= esc($tx['nama_pelanggan']) ?></td>
                            <td><?= date('d M Y', strtotime($tx['tanggal_sewa'])) ?> s/d <?= date('d M Y', strtotime($tx['tanggal_kembali'])) ?></td>
                            <td class="fw-bold">Rp <?= number_format($tx['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= ($tx['status_pembayaran'] ?? '') === 'terbayar' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= esc($tx['status_pembayaran'] ?? 'menunggu') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary"><?= esc($tx['status']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
