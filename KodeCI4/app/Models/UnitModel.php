<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'unit';
    protected $primaryKey       = 'id_unit';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_inv', 'kode_unit', 'tanggal_beli', 'harga_sewa', 'kondisi', 'status_unit'];

    protected $validationRules = [
        'id_inv'      => 'required|is_natural_no_zero',
        'kode_unit'   => 'required|max_length[50]',
        'harga_sewa'  => 'required|numeric',
        'kondisi'     => 'required|in_list[baik,rusak_ringan,rusak_berat]',
        'status_unit' => 'required|in_list[tersedia,disewa,maintenance]',
    ];

    public function getUnitsFull($id_unit = null)
    {
        $builder = $this->select('unit.*, inventory.nama_barang, inventory.kode_barang, inventory.merk, inventory.tipe, kategori.nama_kategori')
                        ->join('inventory', 'inventory.id_inv = unit.id_inv')
                        ->join('kategori', 'kategori.id_kategori = inventory.id_kategori');

        if ($id_unit !== null) {
            return $builder->where('unit.id_unit', $id_unit)->first();
        }

        return $builder->findAll();
    }

    public function getAvailableUnits()
    {
        return $this->select('unit.*, inventory.nama_barang, inventory.kode_barang, inventory.merk, inventory.tipe, kategori.nama_kategori')
                    ->join('inventory', 'inventory.id_inv = unit.id_inv')
                    ->join('kategori', 'kategori.id_kategori = inventory.id_kategori')
                    ->where('unit.status_unit', 'tersedia')
                    ->findAll();
    }
}
