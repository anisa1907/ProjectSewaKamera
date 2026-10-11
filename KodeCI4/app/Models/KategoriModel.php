<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_kategori', 'deskripsi'];

    protected $validationRules = [
        'nama_kategori' => 'required|min_length[3]|max_length[50]',
    ];
}
