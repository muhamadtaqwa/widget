<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container" style="max-width: 480px; margin-top: 3rem; margin-bottom: 4rem;">
    <div style="margin-bottom: 1.25rem;">
        <a href="<?= base_url() ?>" class="copy-btn" style="text-decoration: none;">
            <i data-lucide="arrow-left"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <div class="glass-card">
        <div class="card-header" style="margin-bottom: 1.75rem;">
            <div class="header-title">
                <div class="header-icon-box">
                    <i data-lucide="user-plus"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.4rem;">Daftar Akun</h2>
                    <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);">Mulai buat widget statistik jurnal Anda</p>
                </div>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div style="background: var(--danger-soft); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.85rem 1.15rem; border-radius: var(--radius-sm); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.6rem; font-size: 0.88rem;">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('register') ?>">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@jurnal.ac.id" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password">Password (minimal 6 karakter)</label>
                <input type="password" id="password" name="password" placeholder="Buat password yang kuat" minlength="6" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i data-lucide="user-plus"></i>
                <span>Daftar Sekarang</span>
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.75rem; color: var(--text-muted); font-size: 0.88rem; border-top: 1px solid var(--glass-border); padding-top: 1.25rem;">
            Sudah punya akun? <a href="<?= base_url('login') ?>" style="color: var(--primary); font-weight: 700;">Masuk di sini</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>