<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman Utama mengarah ke login / katalog
$routes->get('/', 'Auth::login');

// Autentikasi
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::processLogin');
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::processRegister');
    $routes->get('logout', 'Auth::logout');
});

// Pelanggan Routes
$routes->group('pelanggan', ['filter' => ['auth', 'role:pelanggan']], function ($routes) {
    $routes->get('katalog', 'Pelanggan\Katalog::index');
    $routes->get('keranjang', 'Pelanggan\Keranjang::index');
    $routes->post('keranjang/tambah', 'Pelanggan\Keranjang::tambah');
    $routes->post('keranjang/hapus/(:num)', 'Pelanggan\Keranjang::hapus/$1');
    $routes->post('keranjang/kosongkan', 'Pelanggan\Keranjang::kosongkan');
    $routes->get('checkout', 'Pelanggan\Sewa::checkout');
    $routes->get('sewa/(:num)', 'Pelanggan\Sewa::checkout/$1');
    $routes->post('sewa/process', 'Pelanggan\Sewa::processCheckout');
    $routes->get('status-sewa', 'Pelanggan\StatusSewa::index');
    $routes->post('status-sewa/batalkan/(:num)', 'Pelanggan\StatusSewa::ajukanPembatalan/$1');
    $routes->get('invoice/(:num)', 'Pelanggan\StatusSewa::invoice/$1');
});

// Kasir Routes
$routes->group('kasir', ['filter' => ['auth', 'role:kasir']], function ($routes) {
    $routes->get('dashboard', 'Kasir\Dashboard::index');
    
    // Master Data
    $routes->get('master-data', 'Kasir\MasterData::index');
    $routes->post('master-data/kategori', 'Kasir\MasterData::saveKategori');
    $routes->post('master-data/inventory', 'Kasir\MasterData::saveInventory');
    $routes->post('master-data/unit', 'Kasir\MasterData::saveUnit');

    $routes->get('sewa-langsung', 'Kasir\Transaksi::directRental');
    $routes->post('sewa-langsung', 'Kasir\Transaksi::simpanDirectRental');
    $routes->get('notifikasi-pembatalan', 'Kasir\Transaksi::notifikasiPembatalan');
    $routes->get('histori', 'Kasir\Transaksi::histori');
    $routes->post('pembatalan/keputusan/(:num)', 'Kasir\Transaksi::keputusanPembatalan/$1');
    $routes->post('refund/proses/(:num)', 'Kasir\Transaksi::prosesRefund/$1');
    $routes->get('durasi', 'Kasir\Transaksi::durasi');
    // Transaksi & Pembayaran
    $routes->get('transaksi', 'Kasir\Transaksi::index');
    $routes->post('transaksi/verifikasi-bayar/(:num)', 'Kasir\Transaksi::verifikasiPembayaran/$1');
    $routes->post('transaksi/serah-unit/(:num)', 'Kasir\Transaksi::serahkanUnit/$1');
    $routes->post('transaksi/pengembalian/(:num)', 'Kasir\Transaksi::pengembalian/$1');

    // Maintenance
    $routes->get('maintenance', 'Kasir\Maintenance::index');
    $routes->post('maintenance/mulai', 'Kasir\Maintenance::mulai');
    $routes->post('maintenance/selesai/(:num)', 'Kasir\Maintenance::selesai/$1');
});

// Owner Routes
$routes->group('owner', ['filter' => ['auth', 'role:owner']], function ($routes) {
    $routes->get('dashboard', 'Owner\Dashboard::index');
    $routes->get('laporan', 'Owner\Laporan::index');
    $routes->get('laporan/cetak', 'Owner\Laporan::cetak');
});
