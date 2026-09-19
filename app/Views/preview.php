<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget Preview - I-Widget</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="glow-bg"></div>
    <div class="container" style="max-width: 800px; margin: 3rem auto;">
        <header style="margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; font-weight: 800;">Widget Preview</h1>
            <p class="subtitle">Preview penuh widget Anda.</p>
        </header>

        <div class="glass-card">
            <div class="preview-workspace" id="previewArea" style="min-height: 600px;">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const html = localStorage.getItem('pnp_widget_preview');
            if (html) {
                document.getElementById('previewArea').innerHTML = html;
            } else {
                document.getElementById('previewArea').innerHTML = '<p style="color: var(--text-muted);">Tidak ada preview.</p>';
            }
            lucide.createIcons();
        });
    </script>
</body>

</html>