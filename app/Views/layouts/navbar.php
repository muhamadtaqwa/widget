<?php
$userRole = session()->get('role') ?? 'user';
$userEmail = session()->get('email') ?? '';
$lang = $_GET['lang'] ?? 'en';
?>
<nav class="navbar">
    <div class="nav-inner">
        <a href="<?= base_url() ?>" class="nav-brand">
            <div class="nav-brand-icon">
                <i data-lucide="blocks"></i>
            </div>
            <div class="nav-brand-text">
                <span class="brand-title">I-Widget</span>
                <span class="brand-badge">OJS</span>
            </div>
        </a>

        <button type="button" class="nav-toggle" id="navToggle" aria-label="Buka Menu" aria-expanded="false">
            <i data-lucide="menu" id="navToggleIcon"></i>
        </button>

        <div class="nav-menu" id="navMenu">
            <div class="nav-menu-group">
                <div class="lang-switcher">
                    <a href="?lang=en" class="lang-link <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
                    <span class="divider"></span>
                    <a href="?lang=id" class="lang-link <?= $lang === 'id' ? 'active' : '' ?>">ID</a>
                </div>
            </div>

            <div class="nav-actions">
                <?php if (session()->get('user_id')): ?>
                    <?php if ($userRole === 'admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="nav-link">
                            <i data-lucide="shield"></i>
                            <span>Admin</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('dashboard') ?>" class="nav-link">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                    <div class="nav-user-chip">
                        <i data-lucide="user"></i>
                        <span class="nav-email"><?= esc($userEmail) ?></span>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="nav-link nav-link-logout">
                        <i data-lucide="log-out"></i>
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="nav-link">
                        <i data-lucide="log-in"></i>
                        <span>Login</span>
                    </a>
                    <a href="<?= base_url('register') ?>" class="btn-nav-primary">
                        <i data-lucide="user-plus"></i>
                        <span>Daftar</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    (function() {
        const toggle = document.getElementById('navToggle');
        const menu = document.getElementById('navMenu');
        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = menu.classList.toggle('active');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                const icon = toggle.querySelector('i');
                if (icon) {
                    icon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
                    if (window.lucide) lucide.createIcons();
                }
            });

            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && !toggle.contains(e.target) && menu.classList.contains('active')) {
                    menu.classList.remove('active');
                    toggle.setAttribute('aria-expanded', 'false');
                    const icon = toggle.querySelector('i');
                    if (icon) {
                        icon.setAttribute('data-lucide', 'menu');
                        if (window.lucide) lucide.createIcons();
                    }
                }
            });
        }
    })();
</script>