<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\SewaModel;
use App\Models\PembayaranModel;
use App\Models\PengembalianModel;

class Laporan extends BaseController
{
    protected $sewaModel;
    protected $pembayaranModel;
    protected $pengembalianModel;

    public function __construct()
    {
        $this->sewaModel         = new SewaModel();
        $this->pembayaranModel   = new PembayaranModel();
        $this->pengembalianModel = new PengembalianModel();
    }

    public function index()
    {
        $tglAwal  = $this->request->getGet('tgl_awal') ?? date('Y-m-01');
        $tglAkhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');

        $builder = $this->sewaModel->select('sewa.*, pelanggan.nama_user as nama_pelanggan, kasir.nama_user as nama_kasir, pembayaran.metode, pembayaran.status as status_pembayaran, pembayaran.tanggal_bayar, pengembalian.denda, pengembalian.kondisi as kondisi_kembali')
                                   ->join('user as pelanggan', 'pelanggan.id_user = sewa.id_pelanggan')
                                   ->join('user as kasir', 'kasir.id_user = sewa.id_user', 'left')
                                   ->join('pembayaran', 'pembayaran.id_sewa = sewa.id_sewa', 'left')
                                   ->join('pengembalian', 'pengembalian.id_sewa = sewa.id_sewa', 'left')
                                   ->where('sewa.tanggal_sewa >=', $tglAwal)
                                   ->where('sewa.tanggal_sewa <=', $tglAkhir);

        $laporan = $builder->orderBy('sewa.id_sewa', 'DESC')->findAll();

        $totalPendapatanSewa = 0;
        $totalDenda          = 0;
        foreach ($laporan as $row) {
            if ($row['status_pembayaran'] === 'terbayar') {
                $totalPendapatanSewa += (float)$row['total_harga'];
            }
            if (!empty($row['denda'])) {
                $totalDenda += (float)$row['denda'];
            }
        }

        return view('owner/laporan', [
            'title'               => 'Laporan Transaksi & Keuangan Sewa',
            'laporan'             => $laporan,
            'tglAwal'             => $tglAwal,
            'tglAkhir'            => $tglAkhir,
            'totalPendapatanSewa' => $totalPendapatanSewa,
            'totalDenda'          => $totalDenda,
        ]);
    }

    public function cetak()
    {
        $tglAwal  = $this->request->getGet('tgl_awal') ?? date('Y-m-01');
        $tglAkhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');

        $builder = $this->sewaModel->select('sewa.*, pelanggan.nama_user as nama_pelanggan, kasir.nama_user as nama_kasir, pembayaran.metode, pembayaran.status as status_pembayaran, pembayaran.tanggal_bayar, pengembalian.denda, pengembalian.kondisi as kondisi_kembali')
                                   ->join('user as pelanggan', 'pelanggan.id_user = sewa.id_pelanggan')
                                   ->join('user as kasir', 'kasir.id_user = sewa.id_user', 'left')
                                   ->join('pembayaran', 'pembayaran.id_sewa = sewa.id_sewa', 'left')
                                   ->join('pengembalian', 'pengembalian.id_sewa = sewa.id_sewa', 'left')
                                   ->where('sewa.tanggal_sewa >=', $tglAwal)
                                   ->where('sewa.tanggal_sewa <=', $tglAkhir);

        $laporan = $builder->orderBy('sewa.id_sewa', 'DESC')->findAll();

        $totalPendapatanSewa = 0;
        $totalDenda          = 0;
        foreach ($laporan as $row) {
            if ($row['status_pembayaran'] === 'terbayar') {
                $totalPendapatanSewa += (float)$row['total_harga'];
            }
            if (!empty($row['denda'])) {
                $totalDenda += (float)$row['denda'];
            }
        }

        return view('owner/cetak_laporan', [
            'title'               => 'Cetak Rekapitulasi Laporan Transaksi Sewa',
            'laporan'             => $laporan,
            'tglAwal'             => $tglAwal,
            'tglAkhir'            => $tglAkhir,
            'totalPendapatanSewa' => $totalPendapatanSewa,
            'totalDenda'          => $totalDenda,
        ]);
    }
}
