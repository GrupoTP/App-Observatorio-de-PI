<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Olá, ' . ($userName ?? '') . '!';
    $subtitle = 'Bem-vindo ao Observatório de Projetos Integradores';
    $backUrl = null;
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stats-card yellow h-100">
                <div class="card-body">
                    <i class="bi bi-file-earmark-text fs-4 mb-2"></i>
                    <div class="display-6 fw-bold"><?= (int) $sentCount ?></div>
                    <div>Projetos enviados</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card green h-100">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-4 mb-2"></i>
                    <div class="display-6 fw-bold"><?= (int) $evaluatedCount ?></div>
                    <div>Projetos avaliados</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card orange h-100">
                <div class="card-body">
                    <i class="bi bi-clock fs-4 mb-2"></i>
                    <div class="display-6 fw-bold"><?= (int) $upcomingCount ?></div>
                    <div>Prazo próximo</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row gap-2 mb-4">
        <a href="/submeter" class="btn btn-senac-primary flex-fill"><i class="bi bi-plus-circle me-2"></i>Submeter novo projeto</a>
        <a href="/portfolio" class="btn btn-senac-secondary flex-fill"><i class="bi bi-briefcase me-2"></i>Ver meu portfólio</a>
    </div>

    <h2 class="h5 fw-bold mb-3">Atividades recentes</h2>
    <?php if ($recentProjects === []): ?>
        <p class="text-muted">Nenhum projeto enviado ainda.</p>
    <?php else: ?>
        <?php foreach ($recentProjects as $project): ?>
            <?php $showActions = false; require dirname(__DIR__) . '/partials/project-card.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
