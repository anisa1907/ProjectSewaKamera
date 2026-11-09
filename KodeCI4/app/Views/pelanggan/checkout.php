<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="<?= base_url('pelanggan/' . (!empty($fromCart) ? 'keranjang' : 'katalog')) ?>" class="btn btn-outline-secondary btn-sm rounded-pill mb-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
        </a>

        <div class="card card-custom p-4 p-md-5">
            <div class="border-bottom pb-3 mb-4">
                <span class="badge bg-primary-subtle text-primary mb-2">Formulir Checkout</span>
                <h3 class="fw-bold text-dark mb-1">Konfirmasi Pemesanan Unit</h3>
                <p class="text-muted small">Lengkapi tanggal sewa, pilihan jaminan, dan metode pembayaran.</p>
            </div>

            <!-- Detail Barang Terpilih -->
            <?php foreach ($units as $item): ?>
                <div class="p-3 bg-light rounded-4 mb-2 border d-flex gap-3 align-items-center">
                    <div class="p-3 bg-white rounded-3 border text-primary"><i class="bi bi-camera-fill fs-2"></i></div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><?= esc($item['nama_barang']) ?></h5>
                        <div class="small text-muted">Kode Unit: <b><?= esc($item['kode_unit']) ?></b> • Kategori: <?= esc($item['nama_kategori']) ?></div>
                        <div class="fw-bold text-primary mt-1">Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?> <span class="text-muted fw-normal small">/hari</span></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <form action="<?= base_url('pelanggan/sewa/process') ?>" method="POST" id="formSewa">
                <?= csrf_field() ?>
                <?php foreach ($units as $item): ?>
                    <input type="hidden" name="id_unit[]" value="<?= esc($item['id_unit']) ?>">
                <?php endforeach; ?>
                <input type="hidden" id="hargaPerHari" value="<?= esc(array_sum(array_column($units, 'harga_sewa'))) ?>">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Tanggal Mulai Sewa</label>
                        <input type="date" name="tanggal_sewa" id="tanggalSewa" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Tanggal Pengembalian</label>
                        <input type="date" name="tanggal_kembali" id="tanggalKembali" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Jenis Jaminan (Diserahkan ke Kasir)</label>
                        <select name="jaminan" class="form-select rounded-3" required>
                            <option value="KTP">KTP Asli</option>
                            <option value="KK">Kartu Keluarga (KK)</option>
                            <option value="Akta">Akta Kelahiran</option>
                        </select>
                        <small class="text-muted d-block mt-1">Jaminan fisik akan diverifikasi kasir saat pengambilan unit.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Metode Pembayaran</label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check p-3 border rounded-3 flex-fill bg-light">
                                <input class="form-check-input" type="radio" name="metode" id="metodeQRIS" value="QRIS" checked>
                                <label class="form-check-label fw-bold text-dark d-block" for="metodeQRIS">
                                    <i class="bi bi-qr-code-scan me-1 text-primary"></i> QRIS (Instan)
                                </label>
                                <small class="text-muted">Cetak barcode QRIS & scan</small>
                            </div>
                            <div class="form-check p-3 border rounded-3 flex-fill bg-light">
                                <input class="form-check-input" type="radio" name="metode" id="metodeCash" value="Cash">
                                <label class="form-check-label fw-bold text-dark d-block" for="metodeCash">
                                    <i class="bi bi-cash me-1 text-success"></i> Cash di Kasir
                                </label>
                                <small class="text-muted">Bayar saat ambil unit</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estimasi Biaya -->
                <div class="p-3 bg-primary-subtle rounded-3 border border-primary-subtle mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary small">Durasi Sewa:</span>
                        <span class="fw-bold text-dark" id="durasiText">1 Hari</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Total Biaya Sewa:</span>
                        <span class="fw-bold text-primary fs-4" id="totalHargaText">Rp <?= number_format(array_sum(array_column($units, 'harga_sewa')), 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('pelanggan/' . (!empty($fromCart) ? 'keranjang' : 'katalog')) ?>" class="btn btn-light rounded-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-primary-custom rounded-3 px-4 py-2">
                        Ajukan Pemesanan Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateDurasi() {
        const startInput = document.getElementById('tanggalSewa');
        const endInput = document.getElementById('tanggalKembali');
        const start = new Date(startInput.value + 'T00:00:00');
        let end = new Date(endInput.value + 'T00:00:00');
        const harga = parseFloat(document.getElementById('hargaPerHari').value);
        const minEnd = new Date(start);
        minEnd.setDate(minEnd.getDate() + 1);
        endInput.min = minEnd.toISOString().slice(0, 10);
        if (end <= start) {
            end = minEnd;
            endInput.value = end.toISOString().slice(0, 10);
        }
        const diffDays = Math.max(1, Math.round((end - start) / 86400000));
        const total = diffDays * harga;
        document.getElementById('durasiText').innerText = diffDays + ' Hari';
        document.getElementById('totalHargaText').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
    document.getElementById('tanggalSewa').addEventListener('change', updateDurasi);
    document.getElementById('tanggalKembali').addEventListener('change', updateDurasi);
    updateDurasi();
</script>
<?= $this->endSection() ?>
