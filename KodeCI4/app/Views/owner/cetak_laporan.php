<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            background: #ffffff;
        }
        .header-kop {
            border-bottom: 3px double #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="p-4 p-md-5">

    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF Dokumen Ini
        </button>
        <button onclick="window.close()" class="btn btn-light rounded-pill px-4 border ms-2">Tutup</button>
    </div>

    <!-- Kop Surat Laporan -->
    <div class="header-kop d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-camera-fill text-primary me-2"></i> CameraRent.Pro</h2>
            <p class="mb-0 text-muted small">Kelompok 9 - Manajemen Inventory Sistem Sewa Camera (Capstone Project 4E)</p>
            <p class="mb-0 text-muted small">Jalan Fotografi Kreatif No. 9, Bandung • Telp: (022) 8899-7700</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0 text-uppercase">REKAPITULASI TRANSAKSI</h4>
            <div class="text-muted small">Periode: <b><?= date('d/m/Y', strtotime($tglAwal)) ?></b> s/d <b><?= date('d/m/Y', strtotime($tglAkhir)) ?></b></div>
        </div>
    </div>

    <!-- Rangkuman Keuangan -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="p-3 border rounded-3 bg-light">
                <div class="small text-muted text-uppercase fw-semibold">Pemasukan Sewa Terbayar (Lunas)</div>
                <h4 class="fw-bold text-primary mb-0 mt-1">Rp <?= number_format($totalPendapatanSewa, 0, ',', '.') ?></h4>
            </div>
        </div>
        <div class="col-6">
            <div class="p-3 border rounded-3 bg-light">
                <div class="small text-muted text-uppercase fw-semibold">Pemasukan Denda / Kerusakan</div>
                <h4 class="fw-bold text-danger mb-0 mt-1">Rp <?= number_format($totalDenda, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <table class="table table-bordered table-striped align-middle mb-4">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Tgl Sewa</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Status Bayar</th>
                <th>Total Sewa</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($laporan as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="font-monospace fw-bold">#INV-<?= str_pad($row['id_sewa'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_sewa'])) ?></td>
                        <td><?= esc($row['nama_pelanggan']) ?></td>
                        <td><?= esc($row['nama_kasir'] ?? '-') ?></td>
                        <td><?= esc($row['metode'] ?? 'QRIS') ?></td>
                        <td><?= strtoupper(esc($row['status_pembayaran'] ?? 'menunggu')) ?></td>
                        <td class="text-end fw-bold">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td class="text-end"><?= (!empty($row['denda']) && (float)$row['denda'] > 0) ? 'Rp ' . number_format($row['denda'], 0, ',', '.') : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold fs-6">
                <td colspan="7" class="text-end">TOTAL KESELURUHAN PENDAPATAN (SEWA + DENDA):</td>
                <td colspan="2" class="text-end text-primary">
                    Rp <?= number_format($totalPendapatanSewa + $totalDenda, 0, ',', '.') ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Owner & Kasir -->
    <div class="row mt-5 pt-4">
        <div class="col-6 text-center">
            <p class="mb-5 text-muted">Disiapkan oleh Kasir,</p>
            <p class="fw-bold mb-0 text-decoration-underline">( Petugas Kasir )</p>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5 text-muted">Mengetahui & Menyetujui,</p>
            <p class="fw-bold mb-0 text-decoration-underline">( Pemilik Usaha / Owner )</p>
        </div>
    </div>

</body>
</html>
