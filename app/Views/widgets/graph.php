<?php
error_reporting(0);
ini_set('display_errors', 0);

// Otomatis deteksi base URL domain dan folder aplikasi

$issn = trim($_GET['issn'] ?? '');
$gsId = trim($_GET['id'] ?? $_GET['gs'] ?? '');
$sintaId = trim($_GET['sinta'] ?? '');

// Jika input Google Scholar adalah URL, ambil ID-nya saja
if (preg_match('/user=([^&]+)/', $gsId, $matches)) {
    $gsId = $matches[1];
}

$warna = $_GET['warna'] ?? '0d9488';
if (strpos($warna, '#') === 0) $warna = substr($warna, 1);
$warnaHex = '#' . $warna;

$openAlexData = [
    'total' => 0,
    'h_index' => 0,
    'i10_index' => 0,
    'history' => []
];

$scholarData = [
    'total' => 0,
    'h_index' => 0,
    'i10_index' => 0,
    'history' => []
];

// Track errors
$oaError = "";
$gsError = "";

// Fetch OpenAlex
if (!empty($issn)) {
    $url = "https://api.openalex.org/sources/issn:$issn";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'I-Widget-Bot/1.0 (mailto:admin@example.com)');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $json = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $json) {
        $data = json_decode($json, true);
        if ($data) {
            $openAlexData['name'] = $data['display_name'] ?? '';
            $openAlexData['total'] = $data['cited_by_count'] ?? 0;
            $openAlexData['h_index'] = $data['summary_stats']['h_index'] ?? 0;
            $openAlexData['i10_index'] = $data['summary_stats']['i10_index'] ?? 0;
            
            if (isset($data['counts_by_year'])) {
                $history = [];
                foreach ($data['counts_by_year'] as $entry) {
                    $history[$entry['year']] = $entry['cited_by_count'];
                }
                ksort($history);
                $openAlexData['history'] = $history;
            }
        } else {
            $oaError = "Gagal memproses data OpenAlex.";
        }
    } else {
        $oaError = "Gagal mengambil data OpenAlex (HTTP $httpCode).";
    }
}

// Fetch Google Scholar
if (!empty($gsId)) {
    $success = false;
    
    // Step 1: Try direct Google Scholar (Mungkin kena limit)
    $url = "https://scholar.google.co.id/citations?user=$gsId&hl=en";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $html && strpos($html, 'gsc_prf_in') !== false) {
        if (preg_match('/<div id="gsc_prf_in">([^<]+)<\/div>/i', $html, $matches)) {
            $scholarData['name'] = trim($matches[1]);
        }
        if (preg_match_all('/<td class="gsc_rsb_std">([^<]+)<\/td>/i', $html, $matches)) {
            $scholarData['total'] = str_replace(',', '', $matches[1][0] ?? '0');
            $scholarData['h_index'] = $matches[1][2] ?? 0;
            $scholarData['i10_index'] = $matches[1][4] ?? 0;
        }
        preg_match_all('/<span class="gsc_g_t"[^>]*>(\d+)<\/span>/i', $html, $yearMatches);
        preg_match_all('/<span class="gsc_g_al"[^>]*>(\d+)<\/span>/i', $html, $countMatches);
        if (count($yearMatches[1]) > 0) {
            $history = [];
            foreach ($yearMatches[1] as $i => $year) {
                $history[$year] = $countMatches[1][$i] ?? 0;
            }
            ksort($history);
            $scholarData['history'] = $history;
            $success = true;
        }
    }

    // Step 2: Fallback to Sinta if Scholar fails
    if (!$success && !empty($sintaId)) {
        $url = "https://sinta.kemdiktisaintek.go.id/journals/profile/$sintaId";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        curl_close($ch);

        if ($html) {
            // Extract Total, h-index, i10-index from Sinta table (Journal By Google Scholar section)
            if (preg_match('/Journal By Google Scholar.*?<table.*?Citation.*?<td.*?>(.*?)<\/td>.*?h-index.*?<td.*?>(.*?)<\/td>.*?i10-index.*?<td.*?>(.*?)<\/td>/s', $html, $statMatches)) {
                $scholarData['total'] = trim($statMatches[1]);
                $scholarData['h_index'] = trim($statMatches[2]);
                $scholarData['i10_index'] = trim($statMatches[3]);
            }
            
            // Extract Chart History
            if (preg_match('/option_gs_citation_peryear.*?xAxis:.*?data: \[(.*?)\].*?series:.*?data: \[(.*?)\]/s', $html, $chartMatches)) {
                $yearsRaw = $chartMatches[1];
                $countsRaw = $chartMatches[2];
                
                preg_match_all("/'(\d+)'/", $yearsRaw, $yearArr);
                preg_match_all("/(\d+)/", $countsRaw, $countArr);
                
                if (!empty($yearArr[1])) {
                    $history = [];
                    foreach ($yearArr[1] as $i => $year) {
                        $history[$year] = $countArr[1][$i] ?? 0;
                    }
                    ksort($history);
                    $scholarData['history'] = $history;
                    if (preg_match('/<div class="univ-name">.*?<h3>\s*<a[^>]*>(.*?)<\/a>\s*<\/h3>/is', $html, $titleMatches)) {
                        $scholarData['name'] = html_entity_decode(trim($titleMatches[1]), ENT_QUOTES, 'UTF-8');
                    } else {
                        $scholarData['name'] = "Data via Sinta Mirror";
                    }
                    $success = true;
                }
            }
        }
    }

    if (!$success) {
        $gsError = "Gagal mengambil data Google Scholar. " . ($httpCode == 429 ? "Google membatasi akses (Rate Limit)." : "ID mungkin tidak valid.");
    }
}

$oaRequested = !empty($issn);
$gsRequested = !empty($gsId);

$showOA = !empty($openAlexData['history']) || !empty($openAlexData['total']);
$showGS = !empty($scholarData['history']) || !empty($scholarData['total']);

if (!$oaRequested && !$gsRequested) {
    echo "<div style='font-family:sans-serif; text-align:center; padding: 20px; color: #666;'>ID tidak ditemukan. Masukkan ISSN atau Google Scholar ID.</div>";
    exit;
}

// If both failed and we have specific errors
if (!$showOA && !$showGS) {
    $finalError = $gsError ?: $oaError ?: "Data tidak ditemukan. Periksa kembali ID yang dimasukkan.";
    echo "<div style='font-family:sans-serif; text-align:center; padding: 40px; color: #ef4444; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; margin: 10px;'>
            <h3 style='margin-bottom: 10px;'>Gagal Mengambil Data</h3>
            <p style='font-size: 14px; color: #7f1d1d;'>$finalError</p>
            <button onclick='window.location.reload()' style='margin-top: 20px; padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;'>Coba Lagi</button>
          </div>";
    exit;
}

// Active tab
$activeTab = $showOA ? 'openalex' : 'scholar';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Sitasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: <?php echo $warnaHex; ?>;
            --primary-rgb: <?php 
                $cleanWarna = ltrim($warnaHex, '#');
                [$r, $g, $b] = sscanf($cleanWarna, "%02x%02x%02x");
                echo "$r, $g, $b";
            ?>;
            --primary-light: rgba(var(--primary-rgb), 0.12);
            --bg: #ffffff;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-glass: rgba(13, 148, 136, 0.16);
            --border-subtle: rgba(226, 232, 240, 0.85);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: transparent;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            border: 1.5px solid var(--border-glass);
            box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.08), 0 2px 6px rgba(0,0,0,0.03);
            position: relative;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3.5px;
            background: linear-gradient(90deg, #0d9488 0%, #2dd4bf 50%, #0d9488 100%);
            z-index: 2;
        }
        .tabs {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            margin: 12px 10px 4px;
            gap: 4px;
            border: 1px solid var(--border-subtle);
        }
        .tab-btn {
            flex: 1;
            padding: 7px 6px;
            border: none;
            background: transparent;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
            border-radius: 7px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .tab-btn.active {
            background: #ffffff;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .tab-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.5);
            color: var(--text-main);
        }
        .tab-content {
            display: none;
            padding: 10px 12px;
            animation: fadeIn 0.35s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tab-content.active {
            display: block;
        }
        .journal-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 12px;
            border-left: 3.5px solid var(--primary);
            padding-left: 10px;
            line-height: 1.35;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 10px;
        }
        .stat-box {
            background: linear-gradient(180deg, #f0fdfa 0%, #ffffff 100%);
            padding: 8px 4px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(13, 148, 136, 0.16);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(13, 148, 136, 0.12);
        }
        .stat-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            color: #0f766e;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }
        .stat-value {
            display: block;
            font-size: 17px;
            font-weight: 800;
            color: var(--primary);
            font-feature-settings: "tnum";
            letter-spacing: -0.3px;
        }
        .chart-container {
            position: relative;
            height: 190px;
            width: 100%;
            max-width: 100%;
            background: #ffffff;
            padding: 6px;
            border-radius: 10px;
            border: 1px solid var(--border-subtle);
            overflow: hidden;
        }
        .chart-container canvas {
            max-width: 100% !important;
        }
        .widget-footer {
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
            padding: 9px 12px;
            background: #f8fafc;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .widget-footer a { color: #0d9488; text-decoration: none; font-weight: 600; }
        .widget-footer a:hover { color: #0f766e; text-decoration: underline; }
        .f-dot { width: 4px; height: 4px; border-radius: 50%; background: #0d9488; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <div class="tabs">
        <?php if ($showOA): ?>
        <button class="tab-btn <?php echo $activeTab == 'openalex' ? 'active' : ''; ?>" onclick="openTab(event, 'oa-content')">OpenAlex</button>
        <?php endif; ?>
        <?php if ($showGS): ?>
        <button class="tab-btn <?php echo $activeTab == 'scholar' ? 'active' : ''; ?>" onclick="openTab(event, 'gs-content')">Google Scholar</button>
        <?php endif; ?>
    </div>

    <?php if ($showOA): ?>
    <div id="oa-content" class="tab-content <?php echo $activeTab == 'openalex' ? 'active' : ''; ?>">
        <div class="journal-name"><?php echo htmlspecialchars($openAlexData['name']); ?></div>
        <div class="stats-grid">
            <div class="stat-box">
                <span class="stat-label">Cited by</span>
                <span class="stat-value"><?php echo number_format($openAlexData['total']); ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-label">h-index</span>
                <span class="stat-value"><?php echo $openAlexData['h_index']; ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-label">i10-index</span>
                <span class="stat-value"><?php echo $openAlexData['i10_index']; ?></span>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="oaChart"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($showGS): ?>
    <div id="gs-content" class="tab-content <?php echo $activeTab == 'scholar' ? 'active' : ''; ?>">
        <div class="journal-name"><?php echo htmlspecialchars($scholarData['name']); ?></div>
        <div class="stats-grid">
            <div class="stat-box">
                <span class="stat-label">Cited by</span>
                <span class="stat-value"><?php echo number_format($scholarData['total']); ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-label">h-index</span>
                <span class="stat-value"><?php echo $scholarData['h_index']; ?></span>
            </div>
            <div class="stat-box">
                <span class="stat-label">i10-index</span>
                <span class="stat-value"><?php echo $scholarData['i10_index']; ?></span>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="gsChart"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!isset($_GET['wl']) || $_GET['wl'] != '1'): ?>
    <div class="widget-footer">
        <span class="f-dot"></span>
        <span>Live Data via <a href="<?= rtrim(base_url(), '/') ?>" target="_blank">I-Widget</a> &bull; <?php echo date('Y'); ?></span>
    </div>
    <?php endif; ?>
</div>

<script>
function openTab(evt, tabId) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");
}

// Chart Configurations
const primaryColor = '<?php echo $warnaHex; ?>';

<?php if ($showOA): ?>
const ctxOA = document.getElementById('oaChart').getContext('2d');
new Chart(ctxOA, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($openAlexData['history'])); ?>,
        datasets: [{
            label: 'Citations',
            data: <?php echo json_encode(array_values($openAlexData['history'])); ?>,
            backgroundColor: primaryColor,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

<?php if ($showGS): ?>
const ctxGS = document.getElementById('gsChart').getContext('2d');
new Chart(ctxGS, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($scholarData['history'])); ?>,
        datasets: [{
            label: 'Citations',
            data: <?php echo json_encode(array_values($scholarData['history'])); ?>,
            backgroundColor: primaryColor,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>
</script>
</body>
</html>
