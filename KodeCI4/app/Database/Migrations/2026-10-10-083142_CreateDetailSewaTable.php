<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailSewaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_sewa' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'id_unit' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'harga_sewa' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
        ]);

        $this->forge->addKey('id_detail', true);
        $this->forge->addForeignKey(
            'id_sewa', 'sewa', 'id_sewa',
            'CASCADE', 'CASCADE'
        );
        $this->forge->addForeignKey(
            'id_unit', 'unit', 'id_unit',
            'CASCADE', 'RESTRICT'
        );
        $this->forge->createTable('detail_sewa');
    }

    public function down()
    {
        $this->forge->dropTable('detail_sewa', true);
    }
}
