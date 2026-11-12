<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-dark mb-1">Katalog Kamera & Perlengkapan</h2>
            <p class="text-muted">Lihat kamera dan perlengkapan. Produk yang sedang disewa tetap dapat dilihat di katalog.</p>
        </div>
        <div class="col-md-5">
            <div class="d-flex justify-content-md-end gap-2 mb-2">
                <a href="<?= base_url('pelanggan/keranjang') ?>" class="btn btn-outline-primary rounded-3 position-relative">
                    <i class="bi bi-cart3 me-1"></i> Keranjang
                    <span class="badge bg-primary ms-1"><?= count(session()->get('keranjang_sewa') ?? []) ?></span>
                </a>
            </div>
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
    <?php if (empty($products)): ?>
        <div class="col-12 text-center py-5">
            <div class="p-4 bg-white rounded-4 border d-inline-block text-muted">
                <i class="bi bi-camera fs-1 d-block mb-2 text-secondary"></i>
                <h5 class="fw-bold">Belum ada produk katalog</h5>
                <p class="small mb-0">Belum ada produk yang terdaftar pada kategori ini.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($products as $u): ?>
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="card card-custom card-hover h-100 overflow-hidden d-flex flex-column">
                    <div class="p-4 bg-light text-center border-bottom position-relative" style="min-height: 140px;">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle position-absolute top-0 start-0 m-3">
                            <?= esc($u['nama_kategori']) ?>
                        </span>
                        <div class="mt-3 d-flex align-items-center justify-content-center" style="height: 150px;">
                            <?php if (!empty($u['gambar_url'])): ?>
                                <img src="<?= esc($u['gambar_url']) ?>" alt="<?= esc($u['nama_barang']) ?>"
                                     loading="lazy" decoding="async" class="img-fluid rounded-3"
                                     style="max-height: 145px; max-width: 100%; object-fit: contain;">
                            <?php else: ?>
                                <div class="text-center text-primary">
                                    <i class="bi bi-camera2" style="font-size: 3.5rem;"></i>
                                    <div class="small text-muted">Foto belum tersedia</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="small text-uppercase tracking-wider text-muted fw-semibold mb-1"><?= esc($u['merk']) ?> • <?= esc($u['tipe']) ?></div>
                        <h5 class="fw-bold text-dark mb-2"><?= esc($u['nama_barang']) ?></h5>
                        <div class="mb-3">
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">
                                <?= (int) $u['jumlah_tersedia'] ?> dari <?= (int) $u['total_unit'] ?> unit tersedia
                            </span>
                            <?php if ((int) $u['jumlah_tersedia'] > 0): ?>
                                <span class="badge bg-success-subtle text-success">Tersedia</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning">Sedang disewa / perawatan</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-2 flex-grow-1 line-clamp-2">
                            <?= esc($u['desc_barang'] ?? 'Produk untuk kebutuhan foto dan video Anda.') ?>
                        </p>
                        <?php if (!empty($u['spesifikasi'])): ?>
                            <p class="text-muted small mb-3 line-clamp-2"><?= esc($u['spesifikasi']) ?></p>
                        <?php endif; ?>

                        <div class="pt-3 border-top mt-auto">
                            <?php if (!empty($u['units'])): ?>
                                <label class="small text-muted mb-1">Pilih unit yang akan disewa</label>
                                <select class="form-select form-select-sm mb-3 pilihan-unit" data-price-target="harga-<?= (int) $u['id_inv'] ?>">
                                    <?php foreach ($u['units'] as $unit): ?>
                                        <option value="<?= (int) $unit['id_unit'] ?>" data-price="<?= (float) $unit['harga_sewa'] ?>">
                                            <?= esc($unit['kode_unit']) ?> — Rp <?= number_format($unit['harga_sewa'], 0, ',', '.') ?>/hari
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="d-flex align-items-end justify-content-between gap-2">
                                    <div>
                                        <span class="small text-muted d-block">Harga Sewa</span>
                                        <span class="fw-bold text-dark fs-5" id="harga-<?= (int) $u['id_inv'] ?>">Rp <?= number_format($u['units'][0]['harga_sewa'], 0, ',', '.') ?></span>
                                        <span class="text-muted small">/hari</span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <form action="<?= base_url('pelanggan/keranjang/tambah') ?>" method="POST" class="form-tambah-keranjang">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id_unit" value="<?= (int) $u['units'][0]['id_unit'] ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm px-3 w-100">
                                                <i class="bi bi-cart-plus me-1"></i> Tambah
                                            </button>
                                        </form>
                                        <a href="<?= base_url('pelanggan/sewa/' . (int) $u['units'][0]['id_unit']) ?>" class="btn btn-primary-custom btn-sm px-3 link-sewa-langsung">
                                            Sewa Langsung <i class="bi bi-chevron-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="small text-muted d-block">Harga Sewa</span>
                                        <span class="fw-bold text-muted">Belum tersedia</span>
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" disabled>
                                        <i class="bi bi-clock me-1"></i> Tidak tersedia
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pilihan-unit').forEach(function (select) {
        const card = select.closest('.card');
        const price = document.getElementById(select.dataset.priceTarget);
        const cartId = card.querySelector('.form-tambah-keranjang input[name="id_unit"]');
        const directLink = card.querySelector('.link-sewa-langsung');
        function updateUnit() {
            const option = select.options[select.selectedIndex];
            const amount = Number(option.dataset.price || 0);
            price.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            cartId.value = option.value;
            directLink.href = '<?= base_url('pelanggan/sewa') ?>/' + option.value;
        }
        select.addEventListener('change', updateUnit);
        updateUnit();
    });
});
</script>
<?= $this->endSection() ?>
