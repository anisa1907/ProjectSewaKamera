<?php

namespace App\Models;

use CodeIgniter\Model;

class PengembalianModel extends Model
{
    protected $table            = 'pengembalian';
    protected $primaryKey       = 'id_pengembalian';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_sewa', 'tanggal_kembali', 'kondisi', 'denda'];

    public function getPengembalianWithSewa($id_pengembalian = null)
    {
        $builder = $this->select('pengembalian.*, sewa.tanggal_sewa, user.nama_user as nama_pelanggan')
                        ->join('sewa', 'sewa.id_sewa = pengembalian.id_sewa')
                        ->join('user', 'user.id_user = sewa.id_pelanggan');

        if ($id_pengembalian !== null) {
            return $builder->where('pengembalian.id_pengembalian', $id_pengembalian)->first();
        }

        return $builder->orderBy('pengembalian.id_pengembalian', 'DESC')->findAll();
    }
}
