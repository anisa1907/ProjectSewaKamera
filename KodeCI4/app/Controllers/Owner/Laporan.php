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

        $tahun = (string)$this->request->getGet('tahun');
        $bulan = (string)$this->request->getGet('bulan');
        $status = (string)$this->request->getGet('status');
        $unit = (int)$this->request->getGet('unit');
        if (preg_match('/^\d{4}$/',$tahun)) $builder->where('YEAR(sewa.tanggal_sewa)',(int)$tahun);
        if (preg_match('/^(0?[1-9]|1[0-2])$/',$bulan)) $builder->where('MONTH(sewa.tanggal_sewa)',(int)$bulan);
        if (in_array($status,['menunggu','dikonfirmasi','berlangsung','selesai','dibatalkan'],true)) $builder->where('sewa.status',$status);
        if ($unit > 0) $builder->where('EXISTS (SELECT 1 FROM detail_sewa ds WHERE ds.id_sewa = sewa.id_sewa AND ds.id_unit = '. $unit .')',null,false);

        $laporan = $builder->orderBy('sewa.id_sewa', 'DESC')->findAll();

        $totalPendapatanSewa = 0;
        $totalDenda          = 0;
        foreach ($laporan as $row) {
            if ($row['status_pembayaran'] === 'terbayar' && $row['status'] !== 'dibatalkan') {
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
            'unitOptions' => (new \App\Models\UnitModel())->getUnitsFull(),
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

        $tahun = (string)$this->request->getGet('tahun');
        $bulan = (string)$this->request->getGet('bulan');
        $status = (string)$this->request->getGet('status');
        $unit = (int)$this->request->getGet('unit');
        if (preg_match('/^\d{4}$/',$tahun)) $builder->where('YEAR(sewa.tanggal_sewa)',(int)$tahun);
        if (preg_match('/^(0?[1-9]|1[0-2])$/',$bulan)) $builder->where('MONTH(sewa.tanggal_sewa)',(int)$bulan);
        if (in_array($status,['menunggu','dikonfirmasi','berlangsung','selesai','dibatalkan'],true)) $builder->where('sewa.status',$status);
        if ($unit > 0) $builder->where('EXISTS (SELECT 1 FROM detail_sewa ds WHERE ds.id_sewa = sewa.id_sewa AND ds.id_unit = '. $unit .')',null,false);

        $laporan = $builder->orderBy('sewa.id_sewa', 'DESC')->findAll();

        $totalPendapatanSewa = 0;
        $totalDenda          = 0;
        foreach ($laporan as $row) {
            if ($row['status_pembayaran'] === 'terbayar' && $row['status'] !== 'dibatalkan') {
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
            'unitOptions' => (new \App\Models\UnitModel())->getUnitsFull(),
            'totalPendapatanSewa' => $totalPendapatanSewa,
            'totalDenda'          => $totalDenda,
        ]);
    }
}
