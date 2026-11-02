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
        $daftarSewa = array_values(array_filter($this->sewaModel->getSewaDetail(), static fn($s) => $s['tanggal_sewa'] === date('Y-m-d')));

        $pengajuanAktif = $this->pengajuanAktif();
        return view('kasir/transaksi', [
            'title'      => 'Kelola Transaksi Sewa & Pembayaran',
            'daftarSewa' => $daftarSewa,
            'pengajuanAktif' => $pengajuanAktif,
        ]);
    }

    private function pengajuanAktif(): array
    {
        $rows = \Config\Database::connect()->table('pengajuan_pembatalan p')
            ->select('p.*, u.nama_user as nama_pelanggan')
            ->join('user u', 'u.id_user = p.id_pelanggan')
            ->where('p.status', 'menunggu')->orderBy('p.dibuat_pada', 'ASC')->get()->getResultArray();
        $bySewa = [];
        foreach ($rows as $row) $bySewa[$row['id_sewa']] = $row;
        return $bySewa;
    }

    public function notifikasiPembatalan()
    {
        $rows = array_values($this->pengajuanAktif());
        return $this->response->setJSON([
            'jumlah' => count($rows),
            'pengajuan' => array_map(static function ($p) {
                return [
                    'id_pengajuan' => (int) $p['id_pengajuan'],
                    'id_sewa' => (int) $p['id_sewa'],
                    'nama_pelanggan' => $p['nama_pelanggan'],
                    'alasan' => $p['alasan'],
                    'dibuat_pada' => $p['dibuat_pada'],
                ];
            }, $rows),
        ]);
    }

    public function verifikasiPembayaran($id_sewa)
    {
        $statusPembayaran = $this->request->getPost('status_pembayaran'); // 'terbayar' atau 'ditolak'
        $idKasir          = session()->get('id_user');

        if (!in_array($statusPembayaran, ['terbayar','ditolak'], true)) return redirect()->back()->with('error','Status pembayaran tidak valid.');
        $sewa = $this->sewaModel->find($id_sewa);
        if (!$sewa || $sewa['status'] !== 'menunggu') return redirect()->back()->with('error','Transaksi tidak dapat diverifikasi.');
        if (isset($this->pengajuanAktif()[$id_sewa])) return redirect()->back()->with('error', 'Ada pengajuan pembatalan yang belum diputuskan. Selesaikan terlebih dahulu.');
        $alasan = trim((string)$this->request->getPost('alasan_pembatalan'));
        if ($statusPembayaran === 'ditolak' && $alasan === '') return redirect()->back()->with('error','Alasan pembatalan wajib diisi.');
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
                'alasan_pembatalan' => $alasan, 'dibatalkan_pada' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->catatHistori($id_sewa, $statusPembayaran === 'ditolak' ? 'pembatalan' : 'pembayaran', $statusPembayaran === 'ditolak' ? $alasan : 'Pembayaran terverifikasi');
        $db->transComplete();

        return redirect()->to(base_url('kasir/transaksi'))->with('success', 'Status pembayaran berhasil diperbarui menjadi ' . ucfirst($statusPembayaran));
    }

    public function serahkanUnit($id_sewa)
    {
        $sewa = $this->sewaModel->find($id_sewa);
        if (!$sewa || $sewa['status'] !== 'dikonfirmasi') return redirect()->back()->with('error','Sewa tidak siap diserahkan.');
        if (isset($this->pengajuanAktif()[$id_sewa])) return redirect()->back()->with('error', 'Pengajuan pembatalan masih menunggu keputusan.');
        $items = $this->detailSewaModel->where('id_sewa', $id_sewa)->findAll();
        if (!$items) return redirect()->back()->with('error','Tidak ada unit sewa.');
        foreach ($items as $item) {
            $unit = $this->unitModel->find($item['id_unit']);
            if (!$unit || $unit['status_unit'] !== 'tersedia') return redirect()->back()->with('error','Unit tidak tersedia.');
        }
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

        $this->catatHistori($id_sewa, 'penyerahan', 'Unit diserahkan');
        $db->transComplete();

        return redirect()->to(base_url('kasir/transaksi'))->with('success', 'Jaminan diverifikasi & unit kamera diserahkan ke pelanggan.');
    }

    public function pengembalian($id_sewa)
    {
        $sewa = $this->sewaModel->find($id_sewa);
        if (!$sewa || $sewa['status'] !== 'berlangsung') return redirect()->to(base_url('kasir/histori'))->with('error','Sewa tidak aktif.');
        $kondisiKamera = $this->request->getPost('kondisi'); // 'baik', 'rusak_ringan', 'rusak_berat'
        $denda         = (float)$this->request->getPost('denda');
        if (!in_array($kondisiKamera, ['baik','rusak_ringan','rusak_berat'],true) || $denda < 0) return redirect()->back()->with('error','Data pengembalian tidak valid.');
        $keterangan    = $this->request->getPost('keterangan') ?? 'Pengembalian sewa';

        $items = $this->detailSewaModel->where('id_sewa', $id_sewa)->findAll();
        if (!$items) return redirect()->back()->with('error','Tidak ada unit sewa.');
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

        $this->catatHistori($id_sewa, 'pengembalian', $keterangan);
        $db->transComplete();

        return redirect()->to(base_url('kasir/histori'))->with('success', 'Unit berhasil dikembalikan, jaminan diserahkan kembali ke pelanggan.');
    }
    private function catatHistori($id, $aktivitas, $keterangan = '')
    {
        \Config\Database::connect()->table('histori_transaksi')->insert([
            'id_sewa' => $id, 'id_user' => session()->get('id_user'),
            'aktivitas' => $aktivitas, 'keterangan' => $keterangan,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ]);
    }

    public function histori()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sewa s')->select('s.*, u.nama_user as nama_pelanggan')
            ->join('user u','u.id_user=s.id_pelanggan');
        $status = (string)$this->request->getGet('status');
        $tahun = (string)$this->request->getGet('tahun');
        $bulan = (string)$this->request->getGet('bulan');
        if (in_array($status,['menunggu','dikonfirmasi','berlangsung','selesai','dibatalkan'],true)) $builder->where('s.status',$status);
        if (preg_match('/^\d{4}$/',$tahun)) $builder->where('YEAR(s.tanggal_sewa)',(int)$tahun);
        if (preg_match('/^(0?[1-9]|1[0-2])$/',$bulan)) $builder->where('MONTH(s.tanggal_sewa)',(int)$bulan);
        $rows = $builder->orderBy('s.id_sewa','DESC')->get()->getResultArray();
        foreach ($rows as &$row) {
            $row['items'] = (new \App\Models\DetailSewaModel())->getItemsBySewa($row['id_sewa']);
        }
        unset($row);
        $pengajuan = $db->table('pengajuan_pembatalan p')->select('p.*, u.nama_user as nama_pelanggan')->join('user u', 'u.id_user=p.id_pelanggan')->where('p.status', 'menunggu')->orderBy('p.id_pengajuan', 'DESC')->get()->getResultArray();
        $refunds = $db->table('refund_pembatalan r')->select('r.*, s.status AS status_sewa, u.nama_user AS nama_pelanggan')
            ->join('sewa s','s.id_sewa=r.id_sewa')->join('user u','u.id_user=s.id_pelanggan')
            ->whereIn('r.status',['perlu_verifikasi','menunggu_refund'])->orderBy('r.dibuat_pada','DESC')->get()->getResultArray();
        return view('kasir/histori',['title'=>'Histori Sewa & Pengembalian','rows'=>$rows,'pengajuan'=>$pengajuan,'refunds'=>$refunds]);
    }

    public function keputusanPembatalan($id_pengajuan)
    {
        $keputusan = (string) $this->request->getPost('keputusan');
        $catatan = trim((string) $this->request->getPost('catatan'));
        if (!in_array($keputusan, ['setujui', 'tolak'], true)) return redirect()->back()->with('error', 'Keputusan tidak valid.');
        if ($keputusan === 'tolak' && $catatan === '') return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $p = $db->table('pengajuan_pembatalan')->where('id_pengajuan', $id_pengajuan)->where('status', 'menunggu')->get()->getRowArray();
            if (!$p) throw new \RuntimeException('Pengajuan tidak ditemukan atau sudah diproses.');
            $sewa = $db->table('sewa')->where('id_sewa', $p['id_sewa'])->get()->getRowArray();
            if (!$sewa || !in_array($sewa['status'], ['menunggu', 'dikonfirmasi'], true)) throw new \RuntimeException('Sewa sudah berjalan atau tidak bisa dibatalkan.');
            $now = date('Y-m-d H:i:s');
            $changed = $db->table('pengajuan_pembatalan')->where('id_pengajuan', $id_pengajuan)->where('status', 'menunggu')->update([
                'status' => $keputusan === 'setujui' ? 'disetujui' : 'ditolak',
                'id_kasir' => session()->get('id_user'), 'catatan_kasir' => $catatan, 'diproses_pada' => $now,
            ]);
            if (!$changed || $db->affectedRows() !== 1) throw new \RuntimeException('Pengajuan telah diproses kasir lain.');
            if ($keputusan === 'setujui') {
                $db->table('sewa')->where('id_sewa', $p['id_sewa'])->whereIn('status', ['menunggu', 'dikonfirmasi'])->update([
                    'status' => 'dibatalkan', 'alasan_pembatalan' => $p['alasan'], 'dibatalkan_pada' => $now,
                ]);
                if ($db->affectedRows() !== 1) throw new \RuntimeException('Status sewa telah berubah.');
                // Simpan kewajiban pemeriksaan/refund tanpa mengubah bukti pembayaran.
                $bayar = $db->table('pembayaran')->where('id_sewa', $p['id_sewa'])->orderBy('id_pembayaran','DESC')->get()->getRowArray();
                if ($bayar && !$db->table('refund_pembatalan')->where('id_sewa',$p['id_sewa'])->countAllResults()) {
                    $db->table('refund_pembatalan')->insert([
                        'id_sewa'=>$p['id_sewa'], 'id_pembayaran'=>$bayar['id_pembayaran'],
                        'jumlah'=>$bayar['jumlah'],
                        'status'=>$bayar['status']==='terbayar' ? 'menunggu_refund' : 'perlu_verifikasi',
                        'catatan'=>'Pembatalan sebelum serah terima; refund 100% apabila dana diterima.',
                        'dibuat_pada'=>$now,
                    ]);
                }
            }
            $db->table('histori_transaksi')->insert([
                'id_sewa' => $p['id_sewa'], 'id_user' => session()->get('id_user'),
                'aktivitas' => $keputusan === 'setujui' ? 'pembatalan_disetujui' : 'pembatalan_ditolak',
                'keterangan' => $keputusan === 'setujui' ? $p['alasan'] : $catatan,
                'dibuat_pada' => $now,
            ]);
            if ($db->transStatus() === false) throw new \RuntimeException('Gagal menyimpan keputusan.');
            $db->transCommit();
            return redirect()->to(base_url('kasir/histori'))->with('success', 'Pengajuan berhasil diproses.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function prosesRefund($id_refund)
    {
        $aksi=(string)$this->request->getPost('aksi');
        $catatan=trim((string)$this->request->getPost('catatan'));
        if (!in_array($aksi,['dana_diterima','dana_tidak_diterima','refund_selesai'],true)) return redirect()->back()->with('error','Aksi tidak valid.');
        $file=$this->request->getFile('bukti_refund');
        if ($aksi==='refund_selesai' && (!$file || !$file->isValid() || $file->hasMoved())) return redirect()->back()->with('error','Bukti transfer refund wajib diunggah.');
        if ($aksi==='dana_tidak_diterima' && mb_strlen($catatan)<5) return redirect()->back()->with('error','Alasan pemeriksaan minimal 5 karakter.');
        if ($aksi==='refund_selesai') {
            if (!in_array(strtolower($file->getExtension()),['jpg','jpeg','png','pdf'],true) || $file->getSize()>2097152 || !in_array($file->getMimeType(),['image/jpeg','image/png','application/pdf'],true)) return redirect()->back()->with('error','Bukti harus JPG, PNG, atau PDF maksimal 2MB.');
        }
        $db=\Config\Database::connect();
        $db->transBegin();
        $filename=null;
        try {
            $r=$db->table('refund_pembatalan')->where('id_refund',$id_refund)->get()->getRowArray();
            if (!$r) throw new \RuntimeException('Data refund tidak ditemukan.');
            $expected=$aksi==='refund_selesai'?'menunggu_refund':'perlu_verifikasi';
            if ($r['status']!==$expected) throw new \RuntimeException('Tahap refund tidak sesuai atau sudah diproses.');
            $status=['dana_diterima'=>'menunggu_refund','dana_tidak_diterima'=>'tidak_ada_dana','refund_selesai'=>'selesai'][$aksi];
            $data=['status'=>$status,'catatan'=>$catatan,'id_kasir'=>session()->get('id_user'),'diproses_pada'=>date('Y-m-d H:i:s')];
            if ($aksi==='refund_selesai') {
                $dir=WRITEPATH.'uploads/refund/';
                if (!is_dir($dir) && !mkdir($dir,0750,true) && !is_dir($dir)) throw new \RuntimeException('Folder bukti refund tidak dapat dibuat.');
                $filename=$file->getRandomName();
                if (!$file->move($dir,$filename)) throw new \RuntimeException('Gagal menyimpan bukti refund.');
                $data['bukti_refund']=$filename;
            }
            $db->table('refund_pembatalan')->where('id_refund',$id_refund)->where('status',$expected)->update($data);
            if ($db->affectedRows()!==1) throw new \RuntimeException('Refund telah diproses kasir lain.');
            $this->catatHistori($r['id_sewa'],'refund_'.$status,$catatan ?: 'Status refund: '.$status);
            if ($db->transStatus()===false) throw new \RuntimeException('Gagal menyimpan refund.');
            $db->transCommit();
            return redirect()->to(base_url('kasir/histori'))->with('success','Status refund berhasil diperbarui.');
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($filename && is_file(WRITEPATH.'uploads/refund/'.$filename)) unlink(WRITEPATH.'uploads/refund/'.$filename);
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function durasi()
    {
        $rows = $this->sewaModel->where('status','berlangsung')->orderBy('tanggal_kembali','ASC')->findAll();
        return view('kasir/durasi',['title'=>'Monitoring Durasi Sewa','rows'=>$rows]);
    }

    public function directRental()
    {
        $db = \Config\Database::connect();
        return view('kasir/direct_rental',[
            'title'=>'Sewa Langsung',
            'pelanggan'=>$db->table('user')->where('role','pelanggan')->orderBy('nama_user')->get()->getResultArray(),
            'units'=>$this->unitModel->getAvailableUnits(),
        ]);
    }

    public function simpanDirectRental()
    {
        $pelanggan=(int)$this->request->getPost('id_pelanggan');
        $unitIds=array_values(array_unique(array_map('intval',(array)$this->request->getPost('unit_ids'))));
        $mulai=(string)$this->request->getPost('waktu_mulai');
        $selesai=(string)$this->request->getPost('waktu_selesai');
        $jaminan=(string)$this->request->getPost('jaminan');
        $metode=(string)$this->request->getPost('metode');
        $start=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$mulai);
        $end=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$selesai);
        if (!$pelanggan || !$unitIds || !$start || !$end || $end <= $start || !in_array($jaminan,['KTP','KK','Akta'],true) || !in_array($metode,['Cash','QRIS'],true))
            return redirect()->back()->withInput()->with('error','Lengkapi data sewa dengan benar.');
        $db=\Config\Database::connect();
        if (!$db->table('user')->where('id_user',$pelanggan)->where('role','pelanggan')->countAllResults()) return redirect()->back()->with('error','Pelanggan tidak valid.');
        $db->transBegin();
        try {
            $units=$db->table('unit')->whereIn('id_unit',$unitIds)->get()->getResultArray();
            if (count($units)!==count($unitIds)) throw new \RuntimeException('Unit tidak ditemukan.');
            $days=max(1,(int)ceil(($end->getTimestamp()-$start->getTimestamp())/86400));
            $total=0;
            foreach ($units as $u) {
                if ($u['status_unit']!=='tersedia') throw new \RuntimeException('Ada unit yang tidak tersedia.');
                $total+=(float)$u['harga_sewa']*$days;
            }
            $id=$this->sewaModel->insert([
                'id_user'=>session()->get('id_user'),'id_pelanggan'=>$pelanggan,
                'tanggal_sewa'=>$start->format('Y-m-d'),'tanggal_kembali'=>$end->format('Y-m-d'),
                'waktu_mulai'=>$start->format('Y-m-d H:i:s'),'waktu_selesai'=>$end->format('Y-m-d H:i:s'),
                'jaminan'=>$jaminan,'total_harga'=>$total,'status'=>'berlangsung','sumber_transaksi'=>'kasir',
            ],true);
            if (!$id) throw new \RuntimeException('Gagal menyimpan transaksi.');
            foreach ($units as $u) {
                $changed=$db->table('unit')->where('id_unit',$u['id_unit'])->where('status_unit','tersedia')->update(['status_unit'=>'disewa']);
                if (!$changed || $db->affectedRows()!==1) throw new \RuntimeException('Unit baru saja disewa pelanggan lain.');
                $this->detailSewaModel->insert(['id_sewa'=>$id,'id_unit'=>$u['id_unit'],'harga_sewa'=>$u['harga_sewa'],'subtotal'=>(float)$u['harga_sewa']*$days]);
            }
            $this->pembayaranModel->insert(['id_sewa'=>$id,'metode'=>$metode,'jumlah'=>$total,'tanggal_bayar'=>date('Y-m-d'),'status'=>'terbayar']);
            $this->catatHistori($id,'sewa_langsung','Sewa langsung oleh kasir');
            if ($db->transStatus()===false) throw new \RuntimeException('Penyimpanan gagal.');
            $db->transCommit();
            return redirect()->to(base_url('kasir/transaksi'))->with('success','Sewa langsung berhasil dibuat.');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error','Direct rental gagal: '.$e->getMessage());
            return redirect()->back()->withInput()->with('error','Transaksi gagal: '.$e->getMessage());
        }
    }
}
