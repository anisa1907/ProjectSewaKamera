<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card card-custom p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                    <i class="bi bi-person-plus-fill fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark">Registrasi Pelanggan</h4>
                <p class="text-muted small">Buat akun untuk mulai menyewa kamera & lensa</p>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger py-2 small rounded-3 mb-3">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/register') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama_user" class="form-control bg-light" placeholder="Nama sesuai identitas" value="<?= old('nama_user') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control bg-light" placeholder="Username unik" value="<?= old('username') ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control bg-light" placeholder="Minimal 6 karakter" required>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 py-2">
                    Daftar Sekarang <i class="bi bi-check-lg ms-1"></i>
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center">
                <p class="small text-muted mb-0">Sudah memiliki akun? <a href="<?= base_url('auth/login') ?>" class="fw-semibold text-primary">Login di sini</a></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
