<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Gerenciar PI';
    $subtitle = 'Grupos e projetos integradores';
    $backUrl = '/admin/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="/admin/pi/novo" class="btn btn-senac-primary"><i class="bi bi-plus-lg me-1"></i> Novo grupo</a>
        <a href="/admin/pi/rubrica" class="btn btn-senac-outline"><i class="bi bi-gear me-1"></i> Configurar rubrica</a>
    </div>

    <form method="get" class="card mb-4"><div class="card-body row g-2">
        <div class="col-md-4"><input type="search" name="q" class="form-control" value="<?= e($search) ?>" placeholder="Buscar"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="todos">Status</option>
                <option value="em-andamento" <?= $status === 'em-andamento' ? 'selected' : '' ?>>Em andamento</option>
                <option value="enviado" <?= $status === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="avaliado" <?= $status === 'avaliado' ? 'selected' : '' ?>>Avaliado</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="course" class="form-select">
                <?php foreach ($courses as $c): ?>
                    <option value="<?= e($c) ?>" <?= $course === $c ? 'selected' : '' ?>><?= e($c === 'todos' ? 'Todos os cursos' : $c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-senac-secondary w-100">Filtrar</button></div>
    </div></form>

    <?php foreach ($groups as $g): ?>
        <div class="card project-card mb-3">
            <div class="card-body d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <h2 class="h6 fw-bold"><?= e($g['nome_grupo'] ?? $g['titulo']) ?></h2>
                    <p class="small text-muted mb-1"><?= e($g['titulo']) ?> — <?= e($g['nome_curso']) ?></p>
                    <span class="badge <?= situacao_badge_class($g['situacao_projeto']) ?>"><?= e(situacao_label($g['situacao_projeto'])) ?></span>
                </div>
                <div class="d-flex gap-2 align-self-center flex-wrap">
                    <a href="/admin/pi/<?= e($g['id_projeto']) ?>" class="btn btn-sm btn-senac-outline">Detalhes</a>
                    <a href="/admin/pi/<?= e($g['id_projeto']) ?>/editar" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <?php if ($g['situacao_projeto'] !== 'avaliado'): ?>
                        <a href="/admin/pi/<?= e($g['id_projeto']) ?>/avaliar" class="btn btn-sm btn-senac-primary">Avaliar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
