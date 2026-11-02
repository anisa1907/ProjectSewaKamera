<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<?php if(session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<?php if(session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<h4>Pengajuan Pembatalan Pelanggan</h4>
<div class="table-responsive mb-4"><table class="table table-bordered"><thead><tr><th>Transaksi</th><th>Pelanggan</th><th>Alasan</th><th>Tanggal</th><th>Keputusan Kasir</th></tr></thead><tbody>
<?php foreach ($pengajuan as $p): ?><tr><td>#<?= esc($p['id_sewa']) ?></td><td><?= esc($p['nama_pelanggan']) ?></td><td><?= esc($p['alasan']) ?></td><td><?= esc($p['dibuat_pada']) ?></td><td>
<form method="post" action="<?= base_url('kasir/pembatalan/keputusan/'.$p['id_pengajuan']) ?>">
<?= csrf_field() ?><textarea class="form-control form-control-sm mb-1" name="catatan" placeholder="Alasan penolakan (wajib jika ditolak)"></textarea>
<button class="btn btn-sm btn-success" name="keputusan" value="setujui" onclick="return confirm('Setujui pembatalan ini? Periksa pembayaran/refund secara manual.')">Setujui</button>
<button class="btn btn-sm btn-danger" name="keputusan" value="tolak" onclick="return confirm('Tolak pengajuan ini?')">Tolak</button>
</form></td></tr><?php endforeach ?>
<?php if (!$pengajuan): ?><tr><td colspan="5" class="text-center">Tidak ada pengajuan menunggu.</td></tr><?php endif ?>
</tbody></table></div>
<h4>Pemeriksaan Pembayaran & Refund Pembatalan</h4>
<p class="text-muted">Refund 100% hanya dilakukan setelah dana dipastikan masuk. Bukti transfer pelanggan bukan bukti dana sudah diterima.</p>
<div class="table-responsive mb-4"><table class="table table-bordered align-middle"><thead><tr><th>Sewa</th><th>Pelanggan</th><th>Nominal</th><th>Status</th><th>Tindakan</th></tr></thead><tbody>
<?php foreach ($refunds as $r): ?><tr><td>#<?= esc($r['id_sewa']) ?></td><td><?= esc($r['nama_pelanggan']) ?></td><td>Rp <?= number_format($r['jumlah'],0,',','.') ?></td><td><?= esc($r['status']) ?></td><td>
<form method="post" enctype="multipart/form-data" action="<?= base_url('kasir/refund/proses/'.$r['id_refund']) ?>">
<?= csrf_field() ?><input name="catatan" class="form-control form-control-sm mb-1" placeholder="Catatan pemeriksaan / nomor referensi refund">
<?php if ($r['status']==='perlu_verifikasi'): ?>
<button class="btn btn-sm btn-primary" name="aksi" value="dana_diterima" onclick="return confirm('Sudah cocokkan mutasi rekening dan pastikan dana diterima?')">Dana diterima → Refund 100%</button>
<button class="btn btn-sm btn-outline-secondary" name="aksi" value="dana_tidak_diterima" onclick="return confirm('Yakin dana tidak masuk setelah pemeriksaan?')">Dana tidak diterima</button>
<?php else: ?>
<label class="small">Bukti transfer refund (JPG/PNG/PDF, maks 2 MB)</label><input type="file" name="bukti_refund" accept="image/jpeg,image/png,application/pdf" class="form-control form-control-sm mb-1" required>
<button class="btn btn-sm btn-success" name="aksi" value="refund_selesai" onclick="return confirm('Uang sudah benar-benar ditransfer kembali kepada pelanggan?')">Konfirmasi Refund Selesai</button>
<?php endif ?></form></td></tr><?php endforeach ?>
<?php if (!$refunds): ?><tr><td colspan="5" class="text-center">Tidak ada refund yang perlu ditangani.</td></tr><?php endif ?>
</tbody></table></div>
<h2>Histori & Pengembalian</h2><p>Seluruh transaksi tersimpan permanen, termasuk transaksi lintas hari.</p>
<form method="get" class="row g-2 mb-3"><div class="col"><input class="form-control" name="tahun" placeholder="Tahun (2026)" value="<?= esc($_GET['tahun'] ?? '') ?>"></div><div class="col"><select class="form-select" name="bulan"><option value="">Semua bulan</option><?php for($i=1;$i<=12;$i++): ?><option value="<?= $i ?>" <?= ($_GET['bulan']??'')==(string)$i?'selected':'' ?>><?= $i ?></option><?php endfor ?></select></div><div class="col"><select class="form-select" name="status"><option value="">Semua status</option><?php foreach(['menunggu','dikonfirmasi','berlangsung','selesai','dibatalkan'] as $s): ?><option value="<?= $s ?>" <?= ($_GET['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach ?></select></div><div class="col"><button class="btn btn-primary">Filter</button></div></form>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Unit Disewa</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td>#<?= esc($r['id_sewa']) ?></td><td><?= esc($r['tanggal_sewa']) ?></td><td><?= esc($r['nama_pelanggan']) ?></td><td><?php foreach($r['items'] as $item): ?><?= esc($item['nama_barang'].' / '.$item['kode_unit']) ?><br><?php endforeach ?></td><td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td><td><?= esc($r['status']) ?><?php if($r['status']==='dibatalkan'): ?><small class="d-block text-danger">Alasan: <?= esc($r['alasan_pembatalan'] ?: '-') ?></small><?php endif ?></td><td><?php if($r['status']==='berlangsung'): ?><form method="post" action="<?= base_url('kasir/transaksi/pengembalian/'.$r['id_sewa']) ?>"><?= csrf_field() ?><select name="kondisi" class="form-select form-select-sm"><option value="baik">Baik</option><option value="rusak_ringan">Rusak ringan</option><option value="rusak_berat">Rusak berat</option></select><input name="denda" type="number" min="0" step="100" value="0" class="form-control form-control-sm" placeholder="Denda"><input name="keterangan" class="form-control form-control-sm" placeholder="Catatan"><button class="btn btn-sm btn-primary mt-1" onclick="return confirm('Konfirmasi pengembalian?')">Kembalikan</button></form><?php endif ?></td></tr><?php endforeach ?>
<?php if(!$rows): ?><tr><td colspan="7" class="text-center">Tidak ada transaksi pada filter ini.</td></tr><?php endif ?></tbody></table></div>
<?= $this->endSection() ?>