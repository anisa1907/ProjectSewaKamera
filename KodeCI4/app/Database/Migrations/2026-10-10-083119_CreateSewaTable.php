<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSewaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sewa' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'id_pelanggan' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'tanggal_sewa' => [
                'type' => 'DATE',
            ],
            'tanggal_kembali' => [
                'type' => 'DATE',
            ],
            'jaminan' => [
                'type' => 'ENUM',
                'constraint' => ['KTP', 'KK', 'Akta'],
            ],
            'total_harga' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => [
                    'menunggu',
                    'dikonfirmasi',
                    'berlangsung',
                    'selesai',
                    'dibatalkan',
                ],
                'default' => 'menunggu',
            ],
        ]);

        $this->forge->addKey('id_sewa', true);
        $this->forge->addForeignKey(
            'id_user', 'user', 'id_user',
            'CASCADE', 'SET NULL'
        );
        $this->forge->addForeignKey(
            'id_pelanggan', 'user', 'id_user',
            'CASCADE', 'RESTRICT'
        );
        $this->forge->createTable('sewa');
    }

    public function down()
    {
        $this->forge->dropTable('sewa', true);
    }
}