<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Pengelolaan Transaksi Sewa & Pembayaran</h2>
    <p class="text-muted">Proses verifikasi pembayaran, serah terima unit beserta jaminan, dan penerimaan pengembalian.</p>
</div>

<div class="card card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="ps-4">No. Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Jadwal Sewa</th>
                    <th>Jaminan</th>
                    <th>Total & Metode</th>
                    <th>Status Bayar</th>
                    <th>Status Sewa</th>
                    <th class="text-end pe-4">Aksi Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarSewa)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada data transaksi penyewaan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarSewa as $s): ?>
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-dark">
                                #INV-<?= str_pad($s['id_sewa'], 5, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($s['nama_pelanggan']) ?></div>
                            </td>
                            <td>
                                <small class="d-block text-muted">Mulai: <?= date('d M Y', strtotime($s['tanggal_sewa'])) ?></small>
                                <small class="d-block text-muted">Kembali: <?= date('d M Y', strtotime($s['tanggal_kembali'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">
                                    <?= esc($s['jaminan']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp <?= number_format($s['total_harga'], 0, ',', '.') ?></div>
                                <span class="badge bg-light text-dark border"><?= esc($s['metode'] ?? 'QRIS') ?></span>
                            </td>
                            <td>
                                <?php if ($s['status_pembayaran'] === 'terbayar'): ?>
                                    <span class="badge bg-success-subtle text-success">Terbayar</span>
                                <?php elseif ($s['status_pembayaran'] === 'ditolak'): ?>
                                    <span class="badge bg-danger-subtle text-danger">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning">Menunggu Konfirmasi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($s['status'] === 'menunggu'): ?>
                                    <span class="badge bg-secondary">Menunggu</span>
                                <?php elseif ($s['status'] === 'dikonfirmasi'): ?>
                                    <span class="badge bg-info text-dark">Dikonfirmasi (Siap Ambil)</span>
                                <?php elseif ($s['status'] === 'berlangsung'): ?>
                                    <span class="badge bg-primary">Unit Sedang Disewa</span>
                                <?php elseif ($s['status'] === 'selesai'): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php elseif ($s['status'] === 'dibatalkan'): ?>
                                    <span class="badge bg-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?= base_url('pelanggan/invoice/' . $s['id_sewa']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Lihat Invoice">
                                        <i class="bi bi-printer"></i>
                                    </a>

                                    <!-- 1. Tombol Verifikasi Pembayaran jika Menunggu -->
                                    <?php if ($s['status_pembayaran'] === 'menunggu'): ?>
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalBayar<?= $s['id_sewa'] ?>">
                                            <i class="bi bi-check-lg me-1"></i> Verifikasi Bayar
                                        </button>
                                    <?php endif; ?>

                                    <!-- 2. Tombol Serah Terima Unit jika sudah bayar dan siap ambil -->
                                    <?php if ($s['status_pembayaran'] === 'terbayar' && $s['status'] === 'dikonfirmasi'): ?>
                                        <form action="<?= base_url('kasir/transaksi/serah-unit/' . $s['id_sewa']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah jaminan <?= esc($s['jaminan']) ?> telah diterima dan unit siap diserahkan?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-box-arrow-right me-1"></i> Serahkan Unit
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- 3. Tombol Pengembalian Unit jika status 'berlangsung' -->
                                    <?php if ($s['status'] === 'berlangsung'): ?>
                                        <button class="btn btn-warning btn-sm text-dark" data-bs-toggle="modal" data-bs-target="#modalRetur<?= $s['id_sewa'] ?>">
                                            <i class="bi bi-box-arrow-in-left me-1"></i> Terima Kembali
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Modal Verifikasi Pembayaran -->
                                <div class="modal fade text-start" id="modalBayar<?= $s['id_sewa'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= base_url('kasir/transaksi/verifikasi-bayar/' . $s['id_sewa']) ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Verifikasi Pembayaran #INV-<?= str_pad($s['id_sewa'], 5, '0', STR_PAD_LEFT) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Pelanggan: <b><?= esc($s['nama_pelanggan']) ?></b></p>
                                                    <p>Nominal: <b class="text-primary fs-5">Rp <?= number_format($s['total_harga'], 0, ',', '.') ?></b></p>
                                                    <p>Metode: <span class="badge bg-secondary"><?= esc($s['metode'] ?? 'QRIS') ?></span></p>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Pilih Keputusan Kasir:</label>
                                                        <select name="status_pembayaran" class="form-select" required>
                                                            <option value="terbayar">Terbayar (Konfirmasi Lunas)</option>
                                                            <option value="ditolak">Ditolak (Pembayaran Gagal / Palsu)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Pengembalian Unit -->
                                <div class="modal fade text-start" id="modalRetur<?= $s['id_sewa'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= base_url('kasir/transaksi/pengembalian/' . $s['id_sewa']) ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Penerimaan Pengembalian Unit</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info py-2 small mb-3">
                                                        Jaminan <b><?= esc($s['jaminan']) ?></b> wajib dikembalikan ke pelanggan jika semua urusan selesai.
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Kondisi Fisik Unit Saat Kembali:</label>
                                                        <select name="kondisi" class="form-select" required>
                                                            <option value="baik">Kondisi Baik (Normal tanpa cacat)</option>
                                                            <option value="rusak_ringan">Rusak Ringan (Lecet / Goresan)</option>
                                                            <option value="rusak_berat">Rusak Berat (Error / Pecah / Tidak Berfungsi)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Denda Keterlambatan / Kerusakan (Rp):</label>
                                                        <input type="number" name="denda" class="form-control" value="0" min="0" required>
                                                        <small class="text-muted">Isi 0 jika tidak ada denda keterlambatan/kerusakan.</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Catatan Pemeriksaan Kasir:</label>
                                                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan kelengkapan aksesoris, baterai, memory..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning">Konfirmasi Pengembalian</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
