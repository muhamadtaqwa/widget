<?php
error_reporting(0);
ini_set('display_errors', 0);

// Otomatis deteksi base URL domain dan folder aplikasi

$id          = $_GET['id']    ?? '';
$warna       = $_GET['warna'] ?? '0d9488';
$rawData     = $_GET['data']  ?? '';
$journalName = trim($_GET['name'] ?? '');

// Sanitize
$id    = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$warna = preg_replace('/[^a-fA-F0-9]/', '', $warna);
if (strlen($warna) < 6) $warna = '0d9488';

$scimagoUrl = "https://www.scimagojr.com/journalsearch.php?q=$id&tip=sid&clean=0";
$scimagoImg = "https://www.scimagojr.com/journal_img.php?id=$id";

// Decode manual data (Base64 JSON)
$manualData = null;
if ($rawData) {
    $decoded = base64_decode(strtr($rawData, '-_', '+/'));
    if ($decoded) {
        $manualData = json_decode($decoded, true);
    }
}

$quartileData = $manualData['q'] ?? [];
$sjrData      = $manualData['s'] ?? [];

// Sort SJR descending by year
usort($sjrData, fn($a, $b) => intval($b['year']) - intval($a['year']));

// Best quartile
$bestQ = '';
foreach ($quartileData as $row) {
    $q = strtoupper(trim($row['quartile'] ?? ''));
    if (!$bestQ || intval($q[1]) < intval($bestQ[1])) $bestQ = $q;
}
$latestSJR = count($sjrData) > 0 ? $sjrData[0]['value'] : '';

function qClass($q) {
    $q = strtoupper(trim($q));
    $map = ['Q1'=>'q1','Q2'=>'q2','Q3'=>'q3','Q4'=>'q4'];
    return $map[$q] ?? 'qna';
}

// PHP hex -> RGB for CSS variable
[$r, $g, $b] = sscanf($warna, "%02x%02x%02x");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scimago Widget</title>
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
            --bg-row-alt: #f8fafc;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: transparent;
            color: var(--text-main);
            overflow-x: hidden;
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
            min-height: <?php echo $journalName ? '420px' : '380px'; ?>;
            height: auto;
            overflow: hidden;
            margin: 0 auto;
            position: relative;
        }
        .widget-container::before {
            display: none;
        }
        .widget-header {
            padding: 12px 14px 8px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        .journal-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: 0.2px;
        }
        .tabs {
            display: flex;
            background: #f1f5f9;
            padding: 3px;
            margin: 8px 10px 4px;
            border-radius: 8px;
            gap: 3px;
            border: 1px solid var(--border);
        }
        .tab-btn {
            flex: 1;
            padding: 6px 3px;
            font-size: 9.5px;
            font-weight: 700;
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .tab-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .tab-content {
            flex-grow: 1;
            overflow-y: auto;
            display: none;
        }
        .tab-content.active { display: block; }

        /* Overview */
        .overview-inner {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        .scimago-img-link {
            display: block;
            background: #ffffff;
            padding: 8px;
            border-radius: 10px;
            border: 1.5px dashed rgba(13, 148, 136, 0.25);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .scimago-img-link:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(13, 148, 136, 0.1);
        }
        .scimago-img-link img { max-width: 100%; height: auto; border-radius: 4px; display: block; }
        .overview-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            width: 100%;
        }
        .stat-card {
            background: linear-gradient(180deg, #f0fdfa 0%, #ffffff 100%);
            border: 1px solid rgba(13, 148, 136, 0.18);
            border-radius: 10px;
            padding: 10px 8px;
            text-align: center;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.1);
        }
        .stat-card .slabel {
            font-size: 9px;
            font-weight: 700;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: block;
            margin-bottom: 2px;
        }
        .stat-card .svalue {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
            font-feature-settings: "tnum";
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead th {
            background: #f0fdfa;
            color: #0f766e;
            padding: 8px 10px;
            font-weight: 700;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1.5px solid rgba(13, 148, 136, 0.25);
        }
        tbody td { padding: 8px 10px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tbody tr:nth-child(even) { background: var(--bg-row-alt); }
        tbody tr:hover { background: #f0fdfa; }

        .q-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: 800;
            color: #fff;
            font-size: 10px;
            letter-spacing: 0.3px;
        }
        .q1 { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25); }
        .q2 { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 2px 6px rgba(245, 158, 11, 0.25); }
        .q3 { background: linear-gradient(135deg, #f97316, #ea580c); box-shadow: 0 2px 6px rgba(249, 115, 22, 0.25); }
        .q4 { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); }
        .qna { background: #94a3b8; }

        .empty-state {
            padding: 24px 14px;
            text-align: center;
            color: var(--text-muted);
            font-size: 11px;
        }

        /* SJR Progress Bar */
        .sjr-bar-row {
            display: flex;
            align-items: center;
            padding: 7px 10px;
            gap: 8px;
            border-bottom: 1px solid var(--border);
        }
        .sjr-bar-row:nth-child(even) { background: var(--bg-row-alt); }
        .sjr-year-label {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-muted);
            width: 36px;
            flex-shrink: 0;
        }
        .sjr-bar-wrap {
            flex-grow: 1;
            background: #f1f5f9;
            border-radius: 999px;
            height: 7px;
            overflow: hidden;
        }
        .sjr-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #0d9488, #2dd4bf);
        }
        .sjr-val-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--primary);
            width: 44px;
            text-align: right;
            flex-shrink: 0;
            font-feature-settings: "tnum";
        }
        .widget-footer {
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
            padding: 9px 12px;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .widget-footer a { color: #0d9488; text-decoration: none; font-weight: 600; }
        .widget-footer a:hover { color: #0f766e; text-decoration: underline; }
        .f-dot { width: 4px; height: 4px; border-radius: 50%; background: #0d9488; display: inline-block; }

        @media (max-width: 380px) {
            .overview-stats {
                grid-template-columns: 1fr;
            }
            .tabs {
                flex-wrap: wrap;
            }
            .tab-btn {
                flex: 1 1 45%;
                font-size: 9px;
            }
        }
    </style>
</head>
<body>
    <div class="widget-container<?php echo $journalName ? '' : ' no-header'; ?>">
        <?php if ($journalName): ?>
        <div class="widget-header">
            <div class="journal-title"><?php echo htmlspecialchars($journalName); ?></div>
        </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn active" data-tab="overview" onclick="showTab(this, 'overview')">Overview</button>
            <button class="tab-btn" data-tab="quartiles" onclick="showTab(this, 'quartiles')">Quartiles</button>
            <button class="tab-btn" data-tab="sjr" onclick="showTab(this, 'sjr')">SJR Rank</button>
        </div>

        <!-- Overview Tab -->
        <div id="overview" class="tab-content active">
            <div class="overview-inner">
                <a href="<?php echo $scimagoUrl; ?>" target="_blank" class="scimago-img-link">
                    <img src="<?php echo $scimagoImg; ?>" alt="Scimago Widget" id="scimagoImg">
                </a>
                <div class="overview-stats">
                    <div class="stat-card">
                        <span class="slabel">SJR</span>
                        <div class="svalue" id="sjrOverview"><?php echo $latestSJR ?: '—'; ?></div>
                    </div>
                    <div class="stat-card">
                        <span class="slabel">Best Quartile</span>
                        <div class="svalue" id="quartileOverview" style="font-size:14px;padding-top:4px;">
                            <?php if ($bestQ): ?>
                                <span class="q-badge <?php echo qClass($bestQ); ?>"><?php echo htmlspecialchars($bestQ); ?></span>
                            <?php else: ?>
                                <span class="q-badge qna">—</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quartiles Tab -->
        <div id="quartiles" class="tab-content">
            <?php if (count($quartileData) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject Area Category</th>
                            <th>Quartile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quartileData as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <span class="q-badge <?php echo qClass($row['quartile']); ?>">
                                        <?php echo htmlspecialchars(strtoupper($row['quartile'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">No quartile data entered.</div>
            <?php endif; ?>
        </div>

        <!-- SJR Rank Tab -->
        <div id="sjr" class="tab-content">
            <?php if (count($sjrData) > 0):
                // Find max SJR value for bar width calculation
                $maxSJR = max(array_map(fn($r) => floatval($r['value']), $sjrData));
            ?>
                <?php foreach ($sjrData as $row):
                    $pct = $maxSJR > 0 ? round(floatval($row['value']) / $maxSJR * 100) : 0;
                ?>
                <div class="sjr-bar-row">
                    <span class="sjr-year-label"><?php echo htmlspecialchars($row['year']); ?></span>
                    <div class="sjr-bar-wrap">
                        <div class="sjr-bar-fill" style="width:<?php echo $pct; ?>%"></div>
                    </div>
                    <span class="sjr-val-label"><?php echo htmlspecialchars($row['value']); ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No SJR data entered.</div>
            <?php endif; ?>
        </div>

        <?php if (!isset($_GET['wl']) || $_GET['wl'] != '1'): ?>
        <div class="widget-footer">
            <span class="f-dot"></span>
            <span>Live Data via <a href="<?= rtrim(base_url(), '/') ?>" target="_blank">I-Widget</a> &bull; <?php echo date('Y'); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function showTab(btn, tabId) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            if (btn && btn.classList) {
                btn.classList.add('active');
            } else {
                const targetBtn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
                if (targetBtn) targetBtn.classList.add('active');
            }
            const targetContent = document.getElementById(tabId);
            if (targetContent) targetContent.classList.add('active');
        }
    </script>
</body>
</html>
