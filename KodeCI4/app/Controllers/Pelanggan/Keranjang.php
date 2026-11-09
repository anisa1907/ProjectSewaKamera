<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\UnitModel;

class Keranjang extends BaseController
{
    protected $unitModel;

    public function __construct()
    {
        $this->unitModel = new UnitModel();
    }

    private function cart(): array
    {
        return array_values(array_unique(array_map('intval', session()->get('keranjang_sewa') ?? [])));
    }

    public function index()
    {
        $ids = $this->cart();
        $items = [];
        if ($ids) {
            $items = $this->unitModel->select('unit.*, inventory.nama_barang, inventory.merk, inventory.tipe, kategori.nama_kategori')
                ->join('inventory', 'inventory.id_inv = unit.id_inv')
                ->join('kategori', 'kategori.id_kategori = inventory.id_kategori')
                ->whereIn('unit.id_unit', $ids)->where('unit.status_unit', 'tersedia')->findAll();
            // Unit yang tidak lagi tersedia dikeluarkan agar tidak dapat di-checkout.
            $validIds = array_map('intval', array_column($items, 'id_unit'));
            session()->set('keranjang_sewa', $validIds);
        }
        return view('pelanggan/keranjang', ['title' => 'Keranjang Sewa', 'items' => $items]);
    }

    public function tambah()
    {
        $id = (int) $this->request->getPost('id_unit');
        $unit = $this->unitModel->where('id_unit', $id)->where('status_unit', 'tersedia')->first();
        if (!$unit) return redirect()->back()->with('error', 'Unit tidak tersedia atau sudah dipesan.');
        $ids = $this->cart();
        if (!in_array($id, $ids, true)) $ids[] = $id;
        session()->set('keranjang_sewa', $ids);
        return redirect()->to(base_url('pelanggan/keranjang'))->with('success', 'Unit berhasil ditambahkan ke keranjang.');
    }

    public function hapus($id)
    {
        $ids = array_values(array_filter($this->cart(), fn($item) => $item !== (int) $id));
        session()->set('keranjang_sewa', $ids);
        return redirect()->to(base_url('pelanggan/keranjang'))->with('success', 'Unit dihapus dari keranjang.');
    }

    public function kosongkan()
    {
        session()->remove('keranjang_sewa');
        return redirect()->to(base_url('pelanggan/keranjang'))->with('success', 'Keranjang dikosongkan.');
    }
}
