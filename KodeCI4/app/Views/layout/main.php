<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sewa Kamera & Drone') ?></title>
    <!-- Google Fonts & Bootstrap 5 Icons & CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #0ea5e9;
            --dark: #0f172a;
            --light-bg: #f8fafc;
            --card-border: rgba(226, 232, 240, 0.8);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
        }
        .nav-link {
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
            background-color: #f1f5f9;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.08);
        }
        .badge-status {
            padding: 0.4em 0.8em;
            font-weight: 600;
            border-radius: 20px;
            font-size: 0.78rem;
            text-transform: capitalize;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: #fff;
        }
        .footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1.2rem 0;
            color: #64748b;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid max-w-7xl">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <span class="p-2 rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-camera-fill"></i>
                </span>
                <span>CameraRent<span class="text-primary">.Pro</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <?php if (session()->get('role') === 'pelanggan'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'pelanggan/katalog' ? 'active' : '' ?>" href="<?= base_url('pelanggan/katalog') ?>">
                                <i class="bi bi-grid me-1"></i> Katalog Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'pelanggan/status-sewa' ? 'active' : '' ?>" href="<?= base_url('pelanggan/status-sewa') ?>">
                                <i class="bi bi-receipt me-1"></i> Status & Invoice
                            </a>
                        </li>
                    <?php elseif (session()->get('role') === 'kasir'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'kasir/dashboard' ? 'active' : '' ?>" href="<?= base_url('kasir/dashboard') ?>">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'kasir/master-data' ? 'active' : '' ?>" href="<?= base_url('kasir/master-data') ?>">
                                <i class="bi bi-box-seam me-1"></i> Master Data
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'kasir/transaksi' ? 'active' : '' ?>" href="<?= base_url('kasir/transaksi') ?>">
                                <i class="bi bi-cash-stack me-1"></i> Transaksi & Sewa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'kasir/maintenance' ? 'active' : '' ?>" href="<?= base_url('kasir/maintenance') ?>">
                                <i class="bi bi-tools me-1"></i> Maintenance
                            </a>
                        </li>
                    <?php elseif (session()->get('role') === 'owner'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'owner/dashboard' ? 'active' : '' ?>" href="<?= base_url('owner/dashboard') ?>">
                                <i class="bi bi-pie-chart me-1"></i> Executive Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() === 'owner/laporan' ? 'active' : '' ?>" href="<?= base_url('owner/laporan') ?>">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan Keuangan
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if (session()->get('logged_in')): ?>
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-bold text-dark fs-6"><?= esc(session()->get('nama_user')) ?></div>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase" style="font-size: 0.7rem;">
                                <?= esc(session()->get('role')) ?>
                            </span>
                        </div>
                        <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Login</a>
                        <a href="<?= base_url('auth/register') ?>" class="btn btn-primary-custom btn-sm rounded-pill px-3">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid py-4 max-w-7xl">
        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">&copy; 2026 Kelompok 9 Capstone Project (4E) - Manajemen Inventory Sistem Sewa Camera.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
