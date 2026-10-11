<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table            = 'inventory';
    protected $primaryKey       = 'id_inv';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_kategori', 'kode_barang', 'nama_barang', 'merk', 'tipe', 'deskripsi'];

    protected $validationRules = [
        'id_kategori' => 'required|is_natural_no_zero',
        'kode_barang' => 'required|max_length[50]',
        'nama_barang' => 'required|max_length[100]',
        'merk'        => 'required|max_length[50]',
    ];

    public function getInventoryWithKategori($id_inv = null)
    {
        $builder = $this->select('inventory.*, kategori.nama_kategori')
                        ->join('kategori', 'kategori.id_kategori = inventory.id_kategori');

        if ($id_inv !== null) {
            return $builder->where('inventory.id_inv', $id_inv)->first();
        }

        return $builder->findAll();
    }
}
