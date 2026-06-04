<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($appName ?? config('app.name')) ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="/"><?= e(config('app.name')) ?></a>
        </div>
    </nav>

    <main class="container flex-grow-1 py-4">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-light border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            Faculdade Senac Recife — Observatório de Projetos Integradores
        </div>
    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"
    ></script>
</body>
</html>
