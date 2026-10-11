<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaintenanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_maintenance' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_unit' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'biaya' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $this->forge->addKey('id_maintenance', true);
        $this->forge->addForeignKey(
            'id_unit', 'unit', 'id_unit',
            'CASCADE', 'RESTRICT'
        );
        $this->forge->createTable('maintenance');
    }

    public function down()
    {
        $this->forge->dropTable('maintenance', true);
    }
}