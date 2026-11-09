<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2 class="fw-bold mb-1">Keranjang Sewa</h2><p class="text-muted mb-0">Periksa unit yang ingin disewa sebelum melanjutkan checkout.</p></div>
    <a href="<?= base_url('pelanggan/katalog') ?>" class="btn btn-outline-secondary"><i class="bi bi-plus-lg me-1"></i>Tambah Unit</a>
</div>
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
<div class="card card-custom p-0 overflow-hidden">
    <?php if (empty($items)): ?>
        <div class="text-center py-5"><i class="bi bi-cart-x fs-1 text-muted"></i><h5 class="fw-bold mt-3">Keranjang masih kosong</h5><p class="text-muted">Tambahkan kamera atau perlengkapan dari katalog.</p><a href="<?= base_url('pelanggan/katalog') ?>" class="btn btn-primary-custom">Lihat Katalog</a></div>
    <?php else: ?>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th class="ps-4">Unit</th><th>Kategori</th><th>Harga / Hari</th><th class="text-end pe-4">Aksi</th></tr></thead><tbody>
        <?php $totalHarian = 0; foreach ($items as $item): $totalHarian += (float)$item['harga_sewa']; ?>
            <tr><td class="ps-4"><strong><?= esc($item['nama_barang']) ?></strong><div class="small text-muted"><?= esc($item['merk'].' '.$item['tipe']) ?> · <?= esc($item['kode_unit']) ?></div></td><td><?= esc($item['nama_kategori']) ?></td><td>Rp <?= number_format($item['harga_sewa'],0,',','.') ?></td><td class="text-end pe-4"><form method="POST" action="<?= base_url('pelanggan/keranjang/hapus/'.$item['id_unit']) ?>"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus unit dari keranjang?')"><i class="bi bi-trash"></i> Hapus</button></form></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
        <div class="p-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-3"><div><div class="small text-muted">Estimasi total harga per hari</div><div class="fs-4 fw-bold text-primary">Rp <?= number_format($totalHarian,0,',','.') ?></div><small class="text-muted">Total akhir dihitung berdasarkan tanggal sewa saat checkout.</small></div><div class="d-flex gap-2"><form method="POST" action="<?= base_url('pelanggan/keranjang/kosongkan') ?>"><?= csrf_field() ?><button class="btn btn-outline-danger" onclick="return confirm('Kosongkan seluruh keranjang?')">Kosongkan</button></form><a href="<?= base_url('pelanggan/checkout') ?>" class="btn btn-primary-custom">Lanjut Checkout <i class="bi bi-arrow-right ms-1"></i></a></div></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
