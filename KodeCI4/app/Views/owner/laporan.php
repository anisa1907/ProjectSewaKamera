<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-dark mb-1">Laporan Transaksi & Keuangan Sewa</h2>
            <p class="text-muted">Rekapitulasi pemasukan sewa kamera, pembayaran lunas, dan denda operasional.</p>
        </div>
        <div class="col-md-5 text-md-end">
            <a href="<?= base_url('owner/laporan/cetak?tgl_awal=' . $tglAwal . '&tgl_akhir=' . $tglAkhir) ?>" target="_blank" class="btn btn-primary-custom rounded-pill px-4">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen Laporan
            </a>
        </div>
    </div>
</div>

<!-- Filter Periode -->
<div class="card card-custom p-4 mb-4">
    <form action="<?= base_url('owner/laporan') ?>" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Tanggal Awal</label>
            <input type="date" name="tgl_awal" class="form-control rounded-3" value="<?= esc($tglAwal) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" class="form-control rounded-3" value="<?= esc($tglAkhir) ?>" required>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-dark rounded-3 w-100 py-2">
                <i class="bi bi-funnel me-1"></i> Terapkan Filter Periode
            </button>
        </div>
    </form>
</div>

<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-light border">
            <span class="text-muted small fw-semibold text-uppercase">Total Pendapatan Sewa (Lunas)</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">Rp <?= number_format($totalPendapatanSewa, 0, ',', '.') ?></h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-light border">
            <span class="text-muted small fw-semibold text-uppercase">Total Pendapatan Denda</span>
            <h3 class="fw-bold text-danger mb-0 mt-1">Rp <?= number_format($totalDenda, 0, ',', '.') ?></h3>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="card card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="ps-4">No. Transaksi</th>
                    <th>Tgl Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Kasir</th>
                    <th>Total Sewa</th>
                    <th>Metode & Status Bayar</th>
                    <th>Denda</th>
                    <th>Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporan)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Tidak ada transaksi sewa pada rentang periode yang dipilih.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($laporan as $row): ?>
                        <tr>
                            <td class="ps-4 font-monospace fw-bold">#INV-<?= str_pad($row['id_sewa'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_sewa'])) ?></td>
                            <td class="fw-semibold"><?= esc($row['nama_pelanggan']) ?></td>
                            <td><?= esc($row['nama_kasir'] ?? '-') ?></td>
                            <td class="fw-bold">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary me-1"><?= esc($row['metode'] ?? 'QRIS') ?></span>
                                <span class="badge <?= ($row['status_pembayaran'] ?? '') === 'terbayar' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= esc($row['status_pembayaran'] ?? 'menunggu') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($row['denda']) && (float)$row['denda'] > 0): ?>
                                    <span class="text-danger fw-bold">Rp <?= number_format($row['denda'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= esc($row['status']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
