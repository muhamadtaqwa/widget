<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar') ?>

<div class="container" style="margin-top: 1.5rem; margin-bottom: 4rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.25rem;">Dashboard Jurnal</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Kelola dan pantau seluruh widget statistik jurnal Anda.</p>
        </div>
        <div>
            <a href="<?= base_url() ?>" class="btn btn-primary">
                <i data-lucide="plus"></i>
                <span>Buat Widget Baru</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: var(--success-soft); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.9rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background: var(--danger-soft); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.25); padding: 0.9rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
            <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($widgets)): ?>
        <div class="glass-card" style="text-align: center; padding: 4rem 2rem;">
            <div style="width: 64px; height: 64px; border-radius: var(--radius-lg); background: var(--tosca-50); border: 1.5px solid var(--glass-border-tosca); display: inline-flex; align-items: center; justify-content: center; color: var(--primary); margin-bottom: 1.25rem;">
                <i data-lucide="layout-grid" style="width: 32px; height: 32px;"></i>
            </div>
            <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 0.5rem;">Belum Ada Widget</h3>
            <p style="color: var(--text-muted); max-width: 440px; margin: 0 auto 1.5rem; font-size: 0.92rem;">
                Anda belum membuat widget jurnal. Klik tombol di bawah untuk mulai membuat widget statistik pertama Anda.
            </p>
            <a href="<?= base_url() ?>" class="btn btn-primary" style="display: inline-flex;">
                <i data-lucide="plus"></i>
                <span>Buat Widget Sekarang</span>
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            <?php foreach ($widgets as $w): ?>
                <div class="glass-card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; font-weight: 600; color: var(--primary-dark); background: var(--tosca-50); padding: 0.25rem 0.65rem; border-radius: var(--radius-xs); border: 1px solid var(--glass-border-tosca);">
                                <?= esc($w['widget_id']) ?>
                            </span>
                            <?php if (!empty($w['is_whitelabel'])): ?>
                                <span class="status-pill whitelabel">
                                    <i data-lucide="award" style="width: 12px; height: 12px;"></i>
                                    <span>Whitelabel</span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; line-height: 1.4;">
                            <?= esc($w['journal_name'] ?: 'Untitled Journal') ?>
                        </h3>
                    </div>

                    <div style="display: flex; gap: 0.6rem; margin-top: 1.5rem; border-top: 1px solid var(--glass-border); padding-top: 1.25rem; align-items: center; flex-wrap: wrap;">
                        <a href="<?= base_url('journal/edit/' . $w['id'] . '/builder') ?>" class="copy-btn" style="padding: 0.45rem 0.85rem; font-size: 0.82rem;">
                            <i data-lucide="edit-3"></i>
                            <span>Edit Builder</span>
                        </a>
                        <a href="<?= base_url('journal/preview/' . $w['id']) ?>" class="copy-btn" style="padding: 0.45rem 0.85rem; font-size: 0.82rem;">
                            <i data-lucide="eye"></i>
                            <span>Preview</span>
                        </a>
                        <a href="<?= base_url('journal/delete/' . $w['id']) ?>" class="remove-row-btn" style="margin-left: auto; width: 34px; height: 34px;" title="Hapus widget" onclick="return confirm('Apakah Anda yakin ingin menghapus widget ini?')">
                            <i data-lucide="trash-2"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>