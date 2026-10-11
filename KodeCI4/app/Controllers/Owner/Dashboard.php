<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\SewaModel;
use App\Models\PembayaranModel;
use App\Models\PengembalianModel;
use App\Models\MaintenanceModel;
use App\Models\UnitModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $sewaModel       = new SewaModel();
        $pembayaranModel = new PembayaranModel();
        $unitModel       = new UnitModel();

        // Pendapatan terbayar
        $totalPendapatan = $pembayaranModel->where('status', 'terbayar')->selectSum('jumlah')->first()['jumlah'] ?? 0;
        $totalTransaksi  = $sewaModel->countAllResults();
        $transaksiSelesai = $sewaModel->where('status', 'selesai')->countAllResults();
        $totalUnit       = $unitModel->countAllResults();

        $transaksiTerbaru = $sewaModel->getSewaDetail();

        return view('owner/dashboard', [
            'title'            => 'Executive Dashboard - Owner',
            'totalPendapatan'  => $totalPendapatan,
            'totalTransaksi'   => $totalTransaksi,
            'transaksiSelesai' => $transaksiSelesai,
            'totalUnit'        => $totalUnit,
            'transaksiTerbaru' => array_slice($transaksiTerbaru, 0, 5),
        ]);
    }
}
