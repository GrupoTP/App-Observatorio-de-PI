<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Meus Projetos';
    $subtitle = 'Gerencie todos os seus projetos integradores';
    $backUrl = '/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Buscar</label>
                    <input type="search" name="q" class="form-control" value="<?= e($search) ?>" placeholder="Título do projeto">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="todos" <?= $status === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="enviado" <?= $status === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                        <option value="em-correcao" <?= $status === 'em-correcao' ? 'selected' : '' ?>>Em correção</option>
                        <option value="avaliado" <?= $status === 'avaliado' ? 'selected' : '' ?>>Avaliado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-senac-secondary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($projects === []): ?>
        <div class="alert alert-info">Nenhum projeto encontrado. <a href="/submeter">Submeter novo projeto</a></div>
    <?php else: ?>
        <?php foreach ($projects as $project): ?>
            <?php $showActions = true; require dirname(__DIR__) . '/partials/project-card.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
