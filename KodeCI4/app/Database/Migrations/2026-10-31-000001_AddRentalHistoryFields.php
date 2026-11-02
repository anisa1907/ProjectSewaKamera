<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class AddRentalHistoryFields extends Migration {
 public function up() {
  $this->forge->addColumn('sewa', [
   'sumber_transaksi'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'pelanggan'],
   'waktu_mulai'=>['type'=>'DATETIME','null'=>true],
   'waktu_selesai'=>['type'=>'DATETIME','null'=>true],
   'alasan_pembatalan'=>['type'=>'TEXT','null'=>true],
   'dibatalkan_pada'=>['type'=>'DATETIME','null'=>true],
  ]);
  $this->forge->addColumn('inventory', ['spesifikasi'=>['type'=>'TEXT','null'=>true]]);
  $this->forge->addField([
   'id_log'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
   'id_sewa'=>['type'=>'INT','unsigned'=>true],
   'id_user'=>['type'=>'INT','unsigned'=>true,'null'=>true],
   'aktivitas'=>['type'=>'VARCHAR','constraint'=>60],
   'keterangan'=>['type'=>'TEXT','null'=>true],
   'dibuat_pada'=>['type'=>'DATETIME'],
  ]);
  $this->forge->addKey('id_log',true);
  $this->forge->addKey('id_sewa');
  $this->forge->createTable('histori_transaksi');
 }
 public function down() {
  $this->forge->dropTable('histori_transaksi',true);
  $this->forge->dropColumn('inventory','spesifikasi');
  foreach (['sumber_transaksi','waktu_mulai','waktu_selesai','alasan_pembatalan','dibatalkan_pada'] as $col) $this->forge->dropColumn('sewa',$col);
 }
}
