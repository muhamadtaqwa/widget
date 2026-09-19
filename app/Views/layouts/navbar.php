<?php
$userRole = session()->get('role') ?? 'user';
$userEmail = session()->get('email') ?? '';
$lang = $_GET['lang'] ?? 'en';
?>
<nav class="navbar">
    <div class="nav-inner">
        <a href="<?= base_url() ?>" class="nav-brand">
            <i data-lucide="blocks" style="width: 32px; height: 32px; color: var(--primary);"></i>
            <span>I-Widget</span>
        </a>

        <div class="nav-actions">
            <div class="lang-switcher">
                <a href="?lang=en" class="lang-link <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
                <span class="divider"></span>
                <a href="?lang=id" class="lang-link <?= $lang === 'id' ? 'active' : '' ?>">ID</a>
            </div>

            <?php if (session()->get('user_id')): ?>
                <?php if ($userRole === 'admin'): ?>
                    <a href="<?= base_url('admin') ?>" class="nav-link">
                        <i data-lucide="shield"></i> Admin
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('dashboard') ?>" class="nav-link">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>
                <span class="nav-email"><?= esc($userEmail) ?></span>
                <a href="<?= base_url('logout') ?>" class="nav-link nav-link-logout">
                    <i data-lucide="log-out"></i> Logout
                </a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="nav-link">
                    <i data-lucide="log-in"></i> Login
                </a>
                <a href="<?= base_url('register') ?>" class="btn-nav-primary">
                    Daftar
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>