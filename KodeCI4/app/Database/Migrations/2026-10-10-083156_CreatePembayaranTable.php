<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pembayaran' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_sewa' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'metode' => [
                'type' => 'ENUM',
                'constraint' => ['QRIS', 'Cash'],
            ],
            'jumlah' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['menunggu', 'terbayar', 'ditolak'],
                'default' => 'menunggu',
            ],
        ]);

        $this->forge->addKey('id_pembayaran', true);
        $this->forge->addForeignKey(
            'id_sewa', 'sewa', 'id_sewa',
            'CASCADE', 'CASCADE'
        );
        $this->forge->createTable('pembayaran');
    }

    public function down()
    {
        $this->forge->dropTable('pembayaran', true);
    }
}