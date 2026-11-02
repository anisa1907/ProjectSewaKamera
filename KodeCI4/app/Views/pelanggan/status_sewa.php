<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Riwayat & Status Transaksi Sewa</h2>
    <p class="text-muted">Pantau status konfirmasi pemesanan, verifikasi pembayaran, dan cetak invoice QRIS Anda.</p>
</div>

<?php if(session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<?php if(session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<div class="card card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
                <tr>
                    <th class="ps-4">No. Transaksi</th>
                    <th>Tgl Sewa</th>
                    <th>Tgl Kembali</th>
                    <th>Jaminan</th>
                    <th>Total Biaya</th>
                    <th>Metode & Status Bayar</th>
                    <th>Status Sewa</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarSewa)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada riwayat transaksi sewa. <a href="<?= base_url('pelanggan/katalog') ?>" class="text-primary fw-semibold">Sewa sekarang</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarSewa as $s): ?>
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-dark">
                                #INV-<?= str_pad($s['id_sewa'], 5, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td><?= date('d M Y', strtotime($s['tanggal_sewa'])) ?></td>
                            <td><?= date('d M Y', strtotime($s['tanggal_kembali'])) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">
                                    <?= esc($s['jaminan']) ?>
                                </span>
                            </td>
                            <td class="fw-bold text-dark">
                                Rp <?= number_format($s['total_harga'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary-subtle text-secondary"><?= esc($s['metode'] ?? 'QRIS') ?></span>
                                    <?php $refund = $refundBySewa[$s['id_sewa']] ?? null; ?>
                                    <?php if ($s['status']==='dibatalkan' && $refund): ?>
                                        <?php $refundLabels=['perlu_verifikasi'=>'Bukti transfer diperiksa','menunggu_refund'=>'Menunggu refund 100%','selesai'=>'Dana dikembalikan','tidak_ada_dana'=>'Tidak ada dana diterima']; ?>
                                        <span class="badge bg-info-subtle text-dark"><?= esc($refundLabels[$refund['status']] ?? $refund['status']) ?></span>
                                    <?php elseif ($s['status']==='dibatalkan'): ?>
                                        <span class="badge bg-secondary">Pembayaran dibatalkan</span>
                                    <?php elseif ($s['status_pembayaran'] === 'terbayar'): ?>
                                        <span class="badge bg-success-subtle text-success">Terbayar</span>
                                    <?php elseif ($s['status_pembayaran'] === 'ditolak'): ?>
                                        <span class="badge bg-danger-subtle text-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning">Menunggu Kasir</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($s['status'] === 'berlangsung'): ?>
                                    <span class="badge bg-primary">Unit Sedang Disewa</span>
                                <?php elseif ($s['status'] === 'dikonfirmasi'): ?>
                                    <span class="badge bg-info-subtle text-info border">Siap Ambil Unit</span>
                                <?php elseif ($s['status'] === 'selesai'): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php elseif ($s['status'] === 'dibatalkan'): ?>
                                    <span class="badge bg-danger">Dibatalkan</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= base_url('pelanggan/invoice/' . $s['id_sewa']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-printer me-1"></i> Cetak Invoice / QRIS
                                </a>
                                <?php $p = $statusPengajuan[$s['id_sewa']] ?? null; ?>
                                <?php if ($p): ?>
                                    <div class="small mt-2">Pengajuan: <strong><?= esc($p['status']) ?></strong></div>
                                    <?php if ($p['status'] === 'ditolak'): ?><div class="small text-danger">Catatan kasir: <?= esc($p['catatan_kasir'] ?? '-') ?></div><?php endif ?>
                                <?php endif ?>
                                <?php if (in_array($s['status'], ['menunggu','dikonfirmasi'], true) && (!$p || $p['status'] === 'ditolak')): ?>
                                    <form action="<?= base_url('pelanggan/status-sewa/batalkan/' . $s['id_sewa']) ?>" method="post" class="mt-2 text-start">
                                        <?= csrf_field() ?>
                                        <textarea name="alasan" class="form-control form-control-sm mb-1" required minlength="10" maxlength="1000" placeholder="Alasan pembatalan (minimal 10 karakter)"></textarea>
                                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Kirim permohonan pembatalan kepada kasir?')">Ajukan Pembatalan</button>
                                    </form>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
