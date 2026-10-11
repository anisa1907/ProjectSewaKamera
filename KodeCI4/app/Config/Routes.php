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
    $routes->get('sewa/(:num)', 'Pelanggan\Sewa::checkout/$1');
    $routes->post('sewa/process', 'Pelanggan\Sewa::processCheckout');
    $routes->get('status-sewa', 'Pelanggan\StatusSewa::index');
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

    // Transaksi & Pembayaran
    $routes->get('transaksi', 'Kasir\Transaksi::index');
    $routes->post('transaksi/verifikasi-bayar/(:num)', 'Kasir\Transaksi::verifikasiPembayaran/$1');
    $routes->post('transaksi/serah-unit/(:num)', 'Kasir\Transaksi::serahkanUnit/$1');
    $routes->post('transaksi/pengembalian/(:num)', 'Kasir\Transaksi::pengembalian/$1');

    // Maintenance
    $routes->get('maintenance', 'Kasir\Maintenance::index');
    $routes->post('maintenance/selesai/(:num)', 'Kasir\Maintenance::selesai/$1');
});

// Owner Routes
$routes->group('owner', ['filter' => ['auth', 'role:owner']], function ($routes) {
    $routes->get('dashboard', 'Owner\Dashboard::index');
    $routes->get('laporan', 'Owner\Laporan::index');
    $routes->get('laporan/cetak', 'Owner\Laporan::cetak');
});
