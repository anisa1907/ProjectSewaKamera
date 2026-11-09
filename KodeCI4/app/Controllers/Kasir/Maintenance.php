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
        $this->unitModel = new UnitModel();
    }

    public function index()
    {
        $daftarMaintenance = $this->maintenanceModel->getMaintenanceWithUnit();
        $unitRusak = $this->unitModel->select('unit.*, inventory.nama_barang, inventory.merk, inventory.tipe')
            ->join('inventory', 'inventory.id_inv = unit.id_inv')
            ->where('unit.status_unit !=', 'disewa')->orderBy('unit.kode_unit', 'ASC')->findAll();

        return view('kasir/maintenance', [
            'title' => 'Riwayat & Manajemen Perawatan (Maintenance)',
            'daftarMaintenance' => $daftarMaintenance,
            'unitRusak' => $unitRusak,
        ]);
    }

    public function mulai()
    {
        $idUnit = (int) $this->request->getPost('id_unit');
        $tanggalMulai = $this->request->getPost('tanggal_mulai') ?: date('Y-m-d');
        $kondisi = $this->request->getPost('kondisi');
        $biaya = $this->request->getPost('biaya');
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $tanggalObj = \DateTime::createFromFormat('Y-m-d', $tanggalMulai);
        if (!$idUnit || !$tanggalObj || $tanggalObj->format('Y-m-d') !== $tanggalMulai || $tanggalMulai > date('Y-m-d') || !in_array($kondisi, ['rusak_ringan', 'rusak_berat'], true) || !is_numeric($biaya) || (float) $biaya < 0 || $keterangan === '' || mb_strlen($keterangan) > 255) {
            return redirect()->back()->withInput()->with('error', 'Lengkapi data perbaikan dengan benar. Tanggal masuk tidak boleh di masa depan.');
        }
        $unit = $this->unitModel->find($idUnit);
        if (!$unit) return redirect()->back()->with('error', 'Unit tidak ditemukan.');
        if ($unit['status_unit'] === 'disewa') return redirect()->back()->with('error', 'Unit sedang disewa dan belum dapat dimasukkan ke maintenance.');
        if ($unit['status_unit'] === 'maintenance') return redirect()->back()->with('error', 'Unit ini masih memiliki status maintenance. Selesaikan perbaikan sebelumnya terlebih dahulu.');

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->maintenanceModel->insert([
                'id_unit' => $idUnit,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => null,
                'biaya' => (float) $biaya,
                'keterangan' => $keterangan,
            ]);
            $this->unitModel->update($idUnit, [
                'kondisi' => $kondisi,
                'status_unit' => 'maintenance',
            ]);
            if ($db->transStatus() === false) throw new \RuntimeException('Gagal menyimpan data maintenance.');
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal memulai maintenance: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Data perbaikan gagal disimpan.');
        }

        return redirect()->to(base_url('kasir/maintenance'))->with('success', 'Unit berhasil dicatat masuk perbaikan dan tidak dapat disewa sementara.');
    }

    public function selesai($id_maintenance)
    {
        $maint = $this->maintenanceModel->find($id_maintenance);
        if (!$maint) return redirect()->to(base_url('kasir/maintenance'))->with('error', 'Data maintenance tidak ditemukan.');
        if (!empty($maint['tanggal_selesai'])) return redirect()->to(base_url('kasir/maintenance'))->with('error', 'Perbaikan ini sudah pernah diselesaikan.');

        $unit = $this->unitModel->find($maint['id_unit']);
        if (!$unit || $unit['status_unit'] !== 'maintenance') return redirect()->to(base_url('kasir/maintenance'))->with('error', 'Status unit tidak sesuai. Periksa data unit sebelum menyelesaikan servis.');

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->maintenanceModel->update($id_maintenance, ['tanggal_selesai' => date('Y-m-d')]);
            $this->unitModel->update($maint['id_unit'], ['kondisi' => 'baik', 'status_unit' => 'tersedia']);
            if ($db->transStatus() === false) throw new \RuntimeException('Gagal menyelesaikan maintenance.');
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal menyelesaikan maintenance: ' . $e->getMessage());
            return redirect()->to(base_url('kasir/maintenance'))->with('error', 'Perubahan status servis gagal disimpan.');
        }

        return redirect()->to(base_url('kasir/maintenance'))->with('success', 'Perawatan selesai. Unit kembali tersedia dengan kondisi baik.');
    }
}
