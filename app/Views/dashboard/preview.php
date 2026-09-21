<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar') ?>

<div class="container" style="margin-top: 2rem; margin-bottom: 4rem; max-width: 900px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <a href="<?= base_url('dashboard') ?>" class="copy-btn" style="text-decoration: none;">
            <i data-lucide="arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
        <a href="<?= base_url('journal/edit/' . $widget['id'] . '/builder') ?>" class="btn btn-primary" style="padding: 0.55rem 1.25rem; font-size: 0.88rem;">
            <i data-lucide="edit-3"></i>
            <span>Buka di Builder</span>
        </a>
    </div>

    <div class="glass-card">
        <div class="card-header">
            <div class="header-title">
                <div class="header-icon-box">
                    <i data-lucide="eye"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.4rem;">Pratinjau Widget</h2>
                    <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);"><?= esc($widget['journal_name']) ?></p>
                </div>
            </div>
            <span class="badge-status">
                <span class="dot"></span>
                <span>Live Loader</span>
            </span>
        </div>

        <div class="preview-workspace">
            <iframe src="<?= base_url('widget_loader?id=' . esc($widget['widget_id'])) ?>" style="width: 100%; max-width: 440px; height: 520px; border: none; border-radius: var(--radius-md);" title="Widget Preview"></iframe>
        </div>

        <div class="card-header" style="margin-top: 2rem; margin-bottom: 1rem;">
            <div class="header-title">
                <div class="header-icon-box">
                    <i data-lucide="code-2"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Kode Sematan (Embed Code)</h3>
            </div>
            <button class="copy-btn" id="copySnippetBtn" type="button" onclick="copyEmbedSnippet()">
                <i data-lucide="copy"></i>
                <span id="copyBtnText">Salin Kode</span>
            </button>
        </div>

        <div class="code-container">
            <div class="code-lang">
                <span>HTML IFRAME</span>
                <span style="font-size: 0.7rem; opacity: 0.8;">OJS Custom Block</span>
            </div>
            <div class="code-wrapper">
                <pre><code id="embedCodeSnippet">&lt;iframe src="<?= base_url('widget_loader?id=' . esc($widget['widget_id'])) ?>" width="100%" height="520" frameborder="0" scrolling="no"&gt;&lt;/iframe&gt;</code></pre>
            </div>
        </div>

        <div class="usage-tip">
            <p><strong>Cara Pakai:</strong> Salin kode sematan di atas dan tempelkan ke <em>Custom Block Manager</em> pada sidebar OJS jurnal Anda.</p>
        </div>
    </div>
</div>

<script>
function copyEmbedSnippet() {
    const code = document.getElementById('embedCodeSnippet').innerText;
    navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyBtnText');
        const original = btnText.innerText;
        btnText.innerText = 'Tersalin!';
        setTimeout(() => {
            btnText.innerText = original;
        }, 2000);
    });
}
</script>
<?= $this->endSection() ?>