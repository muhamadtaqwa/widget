<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= lang('Errors.whoops') ?></title>

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
</head>
<body>

    <div class="container text-center">

        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>

        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>

        <?php if (!empty($message)): ?>
            <div style="margin-top: 1.5rem; padding: 1rem; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; color: #991b1b; font-family: monospace; font-size: 13px; text-align: left; max-width: 650px; margin-left: auto; margin-right: auto; word-break: break-all;">
                <strong>Detail Error:</strong> <?= esc($message) ?><br>
                <small style="color: #b91c1c;">File: <?= esc($file ?? '') ?> (Line: <?= esc($line ?? '') ?>)</small>
            </div>
        <?php endif; ?>

    </div>

</body>

</html>
