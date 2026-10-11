<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\SewaModel;
use App\Models\DetailSewaModel;
use App\Models\UnitModel;
use App\Models\PembayaranModel;
use App\Models\PengembalianModel;
use App\Models\MaintenanceModel;

class Transaksi extends BaseController
{
    protected $sewaModel;
    protected $detailSewaModel;
    protected $unitModel;
    protected $pembayaranModel;
    protected $pengembalianModel;
    protected $maintenanceModel;

    public function __construct()
    {
        $this->sewaModel         = new SewaModel();
        $this->detailSewaModel   = new DetailSewaModel();
        $this->unitModel         = new UnitModel();
        $this->pembayaranModel   = new PembayaranModel();
        $this->pengembalianModel = new PengembalianModel();
        $this->maintenanceModel  = new MaintenanceModel();
    }

    public function index()
    {
        $daftarSewa = $this->sewaModel->getSewaDetail();

        return view('kasir/transaksi', [
            'title'      => 'Kelola Transaksi Sewa & Pembayaran',
            'daftarSewa' => $daftarSewa,
        ]);
    }

    public function verifikasiPembayaran($id_sewa)
    {
        $statusPembayaran = $this->request->getPost('status_pembayaran'); // 'terbayar' atau 'ditolak'
        $idKasir          = session()->get('id_user');

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update status pembayaran
        $this->pembayaranModel->where('id_sewa', $id_sewa)->set([
            'status'        => $statusPembayaran,
            'tanggal_bayar' => date('Y-m-d'),
        ])->update();

        // 2. Update status sewa & assign kasir yang menangani
        if ($statusPembayaran === 'terbayar') {
            $this->sewaModel->update($id_sewa, [
                'id_user' => $idKasir,
                'status'  => 'dikonfirmasi',
            ]);
        } else {
            $this->sewaModel->update($id_sewa, [
                'id_user' => $idKasir,
                'status'  => 'dibatalkan',
            ]);
        }

        $db->transComplete();

        return redirect()->to(base_url('kasir/transaksi'))->with('success', 'Status pembayaran berhasil diperbarui menjadi ' . ucfirst($statusPembayaran));
    }

    public function serahkanUnit($id_sewa)
    {
        $items = $this->detailSewaModel->where('id_sewa', $id_sewa)->findAll();

        $db = \Config\Database::connect();
        $db->transStart();

        // Ubah status sewa menjadi 'berlangsung'
        $this->sewaModel->update($id_sewa, [
            'id_user' => session()->get('id_user'),
            'status'  => 'berlangsung',
        ]);

        // Ubah status unit menjadi 'disewa'
        foreach ($items as $item) {
            $this->unitModel->update($item['id_unit'], [
                'status_unit' => 'disewa',
            ]);
        }

        $db->transComplete();

        return redirect()->to(base_url('kasir/transaksi'))->with('success', 'Jaminan diverifikasi & unit kamera diserahkan ke pelanggan.');
    }

    public function pengembalian($id_sewa)
    {
        $kondisiKamera = $this->request->getPost('kondisi'); // 'baik', 'rusak_ringan', 'rusak_berat'
        $denda         = (float)$this->request->getPost('denda');
        $keterangan    = $this->request->getPost('keterangan') ?? 'Pengembalian sewa';

        $items = $this->detailSewaModel->where('id_sewa', $id_sewa)->findAll();

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Catat Pengembalian
        $this->pengembalianModel->insert([
            'id_sewa'         => $id_sewa,
            'tanggal_kembali' => date('Y-m-d'),
            'kondisi'         => $kondisiKamera,
            'denda'           => $denda,
        ]);

        // 2. Update status sewa selesai
        $this->sewaModel->update($id_sewa, [
            'status' => 'selesai',
        ]);

        // 3. Update kondisi unit & maintenance jika rusak
        foreach ($items as $item) {
            if ($kondisiKamera === 'baik') {
                $this->unitModel->update($item['id_unit'], [
                    'kondisi'     => 'baik',
                    'status_unit' => 'tersedia',
                ]);
            } else {
                // Masukkan ke status maintenance
                $this->unitModel->update($item['id_unit'], [
                    'kondisi'     => $kondisiKamera,
                    'status_unit' => 'maintenance',
                ]);

                // Catat ke tabel maintenance
                $this->maintenanceModel->insert([
                    'id_unit'         => $item['id_unit'],
                    'tanggal_mulai'   => date('Y-m-d'),
                    'tanggal_selesai' => null,
                    'biaya'           => $denda,
                    'keterangan'      => 'Perbaikan unit karena ' . str_replace('_', ' ', $kondisiKamera) . ' - ' . $keterangan,
                ]);
            }
        }

        $db->transComplete();

        return redirect()->to(base_url('kasir/transaksi'))->with('success', 'Unit berhasil dikembalikan, jaminan diserahkan kembali ke pelanggan.');
    }
}
