<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Riwayat & Manajemen Perawatan (Maintenance)</h2>
    <p class="text-muted">Pantau unit kamera, lensa, dan drone yang membutuhkan perbaikan atau servis teknis.</p>
</div>


<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Catat Unit Masuk Perbaikan</h5><p class="text-muted small mb-0">Unit yang dicatat akan otomatis berstatus maintenance dan tidak tampil sebagai unit yang dapat disewa.</p></div><span class="badge bg-warning-subtle text-warning">Input Maintenance</span></div>
    <form action="<?= base_url('kasir/maintenance/mulai') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Pilih Unit</label><select name="id_unit" class="form-select" required><option value="">-- Pilih unit --</option><?php foreach ($unitRusak as $u): if ($u['status_unit'] === 'maintenance') continue; ?><option value="<?= $u['id_unit'] ?>"><?= esc($u['kode_unit'].' - '.$u['nama_barang'].' ('.$u['merk'].')') ?> · <?= esc($u['status_unit']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label">Tanggal Masuk</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-2"><label class="form-label">Kondisi Kerusakan</label><select name="kondisi" class="form-select" required><option value="rusak_ringan">Rusak Ringan</option><option value="rusak_berat">Rusak Berat</option></select></div>
            <div class="col-md-2"><label class="form-label">Estimasi Biaya (Rp)</label><input type="number" name="biaya" class="form-control" min="0" step="1000" value="0" required></div>
            <div class="col-md-12"><label class="form-label">Keterangan / Keluhan</label><textarea name="keterangan" class="form-control" rows="2" maxlength="255" placeholder="Contoh: autofocus tidak berfungsi, lensa berjamur, layar LCD rusak..." required></textarea></div>
            <div class="col-12 text-end"><button class="btn btn-primary-custom"><i class="bi bi-tools me-1"></i> Simpan Perbaikan</button></div>
        </div>
    </form>
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
