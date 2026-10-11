<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Riwayat & Manajemen Perawatan (Maintenance)</h2>
    <p class="text-muted">Pantau unit kamera, lensa, dan drone yang membutuhkan perbaikan atau servis teknis.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card card-custom p-0 overflow-hidden">
            <div class="p-4 border-bottom">
                <h5 class="fw-bold text-dark mb-0">Daftar Riwayat Perawatan Unit</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Kode Unit</th>
                            <th>Nama Barang</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Selesai</th>
                            <th>Biaya Perbaikan</th>
                            <th>Keterangan / Masalah</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarMaintenance)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                                    Tidak ada unit yang sedang dalam masa perawatan. Semua unit prima.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftarMaintenance as $m): ?>
                                <tr>
                                    <td class="ps-4 font-monospace">#MNT-<?= $m['id_maintenance'] ?></td>
                                    <td class="font-monospace fw-bold text-dark"><?= esc($m['kode_unit']) ?></td>
                                    <td><?= esc($m['nama_barang']) ?> (<?= esc($m['merk']) ?>)</td>
                                    <td><?= date('d M Y', strtotime($m['tanggal_mulai'])) ?></td>
                                    <td>
                                        <?php if (!empty($m['tanggal_selesai'])): ?>
                                            <span class="badge bg-success-subtle text-success"><?= date('d M Y', strtotime($m['tanggal_selesai'])) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger">Dalam Proses</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold">Rp <?= number_format($m['biaya'], 0, ',', '.') ?></td>
                                    <td class="small text-muted"><?= esc($m['keterangan']) ?></td>
                                    <td class="text-end pe-4">
                                        <?php if (empty($m['tanggal_selesai'])): ?>
                                            <form action="<?= base_url('kasir/maintenance/selesai/' . $m['id_maintenance']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Tandai perawatan unit ini selesai dan kembalikan ke status tersedia?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                                    <i class="bi bi-check2-circle me-1"></i> Selesai Servis
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="bi bi-check-all text-success"></i> Tuntas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
