<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id_pembayaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_sewa', 'metode', 'jumlah', 'tanggal_bayar', 'status'];

    public function getPembayaranWithSewa($id_pembayaran = null)
    {
        $builder = $this->select('pembayaran.*, sewa.tanggal_sewa, sewa.tanggal_kembali, sewa.id_pelanggan, user.nama_user as nama_pelanggan')
                        ->join('sewa', 'sewa.id_sewa = pembayaran.id_sewa')
                        ->join('user', 'user.id_user = sewa.id_pelanggan');

        if ($id_pembayaran !== null) {
            return $builder->where('pembayaran.id_pembayaran', $id_pembayaran)->first();
        }

        return $builder->orderBy('pembayaran.id_pembayaran', 'DESC')->findAll();
    }
}
