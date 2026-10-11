<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKodeUnitToUnitTable extends Migration
{
    public function up()
    {
        $fields = [
            'kode_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id_inv',
            ],
        ];
        if (!$this->db->fieldExists('kode_unit', 'unit')) {
            $this->forge->addColumn('unit', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('kode_unit', 'unit')) {
            $this->forge->dropColumn('unit', 'kode_unit');
        }
    }
}
