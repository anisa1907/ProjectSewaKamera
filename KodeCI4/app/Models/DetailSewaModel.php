<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailSewaModel extends Model
{
    protected $table            = 'detail_sewa';
    protected $primaryKey       = 'id_detail';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_sewa', 'id_unit', 'harga_sewa', 'subtotal'];

    public function getItemsBySewa($id_sewa)
    {
        return $this->select('detail_sewa.*, unit.kode_unit, inventory.nama_barang, inventory.merk, inventory.tipe, kategori.nama_kategori')
                    ->join('unit', 'unit.id_unit = detail_sewa.id_unit')
                    ->join('inventory', 'inventory.id_inv = unit.id_inv')
                    ->join('kategori', 'kategori.id_kategori = inventory.id_kategori')
                    ->where('detail_sewa.id_sewa', $id_sewa)
                    ->findAll();
    }
}
