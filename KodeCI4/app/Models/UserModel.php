<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_user', 'username', 'password', 'role'];

    protected $validationRules = [
        'nama_user' => 'required|min_length[3]|max_length[100]',
        'username'  => 'required|min_length[3]|max_length[50]|is_unique[user.username,id_user,{id_user}]',
        'role'      => 'required|in_list[owner,kasir,pelanggan]',
    ];
}
