<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, ['en', 'id'])) {
    $lang = 'en';
}

$seo = [
    'en' => [
        'title' => 'I-Widget | Premium OJS Journal Statistics Widget Generator',
        'desc' => 'I-Widget is a premium journal statistics widget generator for Open Journal Systems (OJS). Features statistics tables, OpenAlex citation charts, and custom Scimago widgets.',
        'keywords' => 'i-widget, journal widget, journal statistics, ojs widget, journal stats generator, openalex widget, custom scimago, sinta accreditation, scopus citation, google scholar citation'
    ],
    'id' => [
        'title' => 'I-Widget | Generator Widget Statistik Jurnal OJS Premium',
        'desc' => 'I-Widget adalah tool generator widget statistik jurnal premium untuk Open Journal Systems (OJS). Dilengkapi dengan tabel statistik, grafik sitasi OpenAlex, dan widget Scimago kustom.',
        'keywords' => 'i-widget, widget jurnal, statistik jurnal, ojs widget, generator statistik jurnal, openalex widget, scimago kustom, akreditasi sinta, sitasi scopus, sitasi google scholar'
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

    <title><?php echo htmlspecialchars($currentSeo['title']); ?></title>
    <meta name="title" content="<?php echo htmlspecialchars($currentSeo['title']); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($currentSeo['desc']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($currentSeo['keywords']); ?>">
    <meta name="author" content="I-Widget">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= base_url($lang === 'en' ? '' : 'id') ?>">
    <link rel="alternate" hreflang="en" href="<?= base_url() ?>">
    <link rel="alternate" hreflang="id" href="<?= base_url('id') ?>">
    <link rel="alternate" hreflang="x-default" href="<?= base_url() ?>">

    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($currentSeo['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($currentSeo['desc']); ?>">
    <meta property="og:image" content="<?= base_url('media/img/thumbnail.webp') ?>">
    <meta property="og:locale" content="<?php echo $lang === 'id' ? 'id_ID' : 'en_US'; ?>">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= current_url() ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($currentSeo['title']); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($currentSeo['desc']); ?>">
    <meta property="twitter:image" content="<?= base_url('media/img/thumbnail.webp') ?>">

    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('media/img/favicon/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('media/img/favicon/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('media/img/favicon/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= base_url('media/img/favicon/site.webmanifest') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window.APP_LANG = "<?php echo $lang; ?>";
    </script>
</head>

<body>
    <div class="glow-bg"></div>
    <div class="container">
        <header>
            <div class="header-top">
                <div class="logo-wrapper">
                    <i data-lucide="blocks" style="width: 48px; height: 48px; color: var(--primary);"></i>
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-left: 0.5rem;">I-Widget</span>
                </div>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <button id="premiumTrigger" style="background: rgba(13, 148, 136, 0.1); color: #0d9488; border: 1px solid rgba(13, 148, 136, 0.2); padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; transition: all 0.2s; cursor: pointer;">
                        <i data-lucide="crown" style="width: 16px; height: 16px;"></i> Premium
                    </button>
                    <div class="lang-switcher">
                        <a href="<?= base_url() ?>" class="lang-link <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
                        <span class="divider"></span>
                        <a href="<?= base_url('id') ?>" class="lang-link <?php echo $lang === 'id' ? 'active' : ''; ?>">ID</a>
                    </div>
                </div>
            </div>
            <p class="subtitle" data-i18n="subtitle">Premium Journal Widget Implementation & Generator</p>
        </header>

        <main class="main-content">
            <section class="section-card glass-card" id="konfigurasi">
                <div class="card-header">
                    <div class="header-title">
                        <i data-lucide="settings-2"></i>
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
                            <span data-i18n="quartilesHeader"><i data-lucide="layers"></i> Quartiles — Subject Area & Quartile</span>
                            <button class="add-row-btn" type="button" onclick="addQuartileRow()"><i data-lucide="plus"></i> <span data-i18n="addRow">Add Row</span></button>
                        </div>
                        <div id="quartileRows">
                            <div class="data-row">
                                <input type="text" placeholder="Subject Area Category" class="q-category" value="Applied Psychology" aria-label="Subject Area Category">
                                <select class="q-value" aria-label="Quartile">
                                    <option value="Q1">Q1</option>
                                    <option value="Q2">Q2</option>
                                    <option value="Q3" selected>Q3</option>
                                    <option value="Q4">Q4</option>
                                </select>
                                <button class="remove-row-btn" type="button" onclick="removeRow(this)" aria-label="Remove row"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="full-width hidden scimago-data-section" id="sjrSection">
                        <div class="scimago-section-header">
                            <span data-i18n="sjrHeader"><i data-lucide="trending-up"></i> SJR Progression — Year & SJR Value</span>
                            <button class="add-row-btn" type="button" onclick="addSJRRow()"><i data-lucide="plus"></i> <span data-i18n="addRow">Add Row</span></button>
                        </div>
                        <div id="sjrRows">
                            <div class="data-row">
                                <input type="number" placeholder="Year e.g. 2025" class="sjr-year" value="2025" aria-label="Year">
                                <input type="text" placeholder="SJR e.g. 0.452" class="sjr-value" value="0.452" aria-label="SJR Value">
                                <button class="remove-row-btn" type="button" onclick="removeRow(this)" aria-label="Remove row"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="full-width hidden" id="indexingLinksGroup">
                        <label data-i18n="indexingHeader">Indexing & Abstracting Links</label>
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
                                <input type="text" id="urlDimensions" value="<?= esc($valUrl('dimensions', 'https://app.dimensions.ai/discover/publication?and_facet_source_title=jour.1292855')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlGaruda">Garuda URL</label>
                                <input type="text" id="urlGaruda" value="<?= esc($valUrl('garuda', 'https://garuda.kemdikbud.go.id/journal/view/8645')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlScholar">Google Scholar URL</label>
                                <input type="text" id="urlScholar" value="<?= esc($valUrl('scholar', 'https://scholar.google.com/citations?user=cvLFxv0AAAAJ')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="urlScilit">Scilit URL</label>
                                <input type="text" id="urlScilit" value="<?= esc($valUrl('scilit', 'https://www.scilit.com/sources/81175')) ?>">
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
                            <span data-i18n="templateHeader"><i data-lucide="download"></i> Template Download Links</span>
                            <button class="add-row-btn" type="button" onclick="addTemplateRow()"><i data-lucide="plus"></i> <span data-i18n="addRow">Add Row</span></button>
                        </div>
                        <div id="templateRows">
                            <div class="data-row">
                                <select class="t-type" aria-label="Type">
                                    <option value="doc" selected>DOC</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <input type="text" placeholder="Label" class="t-label" value="Journal Template (DOC)" aria-label="Label">
                                <input type="text" placeholder="Download URL" class="t-url" value="" aria-label="URL">
                                <button class="remove-row-btn" type="button" onclick="removeRow(this)" aria-label="Remove row"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width" id="statcounterUrlGroup">
                        <label for="statcounterUrl" data-i18n="labelStatcounter">Statcounter Summary URL <span style="font-weight:400;color:#94a3b8;">(Optional)</span></label>
                        <input type="text" id="statcounterUrl" value="<?= esc($val('statcounter_url', '')) ?>">
                        <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block;" data-i18n="statcounterTip">Copy-paste URL from your Statcounter Summary Stats page</small>
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

                <div class="card-footer" style="display: flex; gap: 1rem;">
                    <button class="btn btn-primary" id="generateBtn" style="flex: 1;">
                        <i data-lucide="refresh-cw"></i> <span data-i18n="btnUpdate">Update</span>
                    </button>
                    <button class="btn btn-primary" id="saveBtn" style="flex: 1; background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%); color: var(--text-main); box-shadow: var(--shadow-gold);">
                        <i data-lucide="save"></i> <span><?= $w ? 'Update Widget' : 'Simpan Widget' ?></span>
                    </button>
                </div>
            </section>

            <section class="section-card glass-card" id="preview">
                <div class="card-header">
                    <div class="header-title">
                        <i data-lucide="eye"></i>
                        <h2 data-i18n="previewTitle">Live Preview</h2>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button class="copy-btn" id="fullPreviewBtn" style="background: rgba(13, 148, 136, 0.1); color: #0d9488; border: 1px solid rgba(13, 148, 136, 0.2);">
                            <i data-lucide="external-link"></i> <span data-i18n="btnFullPreview">Full Preview</span>
                        </button>
                        <div class="badge-status">
                            <span class="dot"></span> <span data-i18n="realtime">Real-time</span>
                        </div>
                    </div>
                </div>

                <div class="preview-workspace" id="previewWorkspace">
                    <div id="widgetPreview"></div>
                </div>

                <div class="preview-info">
                    <i data-lucide="info"></i>
                    <p data-i18n="previewInfo">This widget is responsive and will adjust to the width of your OJS sidebar container.</p>
                </div>
            </section>

            <section class="section-card glass-card" id="embed">
                <div class="card-header">
                    <div class="header-title">
                        <i data-lucide="code-2"></i>
                        <h2 data-i18n="embedTitle">Embed Code</h2>
                    </div>
                    <button class="copy-btn" id="copyBtn">
                        <i data-lucide="copy"></i> <span data-i18n="btnCopy">Copy Code</span>
                    </button>
                </div>

                <div class="code-container">
                    <div class="code-lang">HTML IFRAME</div>
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
        <div class="modal-content glass-card">
            <button class="modal-close" id="closeModal">
                <i data-lucide="x"></i>
            </button>

            <div class="modal-header">
                <i data-lucide="zap" class="premium-icon"></i>
                <h2 data-i18n="modalTitle">Upgrade to Premium</h2>
                <p data-i18n="modalSubtitle">Unlock advanced features and professional branding for your journal.</p>
            </div>

            <div class="comparison-grid">
                <div class="plan-card">
                    <div class="plan-header">
                        <h3 data-i18n="freePlan">Free</h3>
                        <div class="price">IDR 0<span data-i18n="perLifetime">/lifetime</span></div>
                    </div>
                    <ul class="plan-features">
                        <li><i data-lucide="check" class="check"></i> <span data-i18n="featFree1">Unlimited Widget Generation</span></li>
                        <li><i data-lucide="check" class="check"></i> <span data-i18n="featFree2">Statistics Table (Basic)</span></li>
                        <li><i data-lucide="check" class="check"></i> <span data-i18n="featFree3">Manual Data Updates</span></li>
                        <li><i data-lucide="x" class="cross"></i> <span data-i18n="featFree4">No Advanced Charts</span></li>
                        <li><i data-lucide="x" class="cross"></i> <span data-i18n="featFree5">"Powered by" Branding</span></li>
                    </ul>
                </div>

                <div class="plan-card">
                    <div class="plan-header">
                        <h3 data-i18n="premiumPlan">Premium</h3>
                        <div class="price">IDR 150k<span data-i18n="perYear">/year</span></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: -10px; margin-bottom: 15px;" data-i18n="perJournal">1 Journal</div>
                    </div>
                    <ul class="plan-features">
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem1">Advanced Citation Charts</span></li>
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem3">Daily Automatic Data Sync</span></li>
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem5">Automatic updates via dashboard</span></li>
                        <li><i data-lucide="sparkles" class="star"></i> <span data-i18n="featPrem4">Priority Support</span></li>
                    </ul>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); box-shadow: none; font-size: 0.9rem; padding: 0.75rem;" data-i18n="btnGetStarted">Get Started</a>
                </div>

                <div class="plan-card premium">
                    <div class="plan-badge" data-i18n="recommended">RECOMMENDED</div>
                    <div class="plan-header">
                        <h3 data-i18n="ultimatePlan">Ultimate</h3>
                        <div class="price">IDR 250k<span data-i18n="perYear">/year</span></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: -10px; margin-bottom: 15px;" data-i18n="perJournal">1 Journal</div>
                    </div>
                    <ul class="plan-features">
                        <li><i data-lucide="zap" class="star" style="color: #fbbf24;"></i> <strong data-i18n="featUlt1">Everything in Premium</strong></li>
                        <li><i data-lucide="zap" class="star" style="color: #fbbf24;"></i> <span data-i18n="featUlt2">Whitelabel Configuration</span></li>
                    </ul>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary" data-i18n="btnGetStarted">Get Started Now</a>
                </div>
            </div>

            <div class="modal-footer">
                <p data-i18n="modalFooter">Already have a premium account? <a href="<?= base_url('login') ?>">Login here</a></p>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>