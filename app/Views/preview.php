<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Preview Widget - I-Widget</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="glow-bg"></div>
    <div class="container" style="max-width: 900px; margin: 2rem auto; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <a href="<?= base_url() ?>" class="copy-btn" style="text-decoration: none;">
                <i data-lucide="arrow-left"></i>
                <span>Kembali ke Generator</span>
            </a>
            <button onclick="window.close()" class="copy-btn" type="button">
                <i data-lucide="x"></i>
                <span>Tutup Tab</span>
            </button>
        </div>

        <div class="glass-card">
            <div class="card-header">
                <div class="header-title">
                    <div class="header-icon-box">
                        <i data-lucide="eye"></i>
                    </div>
                    <div>
                        <h1 style="font-size: 1.35rem; font-weight: 800; margin: 0;">Full Preview Widget</h1>
                        <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0;">Tampilan utuh widget yang disematkan</p>
                    </div>
                </div>
                <div class="badge-status">
                    <span class="dot"></span>
                    <span>Pratinjau Mandiri</span>
                </div>
            </div>

            <div class="preview-workspace" id="previewArea" style="min-height: 580px; padding: 1.5rem;">
            </div>

            <div class="preview-info" style="margin-top: 1rem;">
                <i data-lucide="info"></i>
                <p>Widget ini akan merespons lebar kontainer blok sidebar di OJS Anda secara otomatis.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const html = localStorage.getItem('pnp_widget_preview');
            const container = document.getElementById('previewArea');
            if (html && container) {
                container.innerHTML = html;
            } else if (container) {
                container.innerHTML = '<p style="color: var(--text-muted); text-align: center;">Tidak ada data preview yang tersimpan. Silakan kembali ke halaman generator dan perbarui widget.</p>';
            }
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>

</html>