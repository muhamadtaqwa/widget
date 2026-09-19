<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar') ?>

<div class="container" style="margin-top: 1.5rem; margin-bottom: 4rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.25rem;">Panel Admin</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Kelola pengguna terdaftar dan hak akses sistem.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= base_url('admin/journals') ?>" class="btn btn-primary" style="padding: 0.65rem 1.25rem; font-size: 0.88rem;">
                <i data-lucide="book-open"></i>
                <span>Kelola Jurnal</span>
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
                    <i data-lucide="users"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.35rem;">Daftar Pengguna</h2>
                    <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);">Total: <?= count($users) ?> user terdaftar</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Email Pengguna</th>
                        <th>Peran (Role)</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 600; color: var(--primary);"><?= $u['id'] ?></td>
                                <td style="font-weight: 600; color: var(--text-main);"><?= esc($u['email']) ?></td>
                                <td>
                                    <span style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; background: var(--bg-subtle); padding: 0.2rem 0.6rem; border-radius: var(--radius-xs); border: 1px solid var(--glass-border);">
                                        <?= esc($u['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill <?= $u['status'] === 'active' ? 'active' : ($u['status'] === 'banned' ? 'banned' : 'pending') ?>">
                                        <?= esc($u['status']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                        <?php if ($u['status'] === 'pending'): ?>
                                            <a href="<?= base_url('admin/approve/' . $u['id']) ?>" class="copy-btn" style="color: var(--success); border-color: rgba(16,185,129,0.3); padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                                <i data-lucide="check"></i> Approve
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($u['status'] !== 'banned'): ?>
                                            <a href="<?= base_url('admin/ban/' . $u['id']) ?>" class="copy-btn" style="color: var(--warning); border-color: rgba(245,158,11,0.3); padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                                <i data-lucide="shield-alert"></i> Ban
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/delete/' . $u['id']) ?>" class="remove-row-btn" style="width: 30px; height: 30px;" title="Hapus User" onclick="return confirm('Hapus user ini?')">
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