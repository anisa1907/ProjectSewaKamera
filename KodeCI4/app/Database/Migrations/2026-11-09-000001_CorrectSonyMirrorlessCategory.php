<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CorrectSonyMirrorlessCategory extends Migration
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
            $idKategori = $kategori['id_kategori'];
        }

        $this->db->table('inventory')
            ->where('kode_barang', 'BRG-A7M3')
            ->update(['id_kategori' => $idKategori]);
    }

    public function down()
    {
        // Tidak mengembalikan Sony A7 III ke DSLR karena itu klasifikasi yang keliru.
    }
}
