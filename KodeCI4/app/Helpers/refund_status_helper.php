<?php
/** Status tampilan terpadu; tidak mengubah status pembayaran asli. */
if (!function_exists('status_bayar_terpadu')) {
    function status_bayar_terpadu(array $sewa): array
    {
        static $refunds = null;
        if ($refunds === null) {
            $refunds = [];
            $db = \Config\Database::connect();
            if ($db->tableExists('refund_pembatalan')) {
                foreach ($db->table('refund_pembatalan')->select('id_sewa, status')->get()->getResultArray() as $r) {
                    $refunds[$r['id_sewa']] = $r['status'];
                }
            }
        }
        if (($sewa['status'] ?? '') === 'dibatalkan') {
            $state = $refunds[$sewa['id_sewa']] ?? null;
            $labels = [
                'perlu_verifikasi' => ['Bukti transfer diperiksa', 'bg-info-subtle text-dark'],
                'menunggu_refund' => ['Menunggu refund 100%', 'bg-warning text-dark'],
                'selesai' => ['Dana dikembalikan', 'bg-success'],
                'tidak_ada_dana' => ['Tidak ada dana diterima', 'bg-secondary'],
            ];
            return $labels[$state] ?? ['Pembayaran dibatalkan', 'bg-secondary'];
        }
        return match ($sewa['status_pembayaran'] ?? 'menunggu') {
            'terbayar' => ['Terbayar', 'bg-success'],
            'ditolak' => ['Ditolak', 'bg-danger'],
            default => ['Menunggu Konfirmasi', 'bg-warning text-dark'],
        };
    }
}
