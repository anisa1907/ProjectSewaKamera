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
        $tersimpan = $this->inventoryModel->insert([
            'id_kategori' => $this->request->getPost('id_kategori'),
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'merk'        => $this->request->getPost('merk'),
            'tipe'        => $this->request->getPost('tipe'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'spesifikasi' => $this->request->getPost('spesifikasi'),
        ]);

        if (!$tersimpan || $this->inventoryModel->errors()) {
            $errors = $this->inventoryModel->errors();
            return redirect()->back()->withInput()->with('error', 'Produk gagal disimpan: ' . (!empty($errors) ? implode(', ', $errors) : 'Periksa kembali data produk.'));
        }

        $idInv = $this->inventoryModel->getInsertID();
        $hasilGambar = $this->simpanGambarProduk($idInv);
        if ($hasilGambar !== true) {
            return redirect()->to(base_url('kasir/master-data'))->with('warning', 'Produk tersimpan, tetapi gambar tidak diunggah: ' . $hasilGambar);
        }

        return redirect()->to(base_url('kasir/master-data'))->with('success', 'Produk inventori berhasil ditambahkan.');
    }

    /** Mengganti foto produk yang sudah terdaftar tanpa menyimpan path di database. */
    public function uploadGambar($idInv)
    {
        $produk = $this->inventoryModel->find($idInv);
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $hasil = $this->simpanGambarProduk((int) $idInv, $produk['kode_barang']);
        if ($hasil !== true) {
            return redirect()->to(base_url('kasir/master-data'))->with('error', $hasil);
        }

        return redirect()->to(base_url('kasir/master-data'))->with('success', 'Gambar produk berhasil diunggah/diganti.');
    }

    private function simpanGambarProduk(int $idInv, ?string $kodeBarang = null)
    {
        $file = $this->request->getFile('gambar_produk');
        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return true; // Foto bersifat opsional saat menambah produk.
        }
        if (!$file->isValid()) {
            return 'File gambar gagal diunggah.';
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            return 'Ukuran gambar maksimal 2 MB.';
        }

        $mime = $file->getMimeType();
        $formatDiizinkan = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($formatDiizinkan[$mime]) || ! @getimagesize($file->getTempName())) {
            return 'Format gambar harus JPG, PNG, atau WebP yang valid.';
        }

        if ($kodeBarang === null) {
            $produk = $this->inventoryModel->find($idInv);
            $kodeBarang = $produk['kode_barang'] ?? '';
        }
        $namaAman = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $kodeBarang);
        if ($namaAman === '') {
            return 'Kode barang tidak valid untuk nama file gambar.';
        }

        $direktori = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk';
        if (!is_dir($direktori) && ! mkdir($direktori, 0755, true) && !is_dir($direktori)) {
            return 'Folder penyimpanan gambar tidak dapat dibuat.';
        }

        // Hapus versi lama agar tidak menumpuk ketika foto diganti.
        foreach (['webp', 'jpg', 'jpeg', 'png'] as $extLama) {
            $lama = $direktori . DIRECTORY_SEPARATOR . $namaAman . '.' . $extLama;
            if (is_file($lama)) {
                @unlink($lama);
            }
        }

        $ekstensi = $formatDiizinkan[$mime];
        $file->move($direktori, $namaAman . '.' . $ekstensi, true);
        return true;
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
