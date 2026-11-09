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
        $db = \Config\Database::connect();

        echo '<pre>';

        echo "DATABASE YANG DIGUNAKAN APLIKASI:\n";

        print_r(
            $db->query("SELECT DATABASE() AS nama_database")->getRowArray()
        );

        echo "\nJUMLAH INVENTORY:\n";

        print_r(
            $db->query("SELECT COUNT(*) AS total FROM inventory")->getRowArray()
        );

        echo "\nJUMLAH UNIT TERSEDIA:\n";

        print_r(
            $db->query("
                SELECT COUNT(*) AS total
                FROM unit
                WHERE status_unit = 'tersedia'
            ")->getRowArray()
        );

        echo '</pre>';
        exit;

        // Ambil inventory sebagai dasar dan INNER JOIN ke unit.
        // Filter status_unit dilakukan dengan WHERE biasa (bukan kondisi
        // HAVING agregat) agar semua produk yang punya minimal satu unit
        // tersedia ikut muncul pada berbagai konfigurasi MySQL/MariaDB.
        $builder = $db->table('inventory i')
            ->select('i.id_inv, i.kode_barang, i.nama_barang, i.merk, i.tipe, i.deskripsi AS desc_barang, i.spesifikasi, k.nama_kategori, COUNT(u.id_unit) AS jumlah_tersedia')
            ->join('kategori k', 'k.id_kategori = i.id_kategori')
            ->join('unit u', 'u.id_inv = i.id_inv')
            ->where('u.status_unit', 'tersedia')
            ->groupBy(['i.id_inv', 'i.kode_barang', 'i.nama_barang', 'i.merk', 'i.tipe', 'i.deskripsi', 'i.spesifikasi', 'k.nama_kategori']);

        if ($kategoriId !== null && $kategoriId !== '') {
            $builder->where('i.id_kategori', (int) $kategoriId);
        }

        $produkRows = $builder
            ->orderBy('k.nama_kategori', 'ASC')
            ->orderBy('i.nama_barang', 'ASC')
            ->get()->getResultArray();

        $products = [];
        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk' . DIRECTORY_SEPARATOR;

        foreach ($produkRows as $produk) {
            // Ambil unit tersedia milik produk ini secara eksplisit.
            $units = $db->table('unit')
                ->select('id_unit, kode_unit, harga_sewa, kondisi')
                ->where('id_inv', (int) $produk['id_inv'])
                ->where('status_unit', 'tersedia')
                ->orderBy('kode_unit', 'ASC')
                ->get()->getResultArray();

            $produk['units'] = array_map(static function ($unit) {
                return [
                    'id_unit' => (int) $unit['id_unit'],
                    'kode_unit' => $unit['kode_unit'],
                    'harga_sewa' => (float) $unit['harga_sewa'],
                    'kondisi' => $unit['kondisi'],
                ];
            }, $units);
            $produk['jumlah_tersedia'] = count($produk['units']);
            $produk['gambar_url'] = null;

            // Cari gambar berdasarkan kode barang dan seluruh kode unit.
            $namaDasar = [preg_replace('/[^A-Za-z0-9_-]/', '', (string) $produk['kode_barang'])];
            foreach ($produk['units'] as $unit) {
                $namaDasar[] = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $unit['kode_unit']);
            }

            foreach ($namaDasar as $dasar) {
                foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
                    $namaFile = $dasar . '.' . $ext;
                    if ($dasar !== '' && is_file($uploadPath . $namaFile)) {
                        $produk['gambar_url'] = base_url('uploads/produk/' . rawurlencode($namaFile));
                        break 2;
                    }
                }
            }

            $products[] = $produk;
        }

        $kategoriList = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        return view('pelanggan/katalog', [
            'title' => 'Katalog Kamera & Perlengkapan',
            'products' => $products,
            'kategoriList' => $kategoriList,
            'selectedKat' => $kategoriId,
        ]);
    }

}
