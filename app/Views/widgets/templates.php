<?php
error_reporting(0);
ini_set('display_errors', 0);


$journalName = trim($_GET['name'] ?? '');
$warna = $_GET['warna'] ?? '0d9488';
$warna = preg_replace('/[^a-fA-F0-9]/', '', $warna);
if (strlen($warna) < 6) $warna = '0d9488';

[$r, $g, $b] = sscanf($warna, "%02x%02x%02x");

// Decode dynamic template data
$templates = [];
if (!empty($_GET['data'])) {
    $decoded = json_decode(urldecode(base64_decode($_GET['data'])), true);
    if (is_array($decoded)) {
        $templates = $decoded;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Template Widget</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #<?php echo $warna; ?>;
            --primary-rgb: <?php echo "$r, $g, $b"; ?>;
            --primary-light: rgba(var(--primary-rgb), 0.12);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-card: #ffffff;
            --border: rgba(226, 232, 240, 0.85);
            --border-glass: rgba(13, 148, 136, 0.16);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: transparent;
            color: var(--text-main);
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }
        .widget-container {
            background: var(--bg-card);
            border-radius: 14px;
            border: 1.5px solid var(--border-glass);
            box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.08), 0 2px 6px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .widget-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3.5px;
            background: linear-gradient(90deg, #0d9488 0%, #2dd4bf 50%, #0d9488 100%);
            z-index: 2;
        }
        .widget-header {
            padding: 12px 14px 8px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        .widget-title {
            font-size: 10.5px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .journal-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .template-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 14px;
            background: #fff;
        }
        .template-btn {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            gap: 12px;
        }
        .template-btn:hover {
            border-color: #0d9488;
            background: linear-gradient(180deg, #ffffff 0%, #f0fdfa 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.12);
        }
        .template-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #f0fdfa;
            border: 1px solid rgba(13, 148, 136, 0.2);
            border-radius: 9px;
            padding: 4px;
        }
        .template-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .template-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            gap: 2px;
        }
        .template-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .template-sub {
            font-size: 10px;
            color: var(--text-muted);
        }
        .widget-footer {
            padding: 9px 12px;
            font-size: 10px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .widget-footer a { color: #0d9488; text-decoration: none; font-weight: 600; }
        .widget-footer a:hover { color: #0f766e; text-decoration: underline; }
        .f-dot { width: 4px; height: 4px; border-radius: 50%; background: #0d9488; display: inline-block; }
        
        .empty-state {
            padding: 24px 14px;
            text-align: center;
            color: var(--text-muted);
            font-size: 11px;
            font-style: italic;
        }

        @media (max-width: 380px) {
            .template-btn {
                padding: 9px 10px;
                gap: 8px;
            }
            .template-icon {
                width: 28px;
                height: 28px;
            }
            .template-label {
                font-size: 11.5px;
            }
            .template-sub {
                font-size: 9.5px;
            }
        }
    </style>
</head>
<body>
    <div class="widget-container">
        <?php if (!empty($journalName)): ?>
        <div class="widget-header">
            <div class="widget-title">Template Download</div>
            <div class="journal-title"><?php echo htmlspecialchars($journalName); ?></div>
        </div>
        <?php endif; ?>
        <div class="template-list">
            <?php foreach ($templates as $t): 
                $icon = ($t['type'] === 'pdf') ? 'pdf.png' : 'doc.png';
                $sub = ($t['type'] === 'pdf') ? 'Adobe PDF (.pdf)' : 'Microsoft Word (.doc / .docx)';
            ?>
            <a href="<?php echo htmlspecialchars($t['url']); ?>" target="_blank" class="template-btn">
                <div class="template-icon">
                    <img src="<?= rtrim(base_url(), '/') ?>/media/img/download/<?php echo $icon; ?>" alt="<?php echo strtoupper($t['type']); ?> Icon">
                </div>
                <div class="template-info">
                    <span class="template-label"><?php echo htmlspecialchars($t['label']); ?></span>
                    <span class="template-sub"><?php echo $sub; ?></span>
                </div>
            </a>
            <?php endforeach; ?>

            <?php if (empty($templates)): ?>
                <div class="empty-state">No template links provided.</div>
            <?php endif; ?>
        </div>

        <?php if (!isset($_GET['wl']) || $_GET['wl'] != '1'): ?>
        <div class="widget-footer">
            <span class="f-dot"></span>
            <span>Live Data via <a href="<?= rtrim(base_url(), '/') ?>" target="_blank">I-Widget</a> &bull; <?php echo date('Y'); ?></span>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
