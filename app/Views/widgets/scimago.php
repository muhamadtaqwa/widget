<?php
error_reporting(0);
ini_set('display_errors', 0);

// Otomatis deteksi base URL domain dan folder aplikasi

$id          = $_GET['id']    ?? '';
$warna       = $_GET['warna'] ?? '4f46e5';
$rawData     = $_GET['data']  ?? '';
$journalName = trim($_GET['name'] ?? '');

// Sanitize
$id    = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$warna = preg_replace('/[^a-fA-F0-9]/', '', $warna);
if (strlen($warna) < 6) $warna = '4f46e5';

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
            --bg-row-alt: #f8fafc;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: transparent;
            color: var(--text-main);
            overflow-x: hidden;
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
            min-height: <?php echo $journalName ? '440px' : '400px'; ?>;
            height: auto;
            overflow: hidden;
            margin: 10px auto;
            position: relative;
        }
        .widget-container.no-header::before {
            display: none;
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
        .journal-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .tabs {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            gap: 3px;
        }
        .tab-btn {
            flex: 1;
            padding: 7px 4px;
            font-size: 10px;
            font-weight: 700;
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 7px;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .tab-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
        }
        .tab-content {
            flex-grow: 1;
            overflow-y: auto;
            display: none;
        }
        .tab-content.active { display: block; }

        /* Overview */
        .overview-inner {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        .scimago-img-link {
            display: block;
            background: #f8fafc;
            padding: 10px;
            border-radius: 12px;
            border: 1px dashed var(--border);
            transition: transform 0.2s;
        }
        .scimago-img-link:hover { transform: scale(1.02); }
        .scimago-img-link img { max-width: 100%; height: auto; border-radius: 4px; display: block; }
        .overview-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            width: 100%;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }
        .stat-card .slabel {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }
        .stat-card .svalue {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead th {
            background: var(--primary-light);
            color: var(--primary);
            padding: 10px 12px;
            font-weight: 700;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid var(--primary);
        }
        tbody td { padding: 9px 12px; border-bottom: 1px solid var(--border); }
        tbody tr:nth-child(even) { background: var(--bg-row-alt); }
        tbody tr:hover { background: var(--primary-light); }

        .q-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: 800;
            color: #fff;
            font-size: 11px;
            letter-spacing: 0.3px;
        }
        .q1 { background: #10b981; }
        .q2 { background: #f59e0b; }
        .q3 { background: #f97316; }
        .q4 { background: #ef4444; }
        .qna { background: #94a3b8; }

        .empty-state {
            padding: 30px 16px;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* SJR Progress Bar */
        .sjr-bar-row {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            gap: 10px;
            border-bottom: 1px solid var(--border);
        }
        .sjr-bar-row:nth-child(even) { background: var(--bg-row-alt); }
        .sjr-year-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            width: 38px;
            flex-shrink: 0;
        }
        .sjr-bar-wrap {
            flex-grow: 1;
            background: #e2e8f0;
            border-radius: 99px;
            height: 8px;
            overflow: hidden;
        }
        .sjr-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--primary), rgba(var(--primary-rgb), 0.6));
            transition: width 0.6s ease;
        }
        .sjr-val-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--primary);
            width: 42px;
            text-align: right;
            flex-shrink: 0;
        }

        .widget-footer {
            padding: 8px 12px;
            font-size: 10px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            flex-shrink: 0;
        }
        .widget-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }

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
            <button class="tab-btn active" onclick="showTab('overview')">Overview</button>
            <button class="tab-btn" onclick="showTab('quartiles')">Quartiles</button>
            <button class="tab-btn" onclick="showTab('sjr')">SJR Rank</button>
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
        <div class="widget-footer">Updated via <a href="<?= rtrim(base_url(), '/') ?>" target="_blank" style="color: inherit; text-decoration: none; font-weight: 600;">I-Widget</a> &bull; <?php echo date('Y'); ?></div>
        <?php endif; ?>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelector(`button[onclick="showTab('${tabId}')"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</body>
</html>
