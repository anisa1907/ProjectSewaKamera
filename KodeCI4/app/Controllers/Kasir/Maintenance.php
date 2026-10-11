<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\MaintenanceModel;
use App\Models\UnitModel;

class Maintenance extends BaseController
{
    protected $maintenanceModel;
    protected $unitModel;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
        $this->unitModel        = new UnitModel();
    }

    public function index()
    {
        $daftarMaintenance = $this->maintenanceModel->getMaintenanceWithUnit();
        $unitRusak = $this->unitModel->where('status_unit', 'maintenance')->findAll();

        return view('kasir/maintenance', [
            'title'             => 'Riwayat & Manajemen Perawatan (Maintenance)',
            'daftarMaintenance' => $daftarMaintenance,
            'unitRusak'         => $unitRusak,
        ]);
    }

    public function selesai($id_maintenance)
    {
        $maint = $this->maintenanceModel->find($id_maintenance);
        if ($maint) {
            $this->maintenanceModel->update($id_maintenance, [
                'tanggal_selesai' => date('Y-m-d'),
            ]);

            // Kembalikan unit menjadi tersedia & kondisi baik
            $this->unitModel->update($maint['id_unit'], [
                'kondisi'     => 'baik',
                'status_unit' => 'tersedia',
            ]);
        }

        return redirect()->to(base_url('kasir/maintenance'))->with('success', 'Perawatan unit selesai! Unit kini berstatus tersedia kembali.');
    }
}
