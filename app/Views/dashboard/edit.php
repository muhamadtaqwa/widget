<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar') ?>

<div class="container" style="margin-top: 2rem; margin-bottom: 4rem; max-width: 800px;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= base_url('dashboard') ?>" class="copy-btn" style="text-decoration: none;">
            <i data-lucide="arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <div class="glass-card">
        <div class="card-header">
            <div class="header-title">
                <div class="header-icon-box">
                    <i data-lucide="info"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.4rem;">Informasi Widget</h2>
                    <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);"><?= esc($widget['journal_name'] ?? 'Detail Widget') ?></p>
                </div>
            </div>
            <span class="badge-status">
                <span class="dot"></span>
                <span><?= !empty($widget['expired_at']) && $widget['expired_at'] < date('Y-m-d') ? 'Expired' : 'Aktif' ?></span>
            </span>
        </div>

        <div class="form-grid">
            <div class="form-group full-width">
                <label>Widget ID</label>
                <input type="text" value="<?= esc($widget['widget_id'] ?? '') ?>" readonly style="background: var(--bg-subtle); font-family: 'JetBrains Mono', monospace;">
            </div>

            <div class="form-group full-width">
                <label>Nama Jurnal</label>
                <input type="text" value="<?= esc($widget['journal_name'] ?? '') ?>" readonly style="background: var(--bg-subtle);">
            </div>

            <div class="form-group">
                <label>Status Widget</label>
                <input type="text" value="<?= !empty($widget['expired_at']) && $widget['expired_at'] < date('Y-m-d') ? 'Expired' : 'Aktif' ?>" readonly style="background: var(--bg-subtle);">
            </div>

            <div class="form-group">
                <label>Kedaluwarsa Pada</label>
                <input type="text" value="<?= esc($widget['expired_at'] ?? 'Aktif Selamanya') ?>" readonly style="background: var(--bg-subtle);">
            </div>
        </div>

        <div class="card-footer">
            <a href="<?= base_url('journal/edit/' . ($widget['id'] ?? '') . '/builder') ?>" class="btn btn-primary" style="flex: 1;">
                <i data-lucide="edit-3"></i>
                <span>Edit di Live Builder</span>
            </a>
            <a href="<?= base_url('journal/preview/' . ($widget['id'] ?? '')) ?>" class="btn btn-outline" style="flex: 1;">
                <i data-lucide="eye"></i>
                <span>Lihat Preview</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>