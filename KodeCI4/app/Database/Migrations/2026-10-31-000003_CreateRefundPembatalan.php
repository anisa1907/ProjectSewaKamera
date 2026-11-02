<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateRefundPembatalan extends Migration {
 public function up() {
  $this->forge->addField([
   'id_refund'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
   'id_sewa'=>['type'=>'INT','unsigned'=>true],
   'id_pembayaran'=>['type'=>'INT','unsigned'=>true,'null'=>true],
   'jumlah'=>['type'=>'DECIMAL','constraint'=>'12,2'],
   'status'=>['type'=>'VARCHAR','constraint'=>30,'default'=>'perlu_verifikasi'],
   'catatan'=>['type'=>'TEXT','null'=>true],
   'bukti_refund'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],
   'id_kasir'=>['type'=>'INT','unsigned'=>true,'null'=>true],
   'dibuat_pada'=>['type'=>'DATETIME'],
   'diproses_pada'=>['type'=>'DATETIME','null'=>true],
  ]);
  $this->forge->addKey('id_refund',true);
  $this->forge->addUniqueKey('id_sewa');
  $this->forge->createTable('refund_pembatalan',true);
  // Migrasi transaksi yang telah dibatalkan sebelum fitur refund tersedia.
  $db=\Config\Database::connect();
  $old=$db->table('sewa s')->select('s.id_sewa,p.id_pembayaran,p.jumlah,p.status AS status_bayar')
   ->join('pembayaran p','p.id_sewa=s.id_sewa')->where('s.status','dibatalkan')
   ->where('s.sumber_transaksi','pelanggan')->get()->getResultArray();
  foreach ($old as $r) {
   if ($db->table('refund_pembatalan')->where('id_sewa',$r['id_sewa'])->countAllResults()) continue;
   $db->table('refund_pembatalan')->insert([
    'id_sewa'=>$r['id_sewa'],'id_pembayaran'=>$r['id_pembayaran'],'jumlah'=>$r['jumlah'],
    'status'=>$r['status_bayar']==='terbayar'?'menunggu_refund':'perlu_verifikasi',
    'catatan'=>'Data pembatalan lama: periksa bukti dan mutasi rekening sebelum memutuskan refund.',
    'dibuat_pada'=>date('Y-m-d H:i:s'),
   ]);
  }
 }
 public function down() {$this->forge->dropTable('refund_pembatalan',true);}
}
