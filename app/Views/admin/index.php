<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<nav class="navbar" style="background: var(--card-bg); border-bottom: 1px solid var(--glass-border); padding: 1rem 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1300px; margin: 0 auto;">
        <a href="/" style="font-weight: 700; color: var(--primary); text-decoration: none;">I-Widget</a>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="/dashboard" style="color: var(--primary); text-decoration: none;">Dashboard</a>
            <a href="/admin/journals" style="color: var(--primary); text-decoration: none;">Jurnal</a>
            <span style="color: var(--text-muted); font-size: 0.9rem;"><?= esc(session()->get('email')) ?></span>
            <a href="/logout" style="color: var(--danger); text-decoration: none;">Logout</a>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 3rem;">
    <header>
        <h1 style="font-size: 2rem; font-weight: 800;">Admin Panel</h1>
        <p class="subtitle">Kelola user dan widget.</p>
    </header>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: var(--success-soft); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="glass-card">
        <div class="card-header">
            <div class="header-title">
                <i data-lucide="users"></i>
                <h2>Users</h2>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border); text-align: left;">
                        <th style="padding: 0.75rem;">ID</th>
                        <th style="padding: 0.75rem;">Email</th>
                        <th style="padding: 0.75rem;">Role</th>
                        <th style="padding: 0.75rem;">Status</th>
                        <th style="padding: 0.75rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 0.75rem;"><?= $u['id'] ?></td>
                            <td style="padding: 0.75rem;"><?= esc($u['email']) ?></td>
                            <td style="padding: 0.75rem;"><?= esc($u['role']) ?></td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem;
                                    background: <?= $u['status'] === 'active' ? 'var(--success-soft)' : ($u['status'] === 'banned' ? 'var(--danger-soft)' : 'var(--accent-soft)') ?>;
                                    color: <?= $u['status'] === 'active' ? 'var(--success)' : ($u['status'] === 'banned' ? 'var(--danger)' : 'var(--accent-dark)') ?>;">
                                    <?= esc($u['status']) ?>
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($u['status'] === 'pending'): ?>
                                    <a href="/admin/approve/<?= $u['id'] ?>" style="color: var(--success);">Approve</a>
                                <?php endif; ?>
                                <?php if ($u['status'] !== 'banned'): ?>
                                    <a href="/admin/ban/<?= $u['id'] ?>" style="color: var(--danger); margin-left: 0.5rem;">Ban</a>
                                <?php endif; ?>
                                <a href="/admin/delete/<?= $u['id'] ?>" style="color: var(--danger); margin-left: 0.5rem;" onclick="return confirm('Hapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>