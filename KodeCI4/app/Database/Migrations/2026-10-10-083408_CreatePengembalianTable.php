<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengembalianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengembalian' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_sewa' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'tanggal_kembali' => [
                'type' => 'DATE',
            ],
            'kondisi' => [
                'type' => 'ENUM',
                'constraint' => ['baik', 'rusak_ringan', 'rusak_berat'],
            ],
            'denda' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id_pengembalian', true);
        $this->forge->addForeignKey(
            'id_sewa', 'sewa', 'id_sewa',
            'CASCADE', 'CASCADE'
        );
        $this->forge->createTable('pengembalian');
    }

    public function down()
    {
        $this->forge->dropTable('pengembalian', true);
    }
}