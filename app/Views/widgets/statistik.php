<?php
error_reporting(0);
ini_set('display_errors', 0);

// Otomatis deteksi base URL domain dan folder aplikasi

// Cek apakah ada data terenkripsi/teracak (parameter 'd')
if (isset($_GET['d'])) {
    $decoded = json_decode(base64_decode($_GET['d']), true);
    
    $name = $decoded['n'] ?? '';
    $sintaId = $decoded['s'] ?? '';
    $gsId = $decoded['g'] ?? '';
    $scopusCitations = $decoded['c'] ?? '';
    $issn = $decoded['i'] ?? '';
    $stid = $decoded['ti'] ?? '';
    $staccid = $decoded['ai'] ?? '';
    $stlid = $decoded['li'] ?? '';
    $stsc = $decoded['sc'] ?? '';
    $stct = $decoded['ct'] ?? '';
    $style = $decoded['st'] ?? 'text';
    $primary = $decoded['p'] ?? '#4f46e5';
    $text = $decoded['t'] ?? '#ffffff';
} else {
    // Fallback lama jika parameter dikirim satuan
    $name = $_GET['name'] ?? '';
    $sintaId = $_GET['sinta'] ?? '';
    $gsId = $_GET['gs'] ?? '';
    $scopusCitations = $_GET['sc'] ?? '';
    $issn = $_GET['issn'] ?? '';
    $stid = $_GET['stid'] ?? '';
    $stsc = $_GET['stsc'] ?? '';
    $staccid = $_GET['staccid'] ?? '';
    $stlid = $_GET['stlid'] ?? '';
    $stct = $_GET['stct'] ?? '';
    $style = $_GET['st'] ?? 'text';
    $primary = $_GET['primary'] ?? '#4f46e5';
    $text = $_GET['text'] ?? '#ffffff';
}

// Default values
$rank = 'S2';
$impact = '0.00';
$gsCitations = '0';
$openAlexCitations = '0';

// Fetch data from Sinta if ID is provided
if (!empty($sintaId)) {
    $sintaUrl = "https://sinta.kemdiktisaintek.go.id/journals/profile/$sintaId";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sintaUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html) {
        function extractStat($html, $label) {
            $pattern = '/<div class="stat-num">([^<]+)<\/div>\s*<div class="stat-text">' . preg_quote($label, '/') . '<\/div>/i';
            if (preg_match($pattern, $html, $matches)) return trim($matches[1]);
            return null;
        }

        $rankVal = extractStat($html, 'Current Acreditation');
        if ($rankVal && preg_match('/Sinta (\d+)/i', $rankVal, $m)) $rank = 'S' . $m[1];
        
        $impactVal = extractStat($html, 'Impact');
        if ($impactVal) $impact = $impactVal;

        $gsVal = extractStat($html, 'Google Citations');
        if ($gsVal) $gsCitations = str_replace([',', '.'], '', $gsVal);
        
        $scVal = extractStat($html, 'Scopus Citations');
        if ($scVal && empty($scopusCitations)) $scopusCitations = str_replace([',', '.'], '', $scVal);

        // Extract Scholar ID if missing
        if (empty($gsId) && preg_match('/scholar\.google\.com\/citations\?user=([^&"\']+)/i', $html, $matches)) {
            $gsId = $matches[1];
        }

        // Extract ISSN if missing
        if (empty($issn)) {
            if (preg_match('/E-ISSN\s*:\s*(\d{4}-\d{4})/i', $html, $matches)) {
                $issn = $matches[1];
            } else if (preg_match('/P-ISSN\s*:\s*(\d{4}-\d{4})/i', $html, $matches)) {
                $issn = $matches[1];
            }
        }
    }
}

// Fetch OpenAlex Citations if ISSN is available
if (!empty($issn)) {
    $openAlexUrl = "https://api.openalex.org/sources/issn:$issn";
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $openAlexUrl);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch2, CURLOPT_USERAGENT, 'I-Widget-Bot/1.0 (mailto:admin@example.com)');
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 5);
    $alexJson = curl_exec($ch2);
    curl_close($ch2);

    if ($alexJson) {
        $alexData = json_decode($alexJson, true);
        if (isset($alexData['cited_by_count'])) {
            $openAlexCitations = $alexData['cited_by_count'];
        }
    }
}

// Fetch Statcounter Visitors if ID is provided and manual count is empty
if (!empty($stid) && empty($stct)) {
    // Gunakan security code jika ada (untuk project private)
    if (!empty($stsc) && !empty($staccid) && !empty($stlid)) {
        $statUrl = "https://statcounter.com/p{$stid}/summary/?account_id={$staccid}&login_id={$stlid}&code=" . urlencode($stsc) . "&guest_login=1";
    } elseif (!empty($stsc)) {
        $statUrl = "https://statcounter.com/p{$stid}/summary/?guest=1&code=" . urlencode($stsc);
    } else {
        $statUrl = "https://statcounter.com/p{$stid}/summary/?guest=1";
    }
    $ch3 = curl_init();
    curl_setopt($ch3, CURLOPT_URL, $statUrl);
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch3, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36');
    curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch3, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch3, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Referer: https://statcounter.com/'
    ]);
    $statHtml = curl_exec($ch3);
    curl_close($ch3);

    if ($statHtml) {
        $found = false;
        
        // 1. Cari spesifik "Page Views" (Sesuai Permintaan User)
        if (!$found && preg_match('/Page Views[^\d]*([\d,\.]+)/i', $statHtml, $matches)) {
            $stct = trim($matches[1]);
            $found = true;
        }

        // 2. Cari "Total Visits"
        if (!$found && preg_match('/Total Visits[^\d]*([\d,\.]+)/i', $statHtml, $matches)) {
            $stct = trim($matches[1]);
            $found = true;
        }
        
        // 3. Cari di JSON (Cek "page_views" dulu)
        if (!$found && preg_match('/"page_views"\s*:\s*(\d+)/i', $statHtml, $matches)) {
            $stct = trim($matches[1]);
            $found = true;
        }

        // 4. Fallback ke "unique_visitors" jika Page Views benar-benar tidak ada
        if (!$found && preg_match('/"unique_visitors"\s*:\s*(\d+)/i', $statHtml, $matches)) {
            $stct = trim($matches[1]);
            $found = true;
        }

        // 5. Cari "Unique Visitors" sebagai fallback terakhir
        if (!$found && preg_match('/Unique Visitors[^\d]*([\d,\.]+)/i', $statHtml, $matches)) {
            $stct = trim($matches[1]);
            $found = true;
        }
    }
}

// Colors sudah didefinisikan di bagian atas dari parameter 'd' atau fallback $_GET

// Dynamic Grid Columns
$columnCount = 3;
$showScopus = !empty($scopusCitations) && $scopusCitations !== '0';
$showOpenAlex = !empty($issn);
$showStatcounter = !empty($stid);

if ($showScopus) $columnCount++;
if ($showOpenAlex) $columnCount++;
if ($showStatcounter) $columnCount++;

// For grid layout, we might want to cap it
$gridCols = $columnCount;
if ($gridCols > 4) $gridCols = 4;

function formatNumber($num) {
    if (empty($num)) return '0';
    $n = floatval(str_replace([',', '.'], '', $num));
    if ($n >= 1000000) {
        return round($n / 1000000, 1) . 'M';
    }
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'K';
    }
    return $num;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            margin: 0; padding: 0; background: transparent; 
            font-family: 'Inter', sans-serif; overflow: hidden; 
            display: flex; align-items: flex-start; justify-content: center; 
            min-height: 100%; box-sizing: border-box;
        }
        .js-stat-widget { 
            width: 100%; max-width: 900px; margin: 0; 
            color: #333; display: flex; flex-direction: column; 
            background: #fff; border-radius: 12px; 
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .widget-header {
            background: <?php echo htmlspecialchars($primary); ?>;
            color: <?php echo htmlspecialchars($text); ?>;
            padding: 12px 15px; text-align: center; font-weight: 700; font-size: 14px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .widget-body {
            display: grid; 
            grid-template-columns: 1fr;
            gap: 8px;
            padding: 12px;
        }
        
        .stat-card {
            text-decoration: none; display: flex; 
            flex-direction: row; align-items: center; justify-content: space-between;
            background: #fdfdfd; padding: 10px 15px; border-radius: 8px; 
            border: 1px solid #f1f5f9; border-left: 5px solid #ddd;
            transition: all 0.2s;
        }
        .stat-info { display: flex; flex-direction: column; }
        .stat-label { font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; }
        .stat-sub { font-size: 9px; color: #94a3b8; }
        .stat-value { font-size: 18px; font-weight: 800; }

        /* Responsive Grid & Card Layout */
        @media (min-width: 480px) {
            .widget-body { 
                grid-template-columns: repeat(<?php echo $columnCount > 2 ? 2 : $columnCount; ?>, 1fr); 
                gap: 12px; 
                padding: 15px; 
            }
            .stat-card { flex-direction: column; padding: 20px 10px; border-left: 1px solid #f1f5f9; border-bottom: 5px solid #ddd; }
            .stat-info { align-items: center; margin-bottom: 8px; }
            .stat-value { font-size: 24px; }
        }
        @media (min-width: 768px) {
            .widget-body { grid-template-columns: repeat(<?php echo $gridCols; ?>, 1fr); }
            .stat-value { font-size: 28px; }
            .stat-label { font-size: 11px; }
        }

        .scopus { border-color: #ff8200; } .scopus .stat-value { color: #ff8200; }
        .sinta { border-color: #10586e; } .sinta .rank-badge { 
            font-size: 18px; font-weight: 800; color: #fff; 
            background: #10586e; padding: 2px 10px; border-radius: 6px; 
        }
        .impact { border-color: #10586e; } .impact .stat-value { color: #10586e; }
        .scholar { border-color: #5c87f1; } .scholar .stat-value { color: #5c87f1; }
        .openalex { border-color: #0e0e0e; } .openalex .stat-value { color: #0e0e0e; }
        .statcounter { border-color: #032964; } .statcounter .stat-value { color: #032964; }

        /* ===== LOGO MODE — Colored filled cards ===== */
        .widget-body-logo {
            display: grid;
            grid-template-columns: 1fr;   /* default: 1 kolom, stack ke bawah (sama seperti text mode di sidebar) */
            gap: 8px;
            padding: 12px;
        }
        @media (min-width: 380px) {
            .widget-body-logo { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 520px) {
            .widget-body-logo { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 680px) {
            .widget-body-logo { grid-template-columns: repeat(4, 1fr); }
        }
        .scard {
            text-decoration: none;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            color: #fff;
            transition: filter 0.2s, transform 0.15s;
            min-height: 72px;
            border: none;
        }
        .scard:hover { filter: brightness(1.12); transform: translateY(-2px); }
        .scard-icon {
            width: 46px; height: 46px; flex-shrink: 0;
            background: #ffffff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; padding: 4px;
        }
        .scard-icon img { width: 30px; height: 30px; object-fit: contain; }
        .scard-body { display: flex; flex-direction: column; gap: 1px; }
        .scard-name { font-size: 10px; font-weight: 700; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.3px; }
        .scard-value { font-size: 22px; font-weight: 800; line-height: 1.15; }
        .scard-sub { font-size: 9px; opacity: 0.75; }
        /* rank badge in logo mode */
        .scard-badge {
            display: inline-block; font-size: 20px; font-weight: 800;
            background: rgba(255,255,255,0.25);
            padding: 2px 10px; border-radius: 6px; line-height: 1.4;
        }
        /* Platform brand colors */
        .scard-scopus     { background: linear-gradient(135deg, #ff8200, #cc6800); }
        .scard-sinta      { background: linear-gradient(135deg, #10586e, #0a3d4f); }
        .scard-impact     { background: linear-gradient(135deg, #10586e, #0a3d4f); }
        .scard-scholar    { background: linear-gradient(135deg, #5c87f1, #3a67d4); }
        .scard-openalex   { background: linear-gradient(135deg, #2a2a2a, #0e0e0e); }
        .scard-statcounter{ background: linear-gradient(135deg, #1a4a8a, #032964); }
        
        .widget-footer { font-size: 9px; color: #94a3b8; text-align: center; padding: 8px 0; background: #fafafa; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="js-stat-widget">
        <?php if (!empty($name)): ?>
        <div class="widget-header"><?php echo htmlspecialchars($name); ?></div>
        <?php endif; ?>
        <div class="widget-body<?php echo $style === 'logo' ? '-logo' : ''; ?>">

        <?php if ($style === 'logo'): ?>
        <!-- ===== LOGO MODE: Colored filled cards ===== -->

            <?php if ($showScopus): ?>
            <a href="https://sinta.kemdiktisaintek.go.id/journals/profile/<?php echo $sintaId; ?>" target="_blank" rel="noopener noreferrer" class="scard scard-scopus">
                <div class="scard-icon"><img src="<?= rtrim(base_url(), '/') ?>/media/img/Scopus.webp" alt="Scopus"></div>
                <div class="scard-body">
                    <span class="scard-name">Scopus</span>
                    <span class="scard-value"><?php echo htmlspecialchars(formatNumber($scopusCitations)); ?></span>
                    <span class="scard-sub">Citedness</span>
                </div>
            </a>
            <?php endif; ?>

            <a href="https://sinta.kemdiktisaintek.go.id/journals/profile/<?php echo $sintaId; ?>" target="_blank" rel="noopener noreferrer" class="scard scard-sinta">
                <div class="scard-icon"><img src="<?= rtrim(base_url(), '/') ?>/media/img/Sinta.webp" alt="Sinta"></div>
                <div class="scard-body">
                    <span class="scard-name">Sinta</span>
                    <span class="scard-badge"><?php echo htmlspecialchars($rank); ?></span>
                    <span class="scard-sub">Sinta Rank</span>
                </div>
            </a>

            <a href="https://sinta.kemdiktisaintek.go.id/journals/profile/<?php echo $sintaId; ?>" target="_blank" rel="noopener noreferrer" class="scard scard-impact">
                <div class="scard-icon"><img src="<?= rtrim(base_url(), '/') ?>/media/img/Sinta.webp" alt="Sinta Impact"></div>
                <div class="scard-body">
                    <span class="scard-name">S30 Impact</span>
                    <span class="scard-value"><?php echo htmlspecialchars($impact); ?></span>
                    <span class="scard-sub">Score</span>
                </div>
            </a>

            <a href="https://scholar.google.com/citations?user=<?php echo $gsId; ?>" target="_blank" rel="noopener noreferrer" class="scard scard-scholar">
                <div class="scard-icon"><img src="<?= rtrim(base_url(), '/') ?>/media/img/Google Scholar.webp" alt="Google Scholar"></div>
                <div class="scard-body">
                    <span class="scard-name">Google Scholar</span>
                    <span class="scard-value"><?php echo htmlspecialchars(formatNumber($gsCitations)); ?></span>
                    <span class="scard-sub">Citations</span>
                </div>
            </a>

            <?php if ($showOpenAlex): ?>
            <a href="https://openalex.org/sources/issn:<?php echo $issn; ?>" target="_blank" rel="noopener noreferrer" class="scard scard-openalex">
                <div class="scard-icon"><img src="<?= rtrim(base_url(), '/') ?>/media/img/Openalex.webp" alt="OpenAlex"></div>
                <div class="scard-body">
                    <span class="scard-name">OpenAlex</span>
                    <span class="scard-value"><?php echo htmlspecialchars(formatNumber($openAlexCitations)); ?></span>
                    <span class="scard-sub">Citations</span>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($showStatcounter): ?>
            <a href="https://statcounter.com/p<?php echo $stid; ?>/?guest=1" target="_blank" rel="noopener noreferrer" class="scard scard-statcounter">
                <div class="scard-icon"><img src="<?= rtrim(base_url(), '/') ?>/media/img/Statcounter.webp" alt="Statcounter"></div>
                <div class="scard-body">
                    <span class="scard-name">Statcounter</span>
                    <span class="scard-value"><?php echo htmlspecialchars(formatNumber($stct)); ?></span>
                    <span class="scard-sub">Visitors</span>
                </div>
            </a>
            <?php endif; ?>

        <?php else: ?>
        <!-- ===== TEXT MODE: White cards with colored borders ===== -->

            <?php if ($showScopus): ?>
            <a href="https://sinta.kemdiktisaintek.go.id/journals/profile/<?php echo $sintaId; ?>" target="_blank" rel="noopener noreferrer" class="stat-card scopus">
                <div class="stat-info">
                    <span class="stat-label">Scopus</span>
                    <span class="stat-sub">Citations</span>
                </div>
                <span class="stat-value"><?php echo htmlspecialchars(formatNumber($scopusCitations)); ?></span>
            </a>
            <?php endif; ?>

            <a href="https://sinta.kemdiktisaintek.go.id/journals/profile/<?php echo $sintaId; ?>" target="_blank" rel="noopener noreferrer" class="stat-card sinta">
                <div class="stat-info">
                    <span class="stat-label">Sinta Rank</span>
                    <span class="stat-sub">Accredited</span>
                </div>
                <span class="rank-badge"><?php echo htmlspecialchars($rank); ?></span>
            </a>

            <a href="https://sinta.kemdiktisaintek.go.id/journals/profile/<?php echo $sintaId; ?>" target="_blank" rel="noopener noreferrer" class="stat-card impact">
                <div class="stat-info">
                    <span class="stat-label">S30 Impact</span>
                    <span class="stat-sub">Score</span>
                </div>
                <span class="stat-value"><?php echo htmlspecialchars($impact); ?></span>
            </a>

            <a href="https://scholar.google.com/citations?user=<?php echo $gsId; ?>" target="_blank" rel="noopener noreferrer" class="stat-card scholar">
                <div class="stat-info">
                    <span class="stat-label">Google Scholar</span>
                    <span class="stat-sub">Total Citations</span>
                </div>
                <span class="stat-value"><?php echo htmlspecialchars(formatNumber($gsCitations)); ?></span>
            </a>

            <?php if ($showOpenAlex): ?>
            <a href="https://openalex.org/sources/issn:<?php echo $issn; ?>" target="_blank" rel="noopener noreferrer" class="stat-card openalex">
                <div class="stat-info">
                    <span class="stat-label">OpenAlex</span>
                    <span class="stat-sub">Citations</span>
                </div>
                <span class="stat-value"><?php echo htmlspecialchars(formatNumber($openAlexCitations)); ?></span>
            </a>
            <?php endif; ?>

            <?php if ($showStatcounter): ?>
            <a href="https://statcounter.com/p<?php echo $stid; ?>/?guest=1" target="_blank" rel="noopener noreferrer" class="stat-card statcounter">
                <div class="stat-info">
                    <span class="stat-label">Statcounter</span>
                    <span class="stat-sub">Visitors</span>
                </div>
                <span class="stat-value"><?php echo htmlspecialchars(formatNumber($stct)); ?></span>
            </a>
            <?php endif; ?>

        <?php endif; ?>
        </div>
        <?php if (!isset($_GET['wl']) || $_GET['wl'] != '1'): ?>
        <div class="widget-footer">Updated via <a href="<?= rtrim(base_url(), '/') ?>" target="_blank" style="color: inherit; text-decoration: none; font-weight: 600;">I-Widget</a> &bull; <?php echo date('Y'); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
