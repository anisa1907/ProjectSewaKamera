<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\UnitModel;
use App\Models\SewaModel;
use App\Models\DetailSewaModel;
use App\Models\PembayaranModel;

class Sewa extends BaseController
{
    protected $unitModel;
    protected $sewaModel;
    protected $detailSewaModel;
    protected $pembayaranModel;

    public function __construct()
    {
        $this->unitModel       = new UnitModel();
        $this->sewaModel       = new SewaModel();
        $this->detailSewaModel = new DetailSewaModel();
        $this->pembayaranModel = new PembayaranModel();
    }

    public function checkout($id_unit)
    {
        $unit = $this->unitModel->select('unit.*, inventory.nama_barang, inventory.kode_barang, inventory.merk, inventory.tipe, kategori.nama_kategori')
                                ->join('inventory', 'inventory.id_inv = unit.id_inv')
                                ->join('kategori', 'kategori.id_kategori = inventory.id_kategori')
                                ->where('unit.id_unit', $id_unit)
                                ->where('unit.status_unit', 'tersedia')
                                ->first();

        if (!$unit) {
            return redirect()->to(base_url('pelanggan/katalog'))->with('error', 'Unit tidak ditemukan atau sedang tidak tersedia.');
        }

        return view('pelanggan/checkout', [
            'title' => 'Formulir Pemesanan Sewa',
            'unit'  => $unit,
        ]);
    }

    public function processCheckout()
    {
        $idPelanggan    = session()->get('id_user');
        $idUnit         = $this->request->getPost('id_unit');
        $tanggalSewa    = $this->request->getPost('tanggal_sewa');
        $tanggalKembali = $this->request->getPost('tanggal_kembali');
        $jaminan        = $this->request->getPost('jaminan');
        $metode         = $this->request->getPost('metode');

        if (empty($idUnit) || empty($tanggalSewa) || empty($tanggalKembali) || empty($jaminan) || empty($metode)) {
            return redirect()->back()->withInput()->with('error', 'Semua field formulir wajib diisi.');
        }

        $unit = $this->unitModel->where('id_unit', $idUnit)->where('status_unit', 'tersedia')->first();
        if (!$unit) {
            return redirect()->to(base_url('pelanggan/katalog'))->with('error', 'Unit kamera telah dipesan atau tidak tersedia.');
        }

        // Hitung durasi hari
        $dStart = new \DateTime($tanggalSewa);
        $dEnd   = new \DateTime($tanggalKembali);
        $diff   = $dStart->diff($dEnd);
        $days   = max(1, $diff->days);

        $hargaPerHari = (float)$unit['harga_sewa'];
        $totalHarga   = $hargaPerHari * $days;

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Simpan Sewa
        $this->sewaModel->insert([
            'id_user'         => null, // Belum ada kasir yang menangani
            'id_pelanggan'    => $idPelanggan,
            'tanggal_sewa'    => $tanggalSewa,
            'tanggal_kembali' => $tanggalKembali,
            'jaminan'         => $jaminan,
            'total_harga'     => $totalHarga,
            'status'          => 'menunggu',
        ]);
        $idSewa = $this->sewaModel->getInsertID();

        // 2. Simpan Detail Sewa
        $this->detailSewaModel->insert([
            'id_sewa'    => $idSewa,
            'id_unit'    => $idUnit,
            'harga_sewa' => $hargaPerHari,
            'subtotal'   => $totalHarga,
        ]);

        // 3. Simpan Pembayaran (Status: menunggu konfirmasi kasir)
        $this->pembayaranModel->insert([
            'id_sewa'       => $idSewa,
            'metode'        => $metode,
            'jumlah'        => $totalHarga,
            'tanggal_bayar' => date('Y-m-d'),
            'status'        => 'menunggu',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pemesanan. Silakan coba kembali.');
        }

        return redirect()->to(base_url('pelanggan/status-sewa'))->with('success', 'Pemesanan berhasil diajukan! Silakan cetak invoice dan lakukan pembayaran.');
    }
}
