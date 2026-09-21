<?php
error_reporting(0);
ini_set('display_errors', 0);


// Tools URLs
$urls = [
    'turnitin'  => $_GET['turnitin'] ?? '',
    'mendeley'  => $_GET['mendeley'] ?? '',
    'grammarly' => $_GET['grammarly'] ?? '',
    'quillbot'  => $_GET['quillbot'] ?? '',
];

$warna = $_GET['warna'] ?? '0d9488';
$warna = preg_replace('/[^a-fA-F0-9]/', '', $warna);
if (strlen($warna) < 6) $warna = '0d9488';

[$r, $g, $b] = sscanf($warna, "%02x%02x%02x");

$journalName = trim($_GET['name'] ?? '');

$items = [
    ['key' => 'turnitin',  'label' => 'Turnitin',  'img' => 'turnitin.png'],
    ['key' => 'mendeley',  'label' => 'Mendeley',  'img' => 'mendeley.png'],
    ['key' => 'grammarly', 'label' => 'Grammarly', 'img' => 'Grammarly.png'],
    ['key' => 'quillbot',  'label' => 'Quillbot',  'img' => 'Quillbot.png'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools Widget</title>
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
            border: none;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .widget-container::before {
            display: none;
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
        .logo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 14px;
            background: #fff;
        }
        .logo-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            height: 56px;
        }
        .logo-item:hover {
            border-color: #0d9488;
            background: linear-gradient(180deg, #ffffff 0%, #f0fdfa 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.12);
        }
        .logo-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: filter 0.2s, transform 0.2s;
        }
        .logo-item:hover img {
            transform: scale(1.04);
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
        
        .empty-grid {
            padding: 24px 14px;
            text-align: center;
            color: var(--text-muted);
            font-size: 11px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="widget-container">
        <?php if (!empty($journalName)): ?>
        <div class="widget-header">
            <div class="widget-title">Journal Tools</div>
            <div class="journal-title"><?php echo htmlspecialchars($journalName); ?></div>
        </div>
        <?php endif; ?>
        <div class="logo-grid">
<?php 
            $count = 0;
            foreach ($items as $item): 
                if (!empty($urls[$item['key']])):
                    $count++;
            ?>
                <a href="<?php echo htmlspecialchars($urls[$item['key']]); ?>" target="_blank" class="logo-item" title="<?php echo htmlspecialchars($item['label']); ?>">
                    <img src="<?= rtrim(base_url(), '/') ?>/media/img/tool/<?php echo $item['img']; ?>" alt="<?php echo htmlspecialchars($item['label']); ?>">
                </a>
            <?php 
                endif;
            endforeach; 

            if ($count === 0):
            ?>
                <div class="empty-grid" style="grid-column: span 2;">No tool links provided.</div>
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
