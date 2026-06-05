<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Avaliar PI';
    $subtitle = $project['nome_grupo'] ?? $project['titulo'];
    $backUrl = '/admin/pi/' . $project['id_projeto'];
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <form method="post" action="/admin/pi/<?= e($project['id_projeto']) ?>/avaliar">
        <?= csrf_field() ?>
        <div class="card"><div class="card-body">
            <?php foreach ($criteria as $c): ?>
                <div class="mb-3">
                    <label class="form-label"><?= e($c['nome']) ?> (0–10)</label>
                    <input type="number" name="criterio_<?= e($c['nome']) ?>" class="form-control" min="0" max="10" step="0.5" required>
                </div>
            <?php endforeach; ?>
            <div class="mb-3">
                <label class="form-label">Comentário da avaliação</label>
                <textarea name="descricao" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-senac-primary">Salvar avaliação do PI</button>
        </div></div>
    </form>
</div>
