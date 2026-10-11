<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use App\Models\UnitModel;
use App\Models\KategoriModel;
use App\Models\SewaModel;
use App\Models\DetailSewaModel;
use App\Models\PembayaranModel;

class Katalog extends BaseController
{
    protected $inventoryModel;
    protected $unitModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
        $this->unitModel      = new UnitModel();
        $this->kategoriModel  = new KategoriModel();
    }

    public function index()
    {
        $kategoriId = $this->request->getGet('kategori');

        $builder = $this->unitModel->select('unit.*, inventory.nama_barang, inventory.kode_barang, inventory.merk, inventory.tipe, inventory.deskripsi as desc_barang, kategori.nama_kategori')
                                   ->join('inventory', 'inventory.id_inv = unit.id_inv')
                                   ->join('kategori', 'kategori.id_kategori = inventory.id_kategori')
                                   ->where('unit.status_unit', 'tersedia');

        if (!empty($kategoriId)) {
            $builder->where('inventory.id_kategori', $kategoriId);
        }

        $units = $builder->findAll();
        $kategoriList = $this->kategoriModel->findAll();

        return view('pelanggan/katalog', [
            'title'        => 'Katalog Kamera & Perlengkapan',
            'units'        => $units,
            'kategoriList' => $kategoriList,
            'selectedKat'  => $kategoriId,
        ]);
    }
}
