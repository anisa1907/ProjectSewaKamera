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

    public function checkout($id_unit = null)
    {
        // Tetap mendukung URL checkout lama untuk satu unit.
        if ($id_unit !== null) {
            $ids = [(int) $id_unit];
        } else {
            $ids = array_values(array_unique(array_map('intval', session()->get('keranjang_sewa') ?? [])));
        }
        if (!$ids) return redirect()->to(base_url('pelanggan/katalog'))->with('error', 'Keranjang masih kosong.');

        $units = $this->unitModel->select('unit.*, inventory.nama_barang, inventory.kode_barang, inventory.merk, inventory.tipe, kategori.nama_kategori')
            ->join('inventory', 'inventory.id_inv = unit.id_inv')
            ->join('kategori', 'kategori.id_kategori = inventory.id_kategori')
            ->whereIn('unit.id_unit', $ids)->where('unit.status_unit', 'tersedia')->findAll();
        if (count($units) !== count($ids)) {
            return redirect()->to(base_url('pelanggan/keranjang'))->with('error', 'Ada unit dalam keranjang yang sudah tidak tersedia. Periksa kembali keranjang.');
        }

        return view('pelanggan/checkout', [
            'title' => 'Formulir Pemesanan Sewa',
            'units' => $units,
            'unit' => $units[0], // kompatibilitas tampilan lama
            'fromCart' => $id_unit === null,
        ]);
    }

    public function processCheckout()
    {
        $idPelanggan = session()->get('id_user');
        $postedIds = $this->request->getPost('id_unit');
        if (!is_array($postedIds)) $postedIds = [$postedIds];
        $ids = array_values(array_unique(array_filter(array_map('intval', $postedIds))));
        $tanggalSewa = $this->request->getPost('tanggal_sewa');
        $tanggalKembali = $this->request->getPost('tanggal_kembali');
        $jaminan = $this->request->getPost('jaminan');
        $metode = $this->request->getPost('metode');

        if (!$idPelanggan || !$ids || !$tanggalSewa || !$tanggalKembali || !$jaminan || !$metode) {
            return redirect()->back()->withInput()->with('error', 'Data pemesanan belum lengkap.');
        }
        if (!in_array($jaminan, ['KTP', 'KK', 'Akta'], true) || !in_array($metode, ['QRIS', 'Cash'], true)) {
            return redirect()->back()->withInput()->with('error', 'Pilihan jaminan atau pembayaran tidak valid.');
        }
        $start = \DateTime::createFromFormat('Y-m-d', $tanggalSewa);
        $end = \DateTime::createFromFormat('Y-m-d', $tanggalKembali);
        $today = date('Y-m-d');
        if (!$start || !$end || $start->format('Y-m-d') !== $tanggalSewa || $end->format('Y-m-d') !== $tanggalKembali || $tanggalSewa < $today || $tanggalKembali <= $tanggalSewa) {
            return redirect()->back()->withInput()->with('error', 'Tanggal sewa tidak valid. Tanggal kembali harus setelah tanggal mulai.');
        }

        $units = $this->unitModel->whereIn('id_unit', $ids)->where('status_unit', 'tersedia')->findAll();
        if (count($units) !== count($ids)) return redirect()->to(base_url('pelanggan/keranjang'))->with('error', 'Satu atau lebih unit sudah tidak tersedia.');

        $days = (int) $start->diff($end)->days;
        $totalHarga = 0;
        $details = [];
        foreach ($units as $unit) {
            $subtotal = (float) $unit['harga_sewa'] * $days;
            $totalHarga += $subtotal;
            $details[] = ['id_unit' => $unit['id_unit'], 'harga_sewa' => $unit['harga_sewa'], 'subtotal' => $subtotal];
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->sewaModel->insert([
                'id_user' => null,
                'id_pelanggan' => $idPelanggan,
                'tanggal_sewa' => $tanggalSewa,
                'tanggal_kembali' => $tanggalKembali,
                'jaminan' => $jaminan,
                'total_harga' => $totalHarga,
                'status' => 'menunggu',
            ]);
            $idSewa = $this->sewaModel->getInsertID();
            foreach ($details as $detail) {
                $detail['id_sewa'] = $idSewa;
                $this->detailSewaModel->insert($detail);
            }
            $this->pembayaranModel->insert([
                'id_sewa' => $idSewa,
                'metode' => $metode,
                'jumlah' => $totalHarga,
                'tanggal_bayar' => date('Y-m-d'),
                'status' => 'menunggu',
            ]);
            if ($db->transStatus() === false) throw new \RuntimeException('Transaksi database gagal.');
            $db->transCommit();
            session()->remove('keranjang_sewa');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Checkout multi-unit gagal: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pemesanan. Silakan coba kembali.');
        }

        return redirect()->to(base_url('pelanggan/status-sewa'))->with('success', 'Pemesanan ' . count($units) . ' unit berhasil diajukan. Silakan lanjutkan pembayaran.');
    }
}
