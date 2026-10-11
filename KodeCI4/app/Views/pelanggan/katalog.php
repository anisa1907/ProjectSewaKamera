<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-dark mb-1">Katalog Kamera & Perlengkapan</h2>
            <p class="text-muted">Pilih unit fisik yang tersedia untuk kebutuhan foto dan video Anda.</p>
        </div>
        <div class="col-md-5">
            <form action="<?= base_url('pelanggan/katalog') ?>" method="GET" class="d-flex gap-2 justify-content-md-end">
                <select name="kategori" class="form-select w-auto rounded-3 shadow-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= $kat['id_kategori'] ?>" <?= ($selectedKat == $kat['id_kategori']) ? 'selected' : '' ?>>
                            <?= esc($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($selectedKat)): ?>
                    <a href="<?= base_url('pelanggan/katalog') ?>" class="btn btn-outline-secondary rounded-3">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($units)): ?>
        <div class="col-12 text-center py-5">
            <div class="p-4 bg-white rounded-4 border d-inline-block text-muted">
                <i class="bi bi-camera fs-1 d-block mb-2 text-secondary"></i>
                <h5 class="fw-bold">Belum ada unit tersedia</h5>
                <p class="small mb-0">Semua unit pada kategori ini sedang disewa atau dalam perawatan.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($units as $u): ?>
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="card card-custom card-hover h-100 overflow-hidden d-flex flex-column">
                    <div class="p-4 bg-light text-center border-bottom position-relative" style="min-height: 140px;">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle position-absolute top-0 start-0 m-3">
                            <?= esc($u['nama_kategori']) ?>
                        </span>
                        <div class="mt-3">
                            <i class="bi bi-camera2 text-primary" style="font-size: 3.5rem;"></i>
                        </div>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="small text-uppercase tracking-wider text-muted fw-semibold mb-1"><?= esc($u['merk']) ?> • <?= esc($u['tipe']) ?></div>
                        <h5 class="fw-bold text-dark mb-2"><?= esc($u['nama_barang']) ?></h5>
                        <div class="mb-3">
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">
                                Unit: <?= esc($u['kode_unit'] ?? 'UNT-'.$u['id_unit']) ?>
                            </span>
                            <span class="badge bg-success-subtle text-success">
                                Kondisi: <?= ucfirst(esc($u['kondisi'])) ?>
                            </span>
                        </div>
                        <p class="text-muted small mb-4 flex-grow-1 line-clamp-2">
                            <?= esc($u['desc_barang'] ?? 'Unit dalam performa optimal, siap disewa untuk keperluan produksi Anda.') ?>
                        </p>

                        <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-muted d-block">Harga Sewa</span>
                                <span class="fw-bold text-dark fs-5">Rp <?= number_format($u['harga_sewa'], 0, ',', '.') ?></span>
                                <span class="text-muted small">/hari</span>
                            </div>
                            <a href="<?= base_url('pelanggan/sewa/' . $u['id_unit']) ?>" class="btn btn-primary-custom btn-sm px-3">
                                Sewa <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
