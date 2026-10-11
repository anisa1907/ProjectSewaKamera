<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Data User (Owner, Kasir, Pelanggan)
        $users = [
            [
                'id_user'   => 1,
                'nama_user' => 'Pemilik Toko (Owner)',
                'username'  => 'owner',
                'password'  => password_hash('owner123', PASSWORD_BCRYPT),
                'role'      => 'owner',
            ],
            [
                'id_user'   => 2,
                'nama_user' => 'Kasir Utama',
                'username'  => 'kasir',
                'password'  => password_hash('kasir123', PASSWORD_BCRYPT),
                'role'      => 'kasir',
            ],
            [
                'id_user'   => 3,
                'nama_user' => 'Nazwa Hardiana',
                'username'  => 'nazwa',
                'password'  => password_hash('nazwa123', PASSWORD_BCRYPT),
                'role'      => 'pelanggan',
            ],
        ];

        foreach ($users as $user) {
            $existing = $this->db->table('user')->where('username', $user['username'])->get()->getRow();
            if (!$existing) {
                $this->db->table('user')->insert($user);
            }
        }

        // 2. Data Kategori
        $kategori = [
            ['id_kategori' => 1, 'nama_kategori' => 'Kamera DSLR', 'deskripsi' => 'Kamera reflex lensa tunggal digital dengan sensor profesional'],
            ['id_kategori' => 2, 'nama_kategori' => 'Kamera Digicam', 'deskripsi' => 'Kamera saku digital praktis, trend vintage & compact'],
            ['id_kategori' => 3, 'nama_kategori' => 'Lensa Kamera', 'deskripsi' => 'Lensa fix, zoom, dan telephoto untuk berbagai mount'],
            ['id_kategori' => 4, 'nama_kategori' => 'Drone', 'deskripsi' => 'Wahana tanpa awak untuk videografi & pemetaan udara'],
        ];

        foreach ($kategori as $kat) {
            $existing = $this->db->table('kategori')->where('id_kategori', $kat['id_kategori'])->get()->getRow();
            if (!$existing) {
                $this->db->table('kategori')->insert($kat);
            }
        }

        // 3. Data Inventory (Produk)
        $inventory = [
            [
                'id_inv'      => 1,
                'id_kategori' => 1,
                'kode_barang' => 'BRG-EOS90D',
                'nama_barang' => 'Canon EOS 90D Body',
                'merk'        => 'Canon',
                'tipe'        => 'DSLR APS-C 32.5MP 4K',
                'deskripsi'   => 'Kamera DSLR serbaguna beresolusi tinggi dengan kemampuan video 4K tanpa crop.',
            ],
            [
                'id_inv'      => 2,
                'id_kategori' => 1,
                'kode_barang' => 'BRG-A7M3',
                'nama_barang' => 'Sony Alpha A7 III Body',
                'merk'        => 'Sony',
                'tipe'        => 'Mirrorless Full Frame 24.2MP',
                'deskripsi'   => 'Standar kamera full-frame profesional dengan autofokus cepat dan low light superior.',
            ],
            [
                'id_inv'      => 3,
                'id_kategori' => 2,
                'kode_barang' => 'BRG-G7X3',
                'nama_barang' => 'Canon PowerShot G7X Mark III',
                'merk'        => 'Canon',
                'tipe'        => 'Compact Digicam 1-inch Sensor',
                'deskripsi'   => 'Digicam premium favorit vlogger & event kasual berukuran saku.',
            ],
            [
                'id_inv'      => 4,
                'id_kategori' => 3,
                'kode_barang' => 'BRG-LNS2470',
                'nama_barang' => 'Sony FE 24-70mm f/2.8 GM',
                'merk'        => 'Sony',
                'tipe'        => 'Lensa Zoom G Master',
                'deskripsi'   => 'Lensa zoom standar tajam dengan bukaan f/2.8 konstan untuk potret dan video.',
            ],
            [
                'id_inv'      => 5,
                'id_kategori' => 4,
                'kode_barang' => 'BRG-AIR2S',
                'nama_barang' => 'DJI Air 2S Fly More Combo',
                'merk'        => 'DJI',
                'tipe'        => 'Drone Quadcopter 1-Inch CMOS 5.4K',
                'deskripsi'   => 'Drone ringkas dengan sensor 1 inci yang menghasilkan visual udara sinematik.',
            ],
        ];

        foreach ($inventory as $inv) {
            $existing = $this->db->table('inventory')->where('id_inv', $inv['id_inv'])->get()->getRow();
            if (!$existing) {
                $this->db->table('inventory')->insert($inv);
            }
        }

        // 4. Data Unit Fisik
        $units = [
            [
                'id_unit'      => 1,
                'id_inv'       => 1,
                'kode_unit'    => 'Cam-Eos90D-01',
                'tanggal_beli' => '2024-01-15',
                'harga_sewa'   => 150000,
                'kondisi'      => 'baik',
                'status_unit'  => 'tersedia',
            ],
            [
                'id_unit'      => 2,
                'id_inv'       => 1,
                'kode_unit'    => 'Cam-Eos90D-02',
                'tanggal_beli' => '2024-02-10',
                'harga_sewa'   => 150000,
                'kondisi'      => 'baik',
                'status_unit'  => 'tersedia',
            ],
            [
                'id_unit'      => 3,
                'id_inv'       => 2,
                'kode_unit'    => 'Cam-SonyA73-01',
                'tanggal_beli' => '2023-11-20',
                'harga_sewa'   => 250000,
                'kondisi'      => 'baik',
                'status_unit'  => 'tersedia',
            ],
            [
                'id_unit'      => 4,
                'id_inv'       => 3,
                'kode_unit'    => 'Cam-G7X-01',
                'tanggal_beli' => '2024-03-05',
                'harga_sewa'   => 100000,
                'kondisi'      => 'baik',
                'status_unit'  => 'tersedia',
            ],
            [
                'id_inv'       => 4,
                'id_unit'      => 5,
                'kode_unit'    => 'Lns-Sony2470-01',
                'tanggal_beli' => '2024-01-18',
                'harga_sewa'   => 120000,
                'kondisi'      => 'baik',
                'status_unit'  => 'tersedia',
            ],
            [
                'id_unit'      => 6,
                'id_inv'       => 5,
                'kode_unit'    => 'Drn-Air2S-01',
                'tanggal_beli' => '2024-04-01',
                'harga_sewa'   => 300000,
                'kondisi'      => 'baik',
                'status_unit'  => 'tersedia',
            ],
        ];

        foreach ($units as $u) {
            $existing = $this->db->table('unit')->where('id_unit', $u['id_unit'])->get()->getRow();
            if (!$existing) {
                $this->db->table('unit')->insert($u);
            }
        }
    }
}
