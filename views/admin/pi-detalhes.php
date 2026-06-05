<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = $project['nome_grupo'] ?? $project['titulo'];
    $subtitle = $project['nome_curso'] . ' — ' . $project['modulo'];
    $backUrl = '/admin/pi';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="card mb-4"><div class="card-body">
        <h2 class="h6 fw-bold"><?= e($project['titulo']) ?></h2>
        <p><?= e($project['descricao']) ?></p>
        <p class="small"><strong>Submissor:</strong> <?= e(user_display_name($submitter)) ?></p>
        <?php if ($members !== []): ?>
            <p class="small"><strong>Coautores:</strong>
                <?= e(implode(', ', array_map('user_display_name', $members))) ?>
            </p>
        <?php endif; ?>
        <span class="badge <?= situacao_badge_class($project['situacao_projeto']) ?>"><?= e(situacao_label($project['situacao_projeto'])) ?></span>
    </div></div>

    <?php if ($feedback): ?>
        <div class="alert alert-success">Projeto já avaliado em <?= e(format_datetime($feedback['data'])) ?></div>
    <?php else: ?>
        <a href="/admin/pi/<?= e($project['id_projeto']) ?>/avaliar" class="btn btn-senac-primary">Avaliar PI</a>
    <?php endif; ?>
</div>
