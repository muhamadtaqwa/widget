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
    $primary = $decoded['p'] ?? '#0d9488';
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
    $primary = $_GET['primary'] ?? '#0d9488';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: <?php echo htmlspecialchars($primary); ?>;
            --text-on-primary: <?php echo htmlspecialchars($text); ?>;
            --tosca-50: #f0fdfa;
            --tosca-100: #ccfbf1;
            --tosca-500: #14b8a6;
            --tosca-600: #0d9488;
            --tosca-700: #0f766e;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-glass: rgba(13, 148, 136, 0.16);
            --border-subtle: rgba(226, 232, 240, 0.85);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            margin: 0; padding: 0; background: transparent; 
            font-family: 'Poppins', sans-serif; overflow: hidden; 
            display: flex; align-items: flex-start; justify-content: center; 
            min-height: 100%; box-sizing: border-box;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }
        .js-stat-widget { 
            width: 100%; max-width: 900px; margin: 0; 
            display: flex; flex-direction: column; 
            background: #ffffff; border-radius: 16px; 
            border: 1.5px solid var(--border-glass); 
            box-shadow: 0 10px 30px -5px rgba(13, 148, 136, 0.08), 0 2px 6px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            position: relative;
        }
        .js-stat-widget::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3.5px;
            background: linear-gradient(90deg, #0d9488 0%, #2dd4bf 50%, #0d9488 100%);
            z-index: 2;
        }
        .widget-header {
            background: linear-gradient(135deg, #0f766e 0%, var(--primary) 60%, #14b8a6 100%);
            color: var(--text-on-primary);
            padding: 13px 18px; text-align: center; font-weight: 700; font-size: 13.5px;
            letter-spacing: 0.3px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            box-shadow: inset 0 -1px 0 rgba(255,255,255,0.15);
        }
        .widget-body {
            display: grid; 
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 14px;
            background: #ffffff;
        }
        
        .stat-card {
            text-decoration: none; display: flex; 
            flex-direction: row; align-items: center; justify-content: space-between;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            padding: 11px 16px; border-radius: 12px; 
            border: 1px solid var(--border-subtle);
            position: relative;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            border-radius: 4px 0 0 4px;
            transition: width 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 148, 136, 0.1);
            border-color: rgba(13, 148, 136, 0.35);
            background: linear-gradient(180deg, #ffffff 0%, #f0fdfa 100%);
        }
        .stat-card:hover::before {
            width: 6px;
        }
        .stat-info { display: flex; flex-direction: column; gap: 2px; }
        .stat-label { font-size: 10px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-sub { font-size: 9px; color: #94a3b8; font-weight: 500; }
        .stat-value { font-size: 20px; font-weight: 800; font-feature-settings: "tnum"; letter-spacing: -0.3px; }

        /* Responsive Grid & Card Layout */
        @media (min-width: 480px) {
            .widget-body { 
                grid-template-columns: repeat(<?php echo $columnCount > 2 ? 2 : $columnCount; ?>, 1fr); 
                gap: 12px; 
                padding: 16px; 
            }
            .stat-card { 
                flex-direction: column; 
                padding: 16px 12px; 
                align-items: center; 
                text-align: center; 
            }
            .stat-card::before {
                left: 0; right: 0; bottom: 0; top: auto;
                width: 100%; height: 3.5px;
                border-radius: 0 0 12px 12px;
            }
            .stat-card:hover::before {
                width: 100%; height: 5px;
            }
            .stat-info { align-items: center; margin-bottom: 8px; }
            .stat-value { font-size: 24px; }
        }
        @media (min-width: 768px) {
            .widget-body { grid-template-columns: repeat(<?php echo $gridCols; ?>, 1fr); }
            .stat-value { font-size: 26px; }
            .stat-label { font-size: 10.5px; }
        }

        .scopus::before { background: linear-gradient(180deg, #ff8200, #ea580c); } 
        .scopus .stat-value { color: #ea580c; }
        
        .sinta::before { background: linear-gradient(180deg, #0d9488, #14b8a6); } 
        .sinta .rank-badge { 
            font-size: 15px; font-weight: 800; color: #fff; 
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); 
            padding: 3px 12px; border-radius: 999px; 
            box-shadow: 0 2px 8px rgba(13, 148, 136, 0.3);
            letter-spacing: 0.3px;
        }
        .impact::before { background: linear-gradient(180deg, #0f766e, #0d9488); } 
        .impact .stat-value { color: #0d9488; }
        
        .scholar::before { background: linear-gradient(180deg, #3b82f6, #2563eb); } 
        .scholar .stat-value { color: #2563eb; }
        
        .openalex::before { background: linear-gradient(180deg, #334155, #0f172a); } 
        .openalex .stat-value { color: #0f172a; }
        
        .statcounter::before { background: linear-gradient(180deg, #0284c7, #0369a1); } 
        .statcounter .stat-value { color: #0369a1; }

        /* ===== LOGO MODE — Modern vibrant gradient cards ===== */
        .widget-body-logo {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 14px;
            background: #ffffff;
        }
        @media (min-width: 380px) {
            .widget-body-logo { grid-template-columns: repeat(2, 1fr); gap: 12px; }
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
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #fff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            min-height: 72px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        .scard::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 45%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.15) 0%, transparent 100%);
            pointer-events: none;
        }
        .scard:hover { 
            transform: translateY(-2px); 
            filter: brightness(1.08); 
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.16);
        }
        .scard-icon {
            width: 44px; height: 44px; flex-shrink: 0;
            background: #ffffff;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; padding: 4px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
        }
        .scard-icon img { width: 28px; height: 28px; object-fit: contain; }
        .scard-body { display: flex; flex-direction: column; gap: 1px; }
        .scard-name { font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.4px; }
        .scard-value { font-size: 21px; font-weight: 800; line-height: 1.15; letter-spacing: -0.3px; }
        .scard-sub { font-size: 9px; opacity: 0.8; }
        /* rank badge in logo mode */
        .scard-badge {
            display: inline-block; font-size: 16px; font-weight: 800;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(4px);
            padding: 2px 10px; border-radius: 999px; line-height: 1.4;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }
        /* Platform brand colors */
        .scard-scopus     { background: linear-gradient(135deg, #ff8200 0%, #ea580c 100%); box-shadow: 0 4px 12px rgba(255, 130, 0, 0.2); }
        .scard-sinta      { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2); }
        .scard-impact     { background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2); }
        .scard-scholar    { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); }
        .scard-openalex   { background: linear-gradient(135deg, #334155 0%, #0f172a 100%); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2); }
        .scard-statcounter{ background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); }
        
        .widget-footer { 
            font-size: 10px; color: var(--text-muted); text-align: center; 
            padding: 9px 12px; background: #f8fafc; 
            border-top: 1px solid var(--border-subtle);
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .widget-footer a { color: var(--tosca-600); text-decoration: none; font-weight: 600; }
        .widget-footer a:hover { color: var(--tosca-700); text-decoration: underline; }
        .f-dot { width: 4px; height: 4px; border-radius: 50%; background: var(--tosca-600); display: inline-block; }
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
        <div class="widget-footer">
            <span class="f-dot"></span>
            <span>Live Data via <a href="<?= rtrim(base_url(), '/') ?>" target="_blank">I-Widget</a> &bull; <?php echo date('Y'); ?></span>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
