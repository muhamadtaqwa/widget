<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<nav class="navbar" style="background: var(--card-bg); border-bottom: 1px solid var(--glass-border); padding: 1rem 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1300px; margin: 0 auto;">
        <a href="/" style="font-weight: 700; color: var(--primary); text-decoration: none;">I-Widget</a>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="/admin" style="color: var(--primary); text-decoration: none;">Admin</a>
            <a href="/dashboard" style="color: var(--primary); text-decoration: none;">Dashboard</a>
            <a href="/logout" style="color: var(--danger); text-decoration: none;">Logout</a>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 3rem;">
    <header>
        <h1 style="font-size: 2rem; font-weight: 800;">Kelola Jurnal</h1>
        <p class="subtitle">Semua widget dari semua user.</p>
    </header>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: var(--success-soft); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="glass-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border); text-align: left;">
                        <th style="padding: 0.75rem;">ID</th>
                        <th style="padding: 0.75rem;">User ID</th>
                        <th style="padding: 0.75rem;">Journal Name</th>
                        <th style="padding: 0.75rem;">Widget ID</th>
                        <th style="padding: 0.75rem;">Expired</th>
                        <th style="padding: 0.75rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($widgets as $w): ?>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 0.75rem;"><?= $w['id'] ?></td>
                            <td style="padding: 0.75rem;"><?= $w['user_id'] ?></td>
                            <td style="padding: 0.75rem;"><?= esc($w['journal_name']) ?></td>
                            <td style="padding: 0.75rem;"><?= esc($w['widget_id']) ?></td>
                            <td style="padding: 0.75rem;"><?= esc($w['expired_at'] ?? '-') ?></td>
                            <td style="padding: 0.75rem;">
                                <a href="/journal/preview/<?= $w['id'] ?>" style="color: var(--primary); font-size: 0.85rem;">Preview</a>
                                <a href="/admin/delete-journal/<?= $w['id'] ?>" style="color: var(--danger); font-size: 0.85rem; margin-left: 0.75rem;" onclick="return confirm('Hapus widget ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>