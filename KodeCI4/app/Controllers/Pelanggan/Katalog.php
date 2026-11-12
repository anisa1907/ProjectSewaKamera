<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Katalog extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $kategoriId = $this->request->getGet('kategori');
        $db = \Config\Database::connect();

        // Inventory menjadi tabel utama agar produk tetap tampil walaupun
        // seluruh unitnya sedang disewa atau dalam perawatan.
        $builder = $db->table('inventory i')
            ->select('i.id_inv, i.kode_barang, i.nama_barang, i.merk, i.tipe, i.deskripsi AS desc_barang, i.spesifikasi, k.nama_kategori')
            ->join('kategori k', 'k.id_kategori = i.id_kategori', 'left');

        if ($kategoriId !== null && $kategoriId !== '') {
            $builder->where('i.id_kategori', (int) $kategoriId);
        }

        $products = $builder
            ->orderBy('k.nama_kategori', 'ASC')
            ->orderBy('i.nama_barang', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil semua unit (tersedia, disewa, maupun maintenance) sekali query.
        // Data semua unit dipakai untuk mencari foto, sedangkan pilihan sewa
        // hanya mengambil unit dengan status tersedia.
        $unitByInventory = [];
        if (!empty($products)) {
            $inventoryIds = array_map('intval', array_column($products, 'id_inv'));
            $allUnits = $db->table('unit')
                ->select('id_unit, id_inv, kode_unit, harga_sewa, kondisi, status_unit')
                ->whereIn('id_inv', $inventoryIds)
                ->orderBy('kode_unit', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($allUnits as $unit) {
                $unitByInventory[(int) $unit['id_inv']][] = $unit;
            }
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'produk' . DIRECTORY_SEPARATOR;

        foreach ($products as &$produk) {
            $idInv = (int) $produk['id_inv'];
            $allProductUnits = $unitByInventory[$idInv] ?? [];

            $availableUnits = array_values(array_filter(
                $allProductUnits,
                static fn(array $unit): bool => strtolower(trim((string) $unit['status_unit'])) === 'tersedia'
            ));

            $produk['units'] = array_map(static function (array $unit): array {
                return [
                    'id_unit' => (int) $unit['id_unit'],
                    'kode_unit' => $unit['kode_unit'],
                    'harga_sewa' => (float) $unit['harga_sewa'],
                    'kondisi' => $unit['kondisi'],
                ];
            }, $availableUnits);
            $produk['jumlah_tersedia'] = count($produk['units']);
            $produk['total_unit'] = count($allProductUnits);
            $produk['gambar_url'] = null;

            // Prioritaskan foto dengan kode barang, lalu cari berdasarkan
            // SEMUA kode unit termasuk unit yang sedang disewa/maintenance.
            $namaDasar = [preg_replace('/[^A-Za-z0-9_-]/', '', (string) $produk['kode_barang'])];
            foreach ($allProductUnits as $unit) {
                $namaDasar[] = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $unit['kode_unit']);
            }

            foreach (array_unique($namaDasar) as $dasar) {
                if ($dasar === '') {
                    continue;
                }
                foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
                    $namaFile = $dasar . '.' . $ext;
                    if (is_file($uploadPath . $namaFile)) {
                        $produk['gambar_url'] = base_url('uploads/produk/' . rawurlencode($namaFile));
                        break 2;
                    }
                }
            }
        }
        unset($produk);

        $kategoriList = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        return view('pelanggan/katalog', [
            'title' => 'Katalog Kamera & Perlengkapan',
            'products' => $products,
            'kategoriList' => $kategoriList,
            'selectedKat' => $kategoriId,
        ]);
    }
}
