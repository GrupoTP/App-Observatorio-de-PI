<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Gerenciar Projetos';
    $subtitle = 'Todos os projetos submetidos';
    $backUrl = '/admin/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <form method="get" class="card mb-4"><div class="card-body row g-3 align-items-end">
        <div class="col-md-5"><input type="search" name="q" class="form-control" value="<?= e($search) ?>" placeholder="Buscar"></div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="todos">Todos</option>
                <option value="enviado" <?= $status === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="em-correcao" <?= $status === 'em-correcao' ? 'selected' : '' ?>>Em correção</option>
                <option value="avaliado" <?= $status === 'avaliado' ? 'selected' : '' ?>>Avaliado</option>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-senac-secondary w-100">Filtrar</button></div>
    </div></form>

    <?php foreach ($projects as $project): ?>
        <div class="card project-card mb-3">
            <div class="card-body d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <h2 class="h6 fw-bold"><?= e($project['titulo']) ?></h2>
                    <p class="small mb-1"><?= e(trim(($project['nome_civil_nome'] ?? '') . ' ' . ($project['nome_civil_sobrenome'] ?? ''))) ?></p>
                    <span class="badge <?= situacao_badge_class($project['situacao_projeto']) ?>"><?= e(situacao_label($project['situacao_projeto'])) ?></span>
                </div>
                <?php if ($project['situacao_projeto'] !== 'avaliado'): ?>
                    <a href="/admin/projetos/<?= e($project['id_projeto']) ?>/avaliar" class="btn btn-senac-primary align-self-center">Avaliar</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
