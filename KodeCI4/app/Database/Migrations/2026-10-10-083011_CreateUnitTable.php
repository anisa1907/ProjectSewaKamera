<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnitTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_unit' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_inv' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'tanggal_beli' => [
                'type' => 'DATE',
            ],
            'harga_sewa' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'kondisi' => [
                'type' => 'ENUM',
                'constraint' => ['baik', 'rusak_ringan', 'rusak_berat'],
                'default' => 'baik',
            ],
            'status_unit' => [
                'type' => 'ENUM',
                'constraint' => ['tersedia', 'disewa', 'maintenance'],
                'default' => 'tersedia',
            ],
        ]);

        $this->forge->addKey('id_unit', true);
        $this->forge->addForeignKey(
            'id_inv', 'inventory', 'id_inv',
            'CASCADE', 'RESTRICT'
        );
        $this->forge->createTable('unit');
    }

    public function down()
    {
        $this->forge->dropTable('unit', true);
    }
}