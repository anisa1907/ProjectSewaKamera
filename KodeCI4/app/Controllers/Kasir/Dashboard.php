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

        $sewaMenungguBayar = $pembayaranModel->where('status', 'menunggu')->countAllResults();
        $sewaAktif         = $sewaModel->where('status', 'berlangsung')->countAllResults();

        $transaksiTerbaru  = $sewaModel->getSewaDetail();

        return view('kasir/dashboard', [
            'title'             => 'Dashboard Kasir - Operasional Sewa',
            'totalTersedia'     => $totalTersedia,
            'totalDisewa'       => $totalDisewa,
            'totalMaintenance'  => $totalMaintenance,
            'sewaMenungguBayar' => $sewaMenungguBayar,
            'sewaAktif'         => $sewaAktif,
            'transaksiTerbaru'  => array_slice($transaksiTerbaru, 0, 5),
        ]);
    }
}
