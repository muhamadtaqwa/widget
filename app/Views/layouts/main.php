<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'I-Widget' ?></title>
    <meta name="description" content="<?= $description ?? 'I-Widget' ?>">

    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg?v=2') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('media/img/favicon/apple-touch-icon.png?v=2') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('media/img/favicon/favicon-32x32.png?v=2') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('media/img/favicon/favicon-16x16.png?v=2') ?>">
    <link rel="manifest" href="<?= base_url('media/img/favicon/site.webmanifest?v=2') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico?v=2') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=5') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <div class="glow-bg"></div>

    <?= $this->renderSection('content') ?>

    <script>
        lucide.createIcons();
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>