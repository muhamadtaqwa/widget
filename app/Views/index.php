<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, ['en', 'id'])) {
    $lang = 'en';
}

$seo = [
    'en' => [
        'title' => 'I-Widget | Premium OJS Journal Statistics Widget Generator',
        'desc' => 'I-Widget is a premium journal statistics widget generator for Open Journal Systems (OJS).',
        'keywords' => 'i-widget, journal widget, ojs widget, journal stats generator'
    ],
    'id' => [
        'title' => 'I-Widget | Generator Widget Statistik Jurnal OJS Premium',
        'desc' => 'I-Widget adalah tool generator widget statistik jurnal premium untuk Open Journal Systems (OJS).',
        'keywords' => 'i-widget, widget jurnal, ojs widget, generator statistik jurnal'
    ]
];

$currentSeo = $seo[$lang];

// ===== Load widget data if edit mode =====
$editWidgetId = $_GET['edit'] ?? null;
$w = null;
$settings = [];

if ($editWidgetId) {
    $widgetModel = new \App\Models\WidgetModel();
    $w = $widgetModel->where('id', $editWidgetId)
        ->where('user_id', session()->get('user_id'))
        ->first();

    if ($w) {
        $settings = json_decode($w['settings'], true) ?? [];
    }
}

$val = function ($key, $default = '') use ($settings) {
    return $settings[$key] ?? $default;
};

$valUrl = function ($key, $default = '') use ($settings) {
    return $settings['urls'][$key] ?? $default;
};

$currentType  = $val('widget_type', 'table');
$currentStyle = $val('table_style', 'text');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($currentSeo['title']) ?></title>
    <meta name="title" content="<?= htmlspecialchars($currentSeo['title']) ?>">
    <meta name="description" content="<?= htmlspecialchars($currentSeo['desc']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($currentSeo['keywords']) ?>">
    <meta name="author" content="I-Widget">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= base_url($lang === 'en' ? '' : 'id') ?>">

    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:title" content="<?= htmlspecialchars($currentSeo['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($currentSeo['desc']) ?>">
    <meta property="og:image" content="<?= base_url('media/img/thumbnail.webp') ?>">

    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg?v=2') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('media/img/favicon/apple-touch-icon.png?v=2') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('media/img/favicon/favicon-32x32.png?v=2') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('media/img/favicon/favicon-16x16.png?v=2') ?>">
    <link rel="manifest" href="<?= base_url('media/img/favicon/site.webmanifest?v=2') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico?v=2') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=4') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window.APP_LANG = "<?php echo $lang; ?>";
    </script>
</head>

<body>
    <div class="glow-bg"></div>

    <?= $this->include('layouts/navbar') ?>

    <div class="container">
        <header class="hero-section">
            <div class="hero-pill-wrapper">
                <div class="hero-pill">
                    <i data-lucide="sparkles"></i>
                    <span>OJS Widget Generator</span>
                </div>
                <button id="premiumTrigger" class="btn-premium-trigger" type="button">
                    <i data-lucide="crown"></i>
                    <span>Upgrade Premium</span>
                </button>
            </div>
            <h1 class="hero-title">
                Widget Jurnal OJS <span class="hero-title-gradient">Modern &amp; Interaktif</span>
            </h1>
            <p class="hero-subtitle" data-i18n="subtitle">Implementasi &amp; Generator Widget Jurnal Premium untuk Open Journal Systems</p>
            <div class="hero-features">
                <span class="feature-chip"><i data-lucide="smartphone"></i> Responsif Mobile &amp; Desktop</span>
                <span class="feature-chip"><i data-lucide="refresh-cw"></i> Sinkronisasi Sinta &amp; OpenAlex</span>
                <span class="feature-chip"><i data-lucide="code-2"></i> 1-Click Embed Iframe</span>
                <span class="feature-chip"><i data-lucide="shield-check"></i> Bebas Emoji &amp; Profesional</span>
            </div>
        </header>

        <main class="main-content">
            <section class="glass-card" id="konfigurasi">
                <div class="card-header">
                    <div class="header-title">
                        <div class="header-icon-box">
                            <i data-lucide="settings-2"></i>
                        </div>
                        <h2 data-i18n="configTitle">Journal Configuration</h2>
                    </div>
                </div>

                <input type="hidden" id="widgetId" value="<?= $w['id'] ?? '' ?>">

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label id="widgetTypeLabel" data-i18n="widgetType">Widget Type</label>
                        <div class="widget-type-selector" role="radiogroup" aria-labelledby="widgetTypeLabel">
                            <label class="type-option" for="typeTable">
                                <input type="radio" name="widgetType" id="typeTable" value="table" <?= $currentType === 'table' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="layout"></i>
                                    <span data-i18n="typeTable">Statistics Table</span>
                                </div>
                            </label>
                            <label class="type-option" for="typeChart">
                                <input type="radio" name="widgetType" id="typeChart" value="chart" <?= $currentType === 'chart' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="bar-chart-2"></i>
                                    <span data-i18n="typeChart">Citation Chart</span>
                                </div>
                            </label>
                            <label class="type-option" for="typeScimago">
                                <input type="radio" name="widgetType" id="typeScimago" value="scimago" <?= $currentType === 'scimago' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="award"></i>
                                    <span data-i18n="typeScimago">Scimago Widget</span>
                                </div>
                            </label>
                            <label class="type-option" for="typeIndexing">
                                <input type="radio" name="widgetType" id="typeIndexing" value="indexing" <?= $currentType === 'indexing' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="link"></i>
                                    <span data-i18n="typeIndexing">Indexing List</span>
                                </div>
                            </label>
                            <label class="type-option" for="typeTools">
                                <input type="radio" name="widgetType" id="typeTools" value="tools" <?= $currentType === 'tools' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="wrench"></i>
                                    <span data-i18n="typeTools">Used Tools</span>
                                </div>
                            </label>
                            <label class="type-option" for="typeTemplate">
                                <input type="radio" name="widgetType" id="typeTemplate" value="template" <?= $currentType === 'template' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="download"></i>
                                    <span data-i18n="typeTemplate">Journal Template</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width chart-options hidden" id="chartOptions">
                        <label id="chartOptionsLabel" data-i18n="chartOptions">Chart Options</label>
                        <div class="chart-selector-group" role="group" aria-labelledby="chartOptionsLabel">
                            <label class="chart-option" for="showOpenAlexChart">
                                <input type="checkbox" id="showOpenAlexChart" checked>
                                <div class="chart-option-content">
                                    <i data-lucide="circle-check" class="check-icon"></i>
                                    <i data-lucide="circle" class="uncheck-icon"></i>
                                    <span>OpenAlex</span>
                                </div>
                            </label>
                            <label class="chart-option" for="showScholarChart">
                                <input type="checkbox" id="showScholarChart" checked>
                                <div class="chart-option-content">
                                    <i data-lucide="circle-check" class="check-icon"></i>
                                    <i data-lucide="circle" class="uncheck-icon"></i>
                                    <span>Google Scholar</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width" id="tableStyleOption">
                        <label id="tableStyleLabel" data-i18n="tableView">Table View</label>
                        <div class="widget-type-selector" role="radiogroup" aria-labelledby="tableStyleLabel">
                            <label class="type-option" for="tableStyleText">
                                <input type="radio" name="tableStyle" value="text" id="tableStyleText" <?= $currentStyle === 'text' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <i data-lucide="align-left"></i>
                                    <span data-i18n="viewText">Text Only</span>
                                </div>
                            </label>
                            <label class="type-option" for="tableStyleLogo">
                                <input type="radio" name="tableStyle" value="logo" id="tableStyleLogo" <?= $currentStyle === 'logo' ? 'checked' : '' ?>>
                                <div class="type-info">
                                    <div class="logo-preview-chips">
                                        <img src="<?= base_url('media/img/Sinta.webp') ?>" alt="Sinta" class="logo-chip-img">
                                        <img src="<?= base_url('media/img/Google Scholar.webp') ?>" alt="Google Scholar" class="logo-chip-img">
                                        <img src="<?= base_url('media/img/Scopus.webp') ?>" alt="Scopus" class="logo-chip-img">
                                        <img src="<?= base_url('media/img/Openalex.webp') ?>" alt="OpenAlex" class="logo-chip-img">
                                        <img src="<?= base_url('media/img/Statcounter.webp') ?>" alt="Statcounter" class="logo-chip-img">
                                    </div>
                                    <span data-i18n="viewLogo">Platform Logo</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="journalNameGroup">
                        <label for="journalName" data-i18n="labelJournalName">Journal Name (Optional)</label>
                        <input type="text" id="journalName" placeholder="Example: Journal of Innovation Technology" value="<?= esc($w['journal_name'] ?? 'Journal of Science & Technology') ?>">
                    </div>

                    <div class="form-group" id="sintaIdGroup">
                        <label for="sintaId">Sinta ID</label>
                        <input type="text" id="sintaId" placeholder="Journal ID" value="<?= esc($val('sinta_id', '292')) ?>">
                    </div>

                    <div class="form-group" id="eissnGroup">
                        <label for="eissn">e-ISSN (OpenAlex)</label>
                        <input type="text" id="eissn" placeholder="Contoh: 2656-8012" value="<?= esc($val('eissn', '2656-8012')) ?>">
                    </div>

                    <div class="form-group" id="scopusCitationsGroup">
                        <label for="scopusCitations" data-i18n="labelScopus">Scopus Citations (Optional)</label>
                        <input type="number" id="scopusCitations" placeholder="Total" value="<?= esc($val('scopus_citations', '455')) ?>">
                    </div>

                    <div class="form-group" id="scholarIdGroup">
                        <label for="scholarId">Google Scholar ID</label>
                        <input type="text" id="scholarId" placeholder="Example: 4u6oAAAAJ" value="<?= esc($val('scholar_id', 'cvLFxv0AAAAJ')) ?>">
                    </div>

                    <div class="form-group" id="statcounterCountGroup">
                        <label for="statcounterCount" data-i18n="labelVisitor">Visitor Count <span style="font-weight:400;color:#94a3b8;">(Manual/Override)</span></label>
                        <input type="text" id="statcounterCount" placeholder="Leave empty = fetch automatically" value="<?= esc($val('statcounter_count', '')) ?>">
                    </div>

                    <div class="full-width hidden" id="scimagoIdGroup">
                        <label for="scimagoId">Scimago / Scopus ID</label>
                        <input type="text" id="scimagoId" placeholder="Example: 21101090010" value="<?= esc($val('scimago_id', '21101090010')) ?>">
                    </div>

                    <div class="full-width hidden scimago-data-section" id="quartileSection">
                        <div class="scimago-section-header">
                            <span data-i18n="quartilesHeader"><i data-lucide="layers"></i> Quartiles</span>
                            <button class="add-row-btn" type="button" onclick="addQuartileRow()"><i data-lucide="plus"></i> <span data-i18n="addRow">Add Row</span></button>
                        </div>
                        <div id="quartileRows">
                            <div class="data-row">
                                <input type="text" placeholder="Subject Area Category" class="q-category" value="Applied Psychology">
                                <select class="q-value">
                                    <option value="Q1">Q1</option>
                                    <option value="Q2">Q2</option>
                                    <option value="Q3" selected>Q3</option>
                                    <option value="Q4">Q4</option>
                                </select>
                                <button class="remove-row-btn" type="button" onclick="removeRow(this)"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="full-width hidden scimago-data-section" id="sjrSection">
                        <div class="scimago-section-header">
                            <span data-i18n="sjrHeader"><i data-lucide="trending-up"></i> SJR Progression</span>
                            <button class="add-row-btn" type="button" onclick="addSJRRow()"><i data-lucide="plus"></i> <span data-i18n="addRow">Add Row</span></button>
                        </div>
                        <div id="sjrRows">
                            <div class="data-row">
                                <input type="number" placeholder="Year" class="sjr-year" value="2025">
                                <input type="text" placeholder="SJR Value" class="sjr-value" value="0.452">
                                <button class="remove-row-btn" type="button" onclick="removeRow(this)"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="full-width hidden" id="indexingLinksGroup">
                        <label data-i18n="indexingHeader">Indexing Links</label>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="urlSinta">Sinta URL</label>
                                <input type="text" id="urlSinta" value="<?= esc($valUrl('sinta', 'https://sinta.kemdikbud.go.id/journals/profile/292')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlScopus">Scopus URL</label>
                                <input type="text" id="urlScopus" value="<?= esc($valUrl('scopus', 'https://www.scopus.com/sourceid/21101090010')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlScimago">Scimago URL</label>
                                <input type="text" id="urlScimago" value="<?= esc($valUrl('scimago', 'https://www.scimagojr.com/journalsearch.php?q=21101090010')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlDOAJ">DOAJ URL</label>
                                <input type="text" id="urlDOAJ" value="<?= esc($valUrl('doaj', 'https://doaj.org/toc/2527-7456')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlDimensions">Dimensions URL</label>
                                <input type="text" id="urlDimensions" value="<?= esc($valUrl('dimensions', '')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlGaruda">Garuda URL</label>
                                <input type="text" id="urlGaruda" value="<?= esc($valUrl('garuda', '')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlScholar">Google Scholar URL</label>
                                <input type="text" id="urlScholar" value="<?= esc($valUrl('scholar', '')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlScilit">Scilit URL</label>
                                <input type="text" id="urlScilit" value="<?= esc($valUrl('scilit', '')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="full-width hidden" id="toolsLinksGroup">
                        <label data-i18n="toolsHeader">Tools Links</label>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="urlTurnitin">Turnitin URL</label>
                                <input type="text" id="urlTurnitin" value="<?= esc($valUrl('turnitin', 'https://www.turnitin.id/')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlMendeley">Mendeley URL</label>
                                <input type="text" id="urlMendeley" value="<?= esc($valUrl('mendeley', 'https://www.mendeley.com/')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlGrammarly">Grammarly URL</label>
                                <input type="text" id="urlGrammarly" value="<?= esc($valUrl('grammarly', 'https://www.grammarly.com/')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlQuillbot">Quillbot URL</label>
                                <input type="text" id="urlQuillbot" value="<?= esc($valUrl('quillbot', 'https://quillbot.com/')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="full-width hidden scimago-data-section" id="templateLinksGroup">
                        <div class="scimago-section-header">
                            <span data-i18n="templateHeader"><i data-lucide="download"></i> Template Links</span>
                            <button class="add-row-btn" type="button" onclick="addTemplateRow()"><i data-lucide="plus"></i> <span data-i18n="addRow">Add Row</span></button>
                        </div>
                        <div id="templateRows">
                            <div class="data-row">
                                <select class="t-type">
                                    <option value="doc" selected>DOC</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <input type="text" placeholder="Label" class="t-label" value="Journal Template (DOC)">
                                <input type="text" placeholder="Download URL" class="t-url" value="">
                                <button class="remove-row-btn" type="button" onclick="removeRow(this)"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width" id="statcounterUrlGroup">
                        <label for="statcounterUrl" data-i18n="labelStatcounter">Statcounter Summary URL</label>
                        <input type="text" id="statcounterUrl" value="<?= esc($val('statcounter_url', '')) ?>">
                        <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block;" data-i18n="statcounterTip">Copy-paste URL from Statcounter</small>
                    </div>
                </div>

                <div class="form-grid" style="margin-top: 1.75rem;">
                    <div class="form-group" id="primaryColorGroup">
                        <label for="primaryColor" data-i18n="primaryColor">Primary Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" id="primaryColor" value="<?= esc($val('primary_color', '#0d9488')) ?>">
                            <span class="color-hex"><?= esc($val('primary_color', '#0d9488')) ?></span>
                        </div>
                    </div>

                    <div class="form-group" id="textColorGroup">
                        <label for="textColor" data-i18n="textColor">Text Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" id="textColor" value="<?= esc($val('text_color', '#ffffff')) ?>">
                            <span class="color-hex"><?= esc($val('text_color', '#ffffff')) ?></span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" id="generateBtn" style="flex: 1; min-width: 200px;" type="button">
                        <i data-lucide="refresh-cw"></i> <span data-i18n="btnUpdate">Update</span>
                    </button>
                    <button class="btn btn-secondary" id="saveBtn" style="flex: 1; min-width: 200px;" type="button">
                        <i data-lucide="save"></i> <span><?= $w ? 'Update Widget' : 'Simpan Widget' ?></span>
                    </button>
                </div>
            </section>

            <section class="glass-card" id="preview">
                <div class="card-header">
                    <div class="header-title">
                        <div class="header-icon-box">
                            <i data-lucide="eye"></i>
                        </div>
                        <h2 data-i18n="previewTitle">Live Preview</h2>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <div class="sidebar-sim-toggle" role="group" aria-label="Simulasi Tampilan Widget">
                            <button type="button" class="sim-btn active" id="simSidebarBtn" title="Simulasi Tampilan Sidebar OJS (~280px)">
                                <i data-lucide="columns-2"></i> <span>Sidebar OJS</span>
                            </button>
                            <button type="button" class="sim-btn" id="simFullBtn" title="Tampilan Lebar Bebas / Penuh">
                                <i data-lucide="maximize-2"></i> <span>Lebar Penuh</span>
                            </button>
                        </div>
                        <button class="copy-btn" id="fullPreviewBtn" type="button">
                            <i data-lucide="external-link"></i> <span data-i18n="btnFullPreview">Full Preview</span>
                        </button>
                        <div class="badge-status">
                            <span class="dot"></span> <span data-i18n="realtime">Real-time</span>
                        </div>
                    </div>
                </div>

                <div class="preview-workspace">
                    <div id="widgetPreview"></div>
                </div>

                <div class="preview-info">
                    <i data-lucide="info"></i>
                    <p data-i18n="previewInfo">This widget is responsive and will adjust to the width of your OJS sidebar.</p>
                </div>
            </section>

            <section class="glass-card" id="embed">
                <div class="card-header">
                    <div class="header-title">
                        <div class="header-icon-box">
                            <i data-lucide="code-2"></i>
                        </div>
                        <h2 data-i18n="embedTitle">Embed Code</h2>
                    </div>
                    <button class="copy-btn" id="copyBtn" type="button">
                        <i data-lucide="copy"></i> <span data-i18n="btnCopy">Copy Code</span>
                    </button>
                </div>

                <div class="code-container">
                    <div class="code-lang">
                        <span>HTML IFRAME</span>
                        <span style="font-size: 0.7rem; opacity: 0.8;">OJS Custom Block</span>
                    </div>
                    <div class="code-wrapper">
                        <pre id="codeSnippet"><code></code></pre>
                    </div>
                </div>

                <div class="usage-tip">
                    <p><strong data-i18n="howToUse">How to Use:</strong> <span data-i18n="usageInstructions">Copy the code above and paste it into the Custom Block Manager in your OJS.</span></p>
                </div>
            </section>
        </main>

        <footer>
            <p data-i18n="footerText">&copy; 2026 I-Widget. Designed for academic excellence.</p>
        </footer>
    </div>

    <div id="premiumModal" class="modal-overlay hidden">
        <div class="modal-content">
            <button class="modal-close" id="closeModal" type="button" aria-label="Close modal">
                <i data-lucide="x"></i>
            </button>

            <div class="modal-header">
                <div class="premium-icon-box">
                    <i data-lucide="crown"></i>
                </div>
                <h2 data-i18n="modalTitle">Upgrade to Premium</h2>
                <p data-i18n="modalSubtitle">Unlock advanced features and professional branding.</p>
            </div>

            <div class="comparison-grid">
                <div class="plan-card">
                    <div class="plan-header">
                        <h3 data-i18n="freePlan">Free</h3>
                        <div class="price">IDR 0<span data-i18n="perLifetime">/lifetime</span></div>
                        <div class="plan-sub">&nbsp;</div>
                    </div>
                    <ul class="plan-features">
                        <li><i data-lucide="check" class="check"></i> <span data-i18n="featFree1">Unlimited Widget Generation</span></li>
                        <li><i data-lucide="check" class="check"></i> <span data-i18n="featFree2">Statistics Table (Basic)</span></li>
                        <li><i data-lucide="check" class="check"></i> <span data-i18n="featFree3">Manual Data Updates</span></li>
                        <li><i data-lucide="x" class="cross"></i> <span data-i18n="featFree4">No Advanced Charts</span></li>
                        <li><i data-lucide="x" class="cross"></i> <span data-i18n="featFree5">"Powered by" Branding</span></li>
                    </ul>
                    <button class="btn btn-secondary plan-btn" disabled style="opacity: 0.65; cursor: default;" type="button" data-i18n="currentPlan">Current Plan</button>
                </div>

                <div class="plan-card">
                    <div class="plan-header">
                        <h3 data-i18n="premiumPlan">Premium</h3>
                        <div class="price">IDR 150k<span data-i18n="perYear">/year</span></div>
                        <div class="plan-sub" data-i18n="perJournal">1 Journal</div>
                    </div>
                    <ul class="plan-features">
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem1">Advanced Citation Charts</span></li>
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem3">Daily Automatic Data Sync</span></li>
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem5">Automatic updates</span></li>
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem4">Priority Support</span></li>
                    </ul>
                    <a href="<?= base_url('login') ?>" class="btn btn-secondary plan-btn" data-i18n="btnGetStarted">Get Started</a>
                </div>

                <div class="plan-card premium">
                    <div class="plan-badge" data-i18n="recommended">RECOMMENDED</div>
                    <div class="plan-header">
                        <h3 data-i18n="ultimatePlan">Ultimate</h3>
                        <div class="price">IDR 250k<span data-i18n="perYear">/year</span></div>
                        <div class="plan-sub" data-i18n="perJournal">1 Journal</div>
                    </div>
                    <ul class="plan-features">
                        <li><i data-lucide="zap" class="star"></i> <strong data-i18n="featUlt1">Everything in Premium</strong></li>
                        <li><i data-lucide="zap" class="star"></i> <span data-i18n="featUlt2">Whitelabel Configuration</span></li>
                        <li><i data-lucide="zap" class="star"></i> <span data-i18n="featUlt3">Custom Branding</span></li>
                    </ul>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary plan-btn" data-i18n="btnGetStarted">Get Started Now</a>
                </div>
            </div>

            <div class="modal-footer">
                <p data-i18n="modalFooter">Already have a premium account? <a href="<?= base_url('login') ?>" style="color: var(--primary); font-weight: 600;">Login here</a></p>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>