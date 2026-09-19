<?php
error_reporting(0);
ini_set('display_errors', 0);


// Indexing URLs
$urls = [
    'sinta'      => $_GET['sinta'] ?? '',
    'scopus'     => $_GET['scopus'] ?? '',
    'scimago'    => $_GET['scimago'] ?? '',
    'doaj'       => $_GET['doaj'] ?? '',
    'dimensions' => $_GET['dimensions'] ?? '',
    'garuda'     => $_GET['garuda'] ?? '',
    'scholar'    => $_GET['scholar'] ?? '',
    'scilit'     => $_GET['scilit'] ?? '',
];

$warna = $_GET['warna'] ?? '4f46e5';
$warna = preg_replace('/[^a-fA-F0-9]/', '', $warna);
if (strlen($warna) < 6) $warna = '4f46e5';

[$r, $g, $b] = sscanf($warna, "%02x%02x%02x");

$journalName = trim($_GET['name'] ?? '');

$items = [
    ['key' => 'sinta',      'label' => 'Sinta',          'img' => 'Shinta.png'],
    ['key' => 'scopus',     'label' => 'Scopus',         'img' => 'Scopus.png'],
    ['key' => 'scimago',    'label' => 'Scimago',        'img' => 'Scimago.png'],
    ['key' => 'doaj',       'label' => 'DOAJ',           'img' => 'DOAJ.png'],
    ['key' => 'dimensions', 'label' => 'Dimensions',     'img' => 'Dimension.png'],
    ['key' => 'garuda',     'label' => 'Garuda',         'img' => 'Garuda.png'],
    ['key' => 'scholar',    'label' => 'Google Scholar', 'img' => 'Google-Scholar.png'],
    ['key' => 'scilit',     'label' => 'Scilit',         'img' => 'Scilit.png'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indexing Widget</title>
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
            font-family: 'Plus Jakarta Sans', sans-serif;
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
        .logo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 16px;
            background: #fff;
        }
        .logo-item {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.2s;
            text-decoration: none;
            height: 60px;
        }
        .logo-item:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.1);
        }
        .logo-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: grayscale(0.2);
            transition: filter 0.2s;
        }
        .logo-item:hover img {
            filter: grayscale(0);
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
        
        .empty-grid {
            padding: 30px 16px;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="widget-container">
        <div class="logo-grid">
<?php 
            $count = 0;
            foreach ($items as $item): 
                if (!empty($urls[$item['key']])):
                    $count++;
            ?>
                <a href="<?php echo htmlspecialchars($urls[$item['key']]); ?>" target="_blank" class="logo-item" title="<?php echo htmlspecialchars($item['label']); ?>">
                    <img src="<?= rtrim(base_url(), '/') ?>/media/img/index/<?php echo $item['img']; ?>" alt="<?php echo htmlspecialchars($item['label']); ?>">
                </a>
            <?php 
                endif;
            endforeach; 

            if ($count === 0):
            ?>
                <div class="empty-grid" style="grid-column: span 2;">No indexing links provided.</div>
            <?php endif; ?>
        </div>

        <?php if (!isset($_GET['wl']) || $_GET['wl'] != '1'): ?>
        <div class="widget-footer">Powered by <a href="<?= rtrim(base_url(), '/') ?>" target="_blank">I-Widget</a> &bull; <?php echo date('Y'); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
