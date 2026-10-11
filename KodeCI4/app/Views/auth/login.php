<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card card-custom p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark">Selamat Datang</h4>
                <p class="text-muted small">Masuk untuk mengakses sistem penyewaan kamera</p>
            </div>

            <form action="<?= base_url('auth/login') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Username Anda" value="<?= old('username') ?>" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 py-2">
                    Masuk Sekarang <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center">
                <p class="small text-muted mb-2">Belum memiliki akun pelanggan?</p>
                <a href="<?= base_url('auth/register') ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-3">
                    Daftar Akun Baru
                </a>
            </div>

            <div class="mt-4 p-3 rounded-3 bg-light border text-start">
                <div class="small fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1"></i> Akun Demo:</div>
                <div class="small text-muted">
                    <div>• <b>Owner:</b> <code>owner</code> / <code>owner123</code></div>
                    <div>• <b>Kasir:</b> <code>kasir</code> / <code>kasir123</code></div>
                    <div>• <b>Pelanggan:</b> <code>nazwa</code> / <code>nazwa123</code></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
