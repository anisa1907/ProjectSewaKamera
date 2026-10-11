<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\InventoryModel;
use App\Models\UnitModel;

class MasterData extends BaseController
{
    protected $kategoriModel;
    protected $inventoryModel;
    protected $unitModel;

    public function __construct()
    {
        $this->kategoriModel  = new KategoriModel();
        $this->inventoryModel = new InventoryModel();
        $this->unitModel      = new UnitModel();
    }

    public function index()
    {
        $kategori  = $this->kategoriModel->findAll();
        $inventory = $this->inventoryModel->getInventoryWithKategori();
        $units     = $this->unitModel->getUnitsFull();

        return view('kasir/master_data', [
            'title'     => 'Kelola Master Data Barang & Unit',
            'kategori'  => $kategori,
            'inventory' => $inventory,
            'units'     => $units,
        ]);
    }

    public function saveKategori()
    {
        $this->kategoriModel->insert([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to(base_url('kasir/master-data'))->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function saveInventory()
    {
        $this->inventoryModel->insert([
            'id_kategori' => $this->request->getPost('id_kategori'),
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'merk'        => $this->request->getPost('merk'),
            'tipe'        => $this->request->getPost('tipe'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to(base_url('kasir/master-data'))->with('success', 'Produk inventori berhasil ditambahkan.');
    }

    public function saveUnit()
    {
        $this->unitModel->insert([
            'id_inv'       => $this->request->getPost('id_inv'),
            'kode_unit'    => $this->request->getPost('kode_unit'),
            'tanggal_beli' => $this->request->getPost('tanggal_beli'),
            'harga_sewa'   => $this->request->getPost('harga_sewa'),
            'kondisi'      => $this->request->getPost('kondisi'),
            'status_unit'  => 'tersedia',
        ]);

        return redirect()->to(base_url('kasir/master-data'))->with('success', 'Unit individual baru berhasil ditambahkan.');
    }
}
