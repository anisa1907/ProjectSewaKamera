<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_inv' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_kategori' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'kode_barang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_barang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'merk' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'tipe' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'deskripsi' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_inv', true);
        $this->forge->addForeignKey(
            'id_kategori', 'kategori', 'id_kategori',
            'CASCADE', 'RESTRICT'
        );
        $this->forge->createTable('inventory');
    }

    public function down()
    {
        $this->forge->dropTable('inventory', true);
    }
}
