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

        $pengajuan = \Config\Database::connect()->table('pengajuan_pembatalan')->where('id_pelanggan', $idPelanggan)->orderBy('id_pengajuan', 'DESC')->get()->getResultArray();
        $statusPengajuan = [];
        foreach ($pengajuan as $p) {
            if (!isset($statusPengajuan[$p['id_sewa']])) $statusPengajuan[$p['id_sewa']] = $p;
        }

        $refundRows = \Config\Database::connect()->table('refund_pembatalan r')
            ->select('r.id_sewa,r.status,r.jumlah,r.catatan')
            ->join('sewa s','s.id_sewa=r.id_sewa')->where('s.id_pelanggan',$idPelanggan)->get()->getResultArray();
        $refundBySewa=[];
        foreach ($refundRows as $r) $refundBySewa[$r['id_sewa']]=$r;
        return view('pelanggan/status_sewa', ['refundBySewa'=>$refundBySewa,
            'title'      => 'Status Transaksi & Riwayat Sewa',
            'daftarSewa' => $daftarSewa,
            'statusPengajuan' => $statusPengajuan,
        ]);
    }

    public function ajukanPembatalan($id_sewa)
    {
        $alasan = trim((string) $this->request->getPost('alasan'));
        if (mb_strlen($alasan) < 10 || mb_strlen($alasan) > 1000) {
            return redirect()->back()->with('error', 'Alasan pembatalan wajib diisi (10-1000 karakter).');
        }
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $sewa = $db->table('sewa')->where('id_sewa', $id_sewa)
                ->where('id_pelanggan', session()->get('id_user'))->get()->getRowArray();
            if (!$sewa || !in_array($sewa['status'], ['menunggu', 'dikonfirmasi'], true)) {
                throw new \RuntimeException('Transaksi tidak dapat diajukan pembatalan.');
            }
            if ($db->table('pengajuan_pembatalan')->where('id_sewa', $id_sewa)->where('status', 'menunggu')->countAllResults()) {
                throw new \RuntimeException('Pengajuan pembatalan masih menunggu persetujuan kasir.');
            }
            $db->table('pengajuan_pembatalan')->insert([
                'id_sewa' => $id_sewa, 'id_pelanggan' => session()->get('id_user'),
                'alasan' => $alasan, 'status' => 'menunggu', 'dibuat_pada' => date('Y-m-d H:i:s'),
            ]);
            $db->table('histori_transaksi')->insert([
                'id_sewa' => $id_sewa, 'id_user' => session()->get('id_user'),
                'aktivitas' => 'pengajuan_pembatalan', 'keterangan' => $alasan,
                'dibuat_pada' => date('Y-m-d H:i:s'),
            ]);
            if ($db->transStatus() === false) throw new \RuntimeException('Gagal menyimpan pengajuan.');
            $db->transCommit();
            return redirect()->back()->with('success', 'Pengajuan pembatalan dikirim ke kasir.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
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
