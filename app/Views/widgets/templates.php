<?php
error_reporting(0);
ini_set('display_errors', 0);


$journalName = trim($_GET['name'] ?? '');
$warna = $_GET['warna'] ?? '4f46e5';
$warna = preg_replace('/[^a-fA-F0-9]/', '', $warna);
if (strlen($warna) < 6) $warna = '4f46e5';

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #<?php echo $warna; ?>;
            --primary-rgb: <?php echo "$r, $g, $b"; ?>;
            --primary-light: rgba(var(--primary-rgb), 0.12);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-card: #ffffff;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: transparent;
            color: var(--text-main);
            overflow: hidden;
        }
        .widget-container {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            margin: 10px auto;
            position: relative;
            overflow: hidden;
        }
        .widget-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--primary), rgba(var(--primary-rgb), 0.5));
        }
        .widget-header {
            padding: 14px 16px 10px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        .widget-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .journal-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .template-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            background: #fff;
        }
        .template-btn {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.2s;
            text-decoration: none;
            gap: 12px;
        }
        .template-btn:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.1);
        }
        .template-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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
        }
        .template-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .template-sub {
            font-size: 11px;
            color: var(--text-muted);
        }
        .widget-footer {
            padding: 8px 12px;
            font-size: 10px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
        }
        .widget-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        
        .empty-state {
            padding: 30px 16px;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
            font-style: italic;
        }

        @media (max-width: 380px) {
            .template-btn {
                padding: 10px;
                gap: 8px;
            }
            .template-icon {
                width: 24px;
                height: 24px;
            }
            .template-label {
                font-size: 12px;
            }
            .template-sub {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="widget-container">
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

    </div>
</body>
</html>
