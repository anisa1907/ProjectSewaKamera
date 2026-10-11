<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\SewaModel;
use App\Models\DetailSewaModel;
use App\Models\PembayaranModel;

class StatusSewa extends BaseController
{
    protected $sewaModel;
    protected $detailSewaModel;
    protected $pembayaranModel;

    public function __construct()
    {
        $this->sewaModel       = new SewaModel();
        $this->detailSewaModel = new DetailSewaModel();
        $this->pembayaranModel = new PembayaranModel();
    }

    public function index()
    {
        $idPelanggan = session()->get('id_user');
        $daftarSewa  = $this->sewaModel->getSewaByPelanggan($idPelanggan);

        return view('pelanggan/status_sewa', [
            'title'      => 'Status Transaksi & Riwayat Sewa',
            'daftarSewa' => $daftarSewa,
        ]);
    }

    public function invoice($id_sewa)
    {
        $idPelanggan = session()->get('id_user');
        $sewa = $this->sewaModel->getSewaDetail($id_sewa);

        if (!$sewa || ($sewa['id_pelanggan'] != $idPelanggan && session()->get('role') === 'pelanggan')) {
            return redirect()->to(base_url('pelanggan/status-sewa'))->with('error', 'Transaksi tidak ditemukan atau Anda tidak berhak mengakses invoice ini.');
        }

        $items      = $this->detailSewaModel->getItemsBySewa($id_sewa);
        $pembayaran = $this->pembayaranModel->where('id_sewa', $id_sewa)->first();

        // Generate QRIS string dinamis
        $qrisData = "00020101021226600016ID.CO.QRIS.WWW01189360099990000000000215INV-" . str_pad($id_sewa, 6, '0', STR_PAD_LEFT) . "520458125303360540" . (int)$sewa['total_harga'] . "5802ID5919SEWA KAMERA KELOMPOK 96007BANDUNG6304";

        return view('pelanggan/invoice', [
            'title'      => 'Invoice Sewa #' . str_pad($id_sewa, 5, '0', STR_PAD_LEFT),
            'sewa'       => $sewa,
            'items'      => $items,
            'pembayaran' => $pembayaran,
            'qrisData'   => $qrisData,
        ]);
    }
}
