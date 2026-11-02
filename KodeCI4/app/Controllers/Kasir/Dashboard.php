<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\SewaModel;
use App\Models\UnitModel;
use App\Models\PembayaranModel;
use App\Models\MaintenanceModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $sewaModel        = new SewaModel();
        $unitModel        = new UnitModel();
        $pembayaranModel  = new PembayaranModel();
        $maintenanceModel = new MaintenanceModel();

        $totalTersedia    = $unitModel->where('status_unit', 'tersedia')->countAllResults();
        $totalDisewa      = $unitModel->where('status_unit', 'disewa')->countAllResults();
        $totalMaintenance = $unitModel->where('status_unit', 'maintenance')->countAllResults();

        $sewaMenungguBayar = $pembayaranModel->select('pembayaran.id_sewa')->join('sewa', 'sewa.id_sewa = pembayaran.id_sewa')->where('pembayaran.status', 'menunggu')->where('sewa.status !=', 'dibatalkan')->countAllResults();
        $sewaAktif         = $sewaModel->where('status', 'berlangsung')->countAllResults();

        $transaksiTerbaru  = $sewaModel->getSewaDetail();

        $pengajuanAktif = \Config\Database::connect()->table('pengajuan_pembatalan p')
            ->select('p.*, u.nama_user as nama_pelanggan')
            ->join('user u', 'u.id_user=p.id_pelanggan')
            ->where('p.status', 'menunggu')->orderBy('p.dibuat_pada', 'ASC')->get()->getResultArray();

        return view('kasir/dashboard', [
            'title'             => 'Dashboard Kasir - Operasional Sewa',
            'pengajuanAktif' => $pengajuanAktif,
            'totalTersedia'     => $totalTersedia,
            'totalDisewa'       => $totalDisewa,
            'totalMaintenance'  => $totalMaintenance,
            'sewaMenungguBayar' => $sewaMenungguBayar,
            'sewaAktif'         => $sewaAktif,
            'transaksiTerbaru'  => array_slice($transaksiTerbaru, 0, 5),
        ]);
    }
}
