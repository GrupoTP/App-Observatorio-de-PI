<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = $pageTitle;
    $subtitle = 'Visão geral do observatório';
    $backUrl = null;
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stats-card yellow"><div class="card-body text-center">
                <div class="display-6 fw-bold"><?= (int) $totalProjects ?></div><div>Projetos</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stats-card orange"><div class="card-body text-center">
                <div class="display-6 fw-bold"><?= (int) $pending ?></div><div>Pendentes</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stats-card green"><div class="card-body text-center">
                <div class="display-6 fw-bold"><?= (int) $evaluated ?></div><div>Avaliados</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stats-card yellow"><div class="card-body text-center">
                <div class="display-6 fw-bold"><?= (int) $totalStudents ?></div><div>Alunos</div>
            </div></div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php if (\App\Auth\SessionAuth::isAdmin()): ?>
            <a href="/admin/projetos" class="btn btn-senac-secondary">Gerenciar Projetos</a>
            <a href="/admin/alunos/novo" class="btn btn-senac-outline">Cadastrar Usuário</a>
        <?php endif; ?>
        <a href="/admin/pi" class="btn btn-senac-primary">Gerenciar PI</a>
        <a href="/admin/alunos" class="btn btn-senac-outline">Gerenciar Alunos</a>
    </div>

    <h2 class="h5 fw-bold mb-3">Projetos recentes</h2>
    <?php foreach ($recent as $project): ?>
        <?php $showActions = false; require dirname(__DIR__) . '/partials/project-card.php'; ?>
    <?php endforeach; ?>
</div>
