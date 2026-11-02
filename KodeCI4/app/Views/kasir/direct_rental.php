<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<?php if(session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<?php if(session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<h2>Sewa Langsung (Kasir)</h2><p>Tarif dihitung per 24 jam, dibulatkan ke atas. Pembayaran langsung dicatat terbayar setelah diterima kasir.</p>
<form method="post" action="<?= base_url('kasir/sewa-langsung') ?>" class="card p-4">
<?= csrf_field() ?>
<label class="form-label">Pelanggan terdaftar</label><select required name="id_pelanggan" class="form-select mb-3"><option value="">Pilih pelanggan</option><?php foreach($pelanggan as $u): ?><option value="<?= esc($u['id_user']) ?>"><?= esc($u['nama_user']) ?></option><?php endforeach ?></select>
<label>Unit yang disewa (boleh lebih dari satu)</label><div class="row mb-3"><?php foreach($units as $u): ?><div class="col-md-6"><label class="border rounded p-2 d-block"><input type="checkbox" name="unit_ids[]" value="<?= esc($u['id_unit']) ?>"> <?= esc($u['nama_barang'].' - '.$u['kode_unit']) ?> (Rp <?= number_format($u['harga_sewa'],0,',','.') ?>/hari)</label></div><?php endforeach ?></div>
<label>Mulai</label><input required type="datetime-local" class="form-control mb-3" name="waktu_mulai" value="<?= date('Y-m-d\TH:i') ?>">
<label>Selesai</label><input required type="datetime-local" class="form-control mb-3" name="waktu_selesai">
<label>Jaminan</label><select class="form-select mb-3" name="jaminan"><option>KTP</option><option>KK</option><option>Akta</option></select>
<label>Pembayaran</label><select class="form-select mb-3" name="metode"><option>Cash</option><option>QRIS</option></select>
<button class="btn btn-primary">Simpan dan Serahkan Unit</button></form>
<?= $this->endSection() ?>