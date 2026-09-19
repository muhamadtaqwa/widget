<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<nav class="navbar" style="background: var(--card-bg); border-bottom: 1px solid var(--glass-border); padding: 1rem 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1300px; margin: 0 auto;">
        <a href="/" style="font-weight: 700; color: var(--primary); text-decoration: none;">I-Widget</a>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="/dashboard" style="color: var(--primary); text-decoration: none;">Dashboard</a>
            <a href="/logout" style="color: var(--danger); text-decoration: none;">Logout</a>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 3rem;">
    <header>
        <h1 style="font-size: 2rem; font-weight: 800;">Edit Widget</h1>
        <p class="subtitle"><?= esc($widget['journal_name'] ?? '') ?></p>
    </header>

    <div class="glass-card">
        <div class="form-grid">
            <div class="form-group full-width">
                <label>Widget ID</label>
                <input type="text" value="<?= esc($widget['widget_id'] ?? '') ?>" readonly>
            </div>

            <div class="form-group full-width">
                <label>Journal Name</label>
                <input type="text" value="<?= esc($widget['journal_name'] ?? '') ?>" readonly>
            </div>

            <div class="form-group">
                <label>Status</label>
                <input type="text" value="<?= $widget['expired_at'] ? 'Expired' : 'Active' ?>" readonly>
            </div>

            <div class="form-group">
                <label>Expired At</label>
                <input type="text" value="<?= esc($widget['expired_at'] ?? '-') ?>" readonly>
            </div>
        </div>

        <div class="card-footer">
            <a href="/journal/edit/<?= $widget['id'] ?>/builder" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 0.75rem 1.5rem;">
                <i data-lucide="edit"></i> Edit di Builder
            </a>
            <a href="/journal/preview/<?= $widget['id'] ?>" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 0.75rem 1.5rem; background: rgba(13,148,136,0.1); color: var(--primary); box-shadow: none;">
                <i data-lucide="eye"></i> Preview
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>