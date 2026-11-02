<?php $pengajuanAktif = $pengajuanAktif ?? []; ?>
<div class="alert alert-warning" id="alert-pembatalan" role="status">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <strong><i class="bi bi-bell-fill me-1"></i> Pengajuan pembatalan menunggu: <span id="jumlah-pembatalan"><?= count($pengajuanAktif) ?></span></strong>
        <a href="<?= base_url('kasir/histori') ?>" class="btn btn-outline-dark btn-sm">Buka semua histori</a>
    </div>
    <div id="daftar-pengajuan">
    <?php if (!$pengajuanAktif): ?><p class="mb-0 small">Tidak ada pengajuan pembatalan saat ini.</p><?php endif; ?>
    <?php foreach ($pengajuanAktif as $p): ?>
        <div class="bg-white rounded border p-3 mb-2" data-pengajuan="<?= (int)$p['id_pengajuan'] ?>">
            <div><strong>#INV-<?= str_pad($p['id_sewa'], 5, '0', STR_PAD_LEFT) ?> — <?= esc($p['nama_pelanggan']) ?></strong></div>
            <div class="small mb-2">Alasan: <?= esc($p['alasan']) ?></div>
            <form method="post" action="<?= base_url('kasir/pembatalan/keputusan/'.$p['id_pengajuan']) ?>" class="d-flex flex-wrap gap-2 align-items-start">
                <?= csrf_field() ?>
                <input class="form-control form-control-sm" style="max-width:320px" name="catatan" placeholder="Alasan penolakan (wajib jika ditolak)">
                <button type="submit" class="btn btn-success btn-sm" name="keputusan" value="setujui" onclick="return confirm('Setujui pembatalan? Periksa refund secara manual jika sudah dibayar.')">Setujui</button>
                <button type="submit" class="btn btn-danger btn-sm" name="keputusan" value="tolak" onclick="return this.form.elements['catatan'].value.trim() !== '' ? confirm('Tolak pengajuan?') : (alert('Isi alasan penolakan dahulu.'), false)">Tolak</button>
            </form>
        </div>
    <?php endforeach; ?>
    </div>
    <div id="peringatan-pengajuan-baru" class="small text-danger fw-bold d-none">Ada perubahan pengajuan pembatalan. Muat ulang halaman untuk menampilkan tindakan terbaru.</div>
</div>
<script>
(function () {
    const count = document.getElementById('jumlah-pembatalan');
    const warning = document.getElementById('peringatan-pengajuan-baru');
    const known = new Set(Array.from(document.querySelectorAll('[data-pengajuan]')).map(el => Number(el.dataset.pengajuan)));
    const initialCount = Number(count.textContent);
    async function refresh() {
        if (document.hidden) return;
        try {
            const response = await fetch(<?= json_encode(base_url('kasir/notifikasi-pembatalan')) ?>, {credentials: 'same-origin', headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok) return;
            const data = await response.json();
            count.textContent = data.jumlah;
            const changed = data.jumlah !== initialCount || data.pengajuan.some(p => !known.has(p.id_pengajuan));
            if (changed) {
                warning.classList.remove('d-none');
                document.querySelectorAll('[data-verifikasi-sewa]').forEach(btn => btn.disabled = true);
            }
        } catch (error) { /* retry on next poll */ }
    }
    setInterval(refresh, 20000);
})();
</script>
