<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceModel extends Model
{
    protected $table            = 'maintenance';
    protected $primaryKey       = 'id_maintenance';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_unit', 'tanggal_mulai', 'tanggal_selesai', 'biaya', 'keterangan'];

    public function getMaintenanceWithUnit($id_maintenance = null)
    {
        $builder = $this->select('maintenance.*, unit.kode_unit, inventory.nama_barang, inventory.merk')
                        ->join('unit', 'unit.id_unit = maintenance.id_unit')
                        ->join('inventory', 'inventory.id_inv = unit.id_inv');

        if ($id_maintenance !== null) {
            return $builder->where('maintenance.id_maintenance', $id_maintenance)->first();
        }

        return $builder->orderBy('maintenance.id_maintenance', 'DESC')->findAll();
    }
}
