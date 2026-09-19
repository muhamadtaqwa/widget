<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container" style="max-width: 480px; margin-top: 5rem;">
    <div class="glass-card">
        <div class="card-header">
            <div class="header-title">
                <i data-lucide="user-plus"></i>
                <h2>Daftar</h2>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div style="background: var(--danger-soft); color: var(--danger); padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Password (min. 6 karakter)</label>
                <input type="password" name="password" minlength="6" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i data-lucide="user-plus"></i> Daftar
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
            Sudah punya akun? <a href="/login" style="color: var(--primary);">Login</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>