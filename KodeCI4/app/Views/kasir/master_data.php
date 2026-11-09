<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert"><?= esc(session()->getFlashdata('warning')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Pengelolaan Master Data</h2>
    <p class="text-muted">Kelola Kategori, Produk Inventori, dan Unit Fisik Individual Kamera/Drone.</p>
</div>

<!-- Tabs Nav -->
<ul class="nav nav-pills mb-4 gap-2" id="masterTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active rounded-pill px-4" id="unit-tab" data-bs-toggle="pill" data-bs-target="#tab-unit" type="button">
            <i class="bi bi-camera me-1"></i> Data Unit Fisik (<?= count($units) ?>)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" id="inv-tab" data-bs-toggle="pill" data-bs-target="#tab-inv" type="button">
            <i class="bi bi-box-seam me-1"></i> Master Produk Inventory (<?= count($inventory) ?>)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" id="kat-tab" data-bs-toggle="pill" data-bs-target="#tab-kat" type="button">
            <i class="bi bi-tags me-1"></i> Kategori (<?= count($kategori) ?>)
        </button>
    </li>
</ul>

<div class="tab-content" id="masterTabsContent">
    <!-- TAB 1: UNIT FISIK -->
    <div class="tab-pane fade show active" id="tab-unit" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">Tambah Unit Fisik Baru</h5>
                    <form action="<?= base_url('kasir/master-data/unit') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Produk Barang</label>
                            <select name="id_inv" class="form-select rounded-3" required>
                                <option value="">Pilih Produk...</option>
                                <?php foreach ($inventory as $iv): ?>
                                    <option value="<?= $iv['id_inv'] ?>"><?= esc($iv['nama_barang']) ?> (<?= esc($iv['kode_barang']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kode Unit Fisik (Contoh: Cam-Eos90D-03)</label>
                            <input type="text" name="kode_unit" class="form-control rounded-3" placeholder="Kode identitas unit" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Harga Sewa per Hari (Rp)</label>
                            <input type="number" name="harga_sewa" class="form-control rounded-3" placeholder="150000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kondisi Awal</label>
                            <select name="kondisi" class="form-select rounded-3">
                                <option value="baik">Baik (Optimal)</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Simpan Unit Fisik</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom p-0 overflow-hidden">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">Daftar Semua Unit Fisik</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Kode Unit</th>
                                    <th>Nama Produk</th>
                                    <th>Harga Sewa</th>
                                    <th>Kondisi</th>
                                    <th>Status Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($units as $u): ?>
                                    <tr>
                                        <td class="ps-4 font-monospace fw-bold text-dark"><?= esc($u['kode_unit'] ?? 'UNT-'.$u['id_unit']) ?></td>
                                        <td>
                                            <div class="fw-semibold"><?= esc($u['nama_barang']) ?></div>
                                            <small class="text-muted"><?= esc($u['nama_kategori']) ?></small>
                                        </td>
                                        <td>Rp <?= number_format($u['harga_sewa'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= ucfirst(esc($u['kondisi'])) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($u['status_unit'] === 'tersedia'): ?>
                                                <span class="badge bg-success-subtle text-success">Tersedia</span>
                                            <?php elseif ($u['status_unit'] === 'disewa'): ?>
                                                <span class="badge bg-primary-subtle text-primary">Disewa</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger">Maintenance</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: INVENTORY -->
    <div class="tab-pane fade" id="tab-inv" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">Tambah Produk Inventori</h5>
                    <form action="<?= base_url('kasir/master-data/inventory') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kategori</label>
                            <select name="id_kategori" class="form-select rounded-3" required>
                                <option value="">Pilih Kategori...</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= esc($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kode Barang (SKU)</label>
                            <input type="text" name="kode_barang" class="form-control rounded-3" placeholder="Contoh: BRG-SONY-FX3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control rounded-3" placeholder="Contoh: Sony Cinema Line FX3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Merk</label>
                            <input type="text" name="merk" class="form-control rounded-3" placeholder="Contoh: Sony" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tipe / Spesifikasi</label>
                            <input type="text" name="tipe" class="form-control rounded-3" placeholder="Full Frame Cinema Camera">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="3"></textarea>
                            <label class="form-label small fw-semibold mt-2">Detail Spesifikasi Item</label>
                            <textarea name="spesifikasi" class="form-control rounded-3" rows="3" placeholder="Sensor, resolusi, lensa, baterai, kelengkapan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Foto Produk (opsional)</label>
                            <input type="file" name="gambar_produk" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">JPG, PNG, atau WebP. Maksimal 2 MB. File disimpan di folder public/uploads/produk, bukan database.</small>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Simpan Produk</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom p-0 overflow-hidden">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">Daftar Produk Inventori</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Kode</th>
                                    <th>Nama Produk</th>
                                    <th>Merk & Tipe</th>
                                    <th>Foto Produk</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inventory as $iv): ?>
                                    <tr>
                                        <td class="ps-4 font-monospace fw-bold"><?= esc($iv['kode_barang']) ?></td>
                                        <td class="fw-bold"><?= esc($iv['nama_barang']) ?></td>
                                        <td><?= esc($iv['merk']) ?> - <?= esc($iv['tipe']) ?></td>
                                        <td>
                                            <form action="<?= base_url('kasir/master-data/inventory/' . $iv['id_inv'] . '/gambar') ?>" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-1">
                                                <?= csrf_field() ?>
                                                <input type="file" name="gambar_produk" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp" required>
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Upload / Ganti</button>
                                            </form>
                                        </td>
                                        <td><span class="badge bg-secondary-subtle text-secondary"><?= esc($iv['nama_kategori']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: KATEGORI -->
    <div class="tab-pane fade" id="tab-kat" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">Tambah Kategori Baru</h5>
                    <form action="<?= base_url('kasir/master-data/kategori') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control rounded-3" placeholder="Contoh: Lighting & Audio" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control rounded-3" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Simpan Kategori</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom p-0 overflow-hidden">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">Daftar Kategori</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kategori as $kat): ?>
                                    <tr>
                                        <td class="ps-4 font-monospace"><?= $kat['id_kategori'] ?></td>
                                        <td class="fw-bold"><?= esc($kat['nama_kategori']) ?></td>
                                        <td class="text-muted small"><?= esc($kat['deskripsi']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
