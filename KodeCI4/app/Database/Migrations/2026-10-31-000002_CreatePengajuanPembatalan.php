<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreatePengajuanPembatalan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengajuan' => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'id_sewa' => ['type'=>'INT','unsigned'=>true],
            'id_pelanggan' => ['type'=>'INT','unsigned'=>true],
            'alasan' => ['type'=>'TEXT'],
            'status' => ['type'=>'ENUM','constraint'=>['menunggu','disetujui','ditolak'],'default'=>'menunggu'],
            'id_kasir' => ['type'=>'INT','unsigned'=>true,'null'=>true],
            'catatan_kasir' => ['type'=>'TEXT','null'=>true],
            'dibuat_pada' => ['type'=>'DATETIME'],
            'diproses_pada' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id_pengajuan', true);
        $this->forge->addKey('id_sewa');
        $this->forge->createTable('pengajuan_pembatalan');
    }
    public function down() { $this->forge->dropTable('pengajuan_pembatalan', true); }
}
