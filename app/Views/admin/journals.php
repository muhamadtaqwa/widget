<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar') ?>

<div class="container" style="margin-top: 1.5rem; margin-bottom: 4rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.25rem;">Kelola Seluruh Jurnal</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Daftar seluruh widget yang dibuat oleh semua pengguna sistem.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= base_url('admin') ?>" class="btn btn-outline" style="padding: 0.65rem 1.25rem; font-size: 0.88rem;">
                <i data-lucide="users"></i>
                <span>Kelola User</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: var(--success-soft); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.9rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>

    <div class="glass-card">
        <div class="card-header">
            <div class="header-title">
                <div class="header-icon-box">
                    <i data-lucide="book-open"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.35rem;">Daftar Widget Jurnal</h2>
                    <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);">Total: <?= count($widgets) ?> widget dibuat</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>User ID</th>
                        <th>Nama Jurnal</th>
                        <th>Widget ID</th>
                        <th>Expired</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($widgets)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Belum ada data widget jurnal.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($widgets as $w): ?>
                            <tr>
                                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 600; color: var(--primary);"><?= $w['id'] ?></td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">User #<?= $w['user_id'] ?></td>
                                <td style="font-weight: 700; color: var(--text-main);"><?= esc($w['journal_name']) ?></td>
                                <td>
                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; background: var(--tosca-50); padding: 0.2rem 0.55rem; border-radius: var(--radius-xs); border: 1px solid var(--glass-border-tosca); color: var(--primary-dark);">
                                        <?= esc($w['widget_id']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: <?= !empty($w['expired_at']) && $w['expired_at'] < date('Y-m-d') ? 'var(--danger)' : 'var(--text-muted)' ?>;">
                                        <?= esc($w['expired_at'] ?? 'Aktif') ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                        <a href="<?= base_url('journal/preview/' . $w['id']) ?>" class="copy-btn" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                            <i data-lucide="eye"></i> Preview
                                        </a>
                                        <a href="<?= base_url('admin/delete-journal/' . $w['id']) ?>" class="remove-row-btn" style="width: 30px; height: 30px;" title="Hapus Widget" onclick="return confirm('Hapus widget ini?')">
                                            <i data-lucide="trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>