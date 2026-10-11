<?php

namespace App\Models;

use CodeIgniter\Model;

class SewaModel extends Model
{
    protected $table            = 'sewa';
    protected $primaryKey       = 'id_sewa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_user',
        'id_pelanggan',
        'tanggal_sewa',
        'tanggal_kembali',
        'jaminan',
        'total_harga',
        'status',
    ];

    public function getSewaDetail($id_sewa = null)
    {
        $builder = $this->select('sewa.*, pelanggan.nama_user as nama_pelanggan, kasir.nama_user as nama_kasir, pembayaran.metode, pembayaran.status as status_pembayaran, pembayaran.tanggal_bayar')
                        ->join('user as pelanggan', 'pelanggan.id_user = sewa.id_pelanggan')
                        ->join('user as kasir', 'kasir.id_user = sewa.id_user', 'left')
                        ->join('pembayaran', 'pembayaran.id_sewa = sewa.id_sewa', 'left');

        if ($id_sewa !== null) {
            return $builder->where('sewa.id_sewa', $id_sewa)->first();
        }

        return $builder->orderBy('sewa.id_sewa', 'DESC')->findAll();
    }

    public function getSewaByPelanggan($id_pelanggan)
    {
        return $this->select('sewa.*, kasir.nama_user as nama_kasir, pembayaran.metode, pembayaran.status as status_pembayaran')
                    ->join('user as kasir', 'kasir.id_user = sewa.id_user', 'left')
                    ->join('pembayaran', 'pembayaran.id_sewa = sewa.id_sewa', 'left')
                    ->where('sewa.id_pelanggan', $id_pelanggan)
                    ->orderBy('sewa.id_sewa', 'DESC')
                    ->findAll();
    }
}
