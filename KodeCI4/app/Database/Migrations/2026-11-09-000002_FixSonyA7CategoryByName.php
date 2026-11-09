<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixSonyA7CategoryByName extends Migration
{
    public function up()
    {
        $kategori = $this->db->table('kategori')
            ->where('nama_kategori', 'Kamera Mirrorless')
            ->get()->getRowArray();

        if (!$kategori) {
            $this->db->table('kategori')->insert([
                'nama_kategori' => 'Kamera Mirrorless',
                'deskripsi' => 'Kamera tanpa cermin dengan lensa yang dapat diganti',
            ]);
            $idKategori = $this->db->insertID();
        } else {
            $idKategori = (int) $kategori['id_kategori'];
        }

        // Gunakan kode barang ATAU nama produk agar tetap cocok dengan
        // database yang kode barangnya berbeda dari data seeder.
        $this->db->table('inventory')
            ->groupStart()
                ->where('kode_barang', 'BRG-A7M3')
                ->orLike('nama_barang', 'Sony Alpha A7 III')
            ->groupEnd()
            ->update(['id_kategori' => $idKategori]);
    }

    public function down()
    {
        // Tidak mengembalikan klasifikasi kamera ke kategori DSLR.
    }
}
