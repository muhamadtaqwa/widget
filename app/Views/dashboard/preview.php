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
        <h1 style="font-size: 2rem; font-weight: 800;">Preview Widget</h1>
        <p class="subtitle"><?= esc($widget['journal_name']) ?></p>
    </header>

    <div class="glass-card">
        <div class="preview-workspace">
            <iframe src="/widget_loader?id=<?= esc($widget['widget_id']) ?>" style="width: 100%; max-width: 400px; height: 500px; border: none;"></iframe>
        </div>

        <div class="code-container" style="margin-top: 2rem;">
            <div class="code-lang">EMBED CODE</div>
            <div class="code-wrapper">
                <pre><code>&lt;iframe src="<?= base_url('widget_loader?id=' . $widget['widget_id']) ?>" width="100%" height="500" frameborder="0"&gt;&lt;/iframe&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>