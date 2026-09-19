<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<nav class="navbar" style="background: var(--card-bg); border-bottom: 1px solid var(--glass-border); padding: 1rem 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1300px; margin: 0 auto;">
        <a href="/" style="font-weight: 700; color: var(--primary); text-decoration: none;">I-Widget</a>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <?php if (session()->get('role') === 'admin'): ?>
                <a href="/admin" style="color: var(--primary); text-decoration: none;">Admin</a>
            <?php endif; ?>
            <span style="color: var(--text-muted); font-size: 0.9rem;"><?= esc(session()->get('email')) ?></span>
            <a href="/logout" style="color: var(--danger); text-decoration: none;">Logout</a>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 3rem;">
    <header>
        <h1 style="font-size: 2rem; font-weight: 800;">Dashboard</h1>
        <p class="subtitle">Kelola widget jurnal Anda.</p>
    </header>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: var(--success-soft); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 1.5rem;">
        <a href="/journal/edit" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 0.75rem 1.5rem;">
            <i data-lucide="plus"></i> Buat Widget Baru
        </a>
    </div>

    <?php if (empty($widgets)): ?>
        <div class="glass-card" style="text-align: center;">
            <h3 style="margin-bottom: 0.5rem;">Belum ada widget</h3>
            <p style="color: var(--text-muted);">Klik "Buat Widget Baru" untuk memulai.</p>
        </div>
    <?php else: ?>
        <div class="form-grid">
            <?php foreach ($widgets as $w): ?>
                <div class="glass-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($w['widget_id']) ?></span>
                        <?php if ($w['is_whitelabel']): ?>
                            <span style="background: var(--accent-soft); color: var(--accent-dark); padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.6rem; font-weight: 700;">WHITELABEL</span>
                        <?php endif; ?>
                    </div>
                    <h3 style="margin-top: 1rem;"><?= esc($w['journal_name']) ?></h3>
                    <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; align-items: center;">
                        <a href="/journal/edit/<?= $w['id'] ?>" style="color: var(--primary); font-size: 0.85rem;">Edit</a>
                        <a href="/journal/preview/<?= $w['id'] ?>" style="color: var(--primary); font-size: 0.85rem;">Preview</a>
                        <a href="/journal/delete/<?= $w['id'] ?>" style="color: var(--danger); font-size: 0.85rem; margin-left: auto;" onclick="return confirm('Hapus widget ini?')">Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>