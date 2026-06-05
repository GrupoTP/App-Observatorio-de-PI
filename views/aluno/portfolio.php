<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Meu Portfólio Profissional';
    $subtitle = 'Projetos aprovados e públicos de ' . ($userName ?? '');
    $backUrl = '/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="d-flex gap-2 mb-4">
        <a href="/curriculo" class="btn btn-senac-primary"><i class="bi bi-file-person me-2"></i>Gerar currículo</a>
    </div>

    <?php if ($projects === []): ?>
        <div class="alert alert-senac-warning">Nenhum projeto público avaliado ainda.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6">
                    <div class="card project-card h-100">
                        <div class="card-body">
                            <h2 class="h6 fw-bold"><?= e($project['titulo']) ?></h2>
                            <p class="small text-muted"><?= e($project['nome_curso']) ?> — <?= e($project['modulo']) ?></p>
                            <p class="small"><?= e(mb_strimwidth($project['descricao'], 0, 120, '…')) ?></p>
                            <?php if (!empty($project['link_github'])): ?>
                                <a href="<?= e($project['link_github']) ?>" target="_blank" rel="noopener" class="small">Ver no GitHub</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
