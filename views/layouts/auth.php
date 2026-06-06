<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<!DOCTYPE html>
<html lang="pt-BR" autocomplete="off">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? config('app.name')) ?> — <?= e(config('app.name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= e(asset_version('assets/css/app.css')) ?>">
</head>
<body class="auth-body">
<?= $content ?? '' ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.487.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
<script src="/assets/js/app.js?v=<?= e(asset_version('assets/js/app.js')) ?>"></script>
</body>
</html>
