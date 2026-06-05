<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Avaliar Projeto';
    $subtitle = $project['titulo'];
    $backUrl = '/admin/projetos';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <form method="post" action="/admin/projetos/<?= e($project['id_projeto']) ?>/avaliar">
        <?= csrf_field() ?>
        <div class="card mb-4">
            <div class="card-body">
                <p><?= e($project['descricao']) ?></p>
                <?php foreach ($criteria as $c): ?>
                    <div class="mb-3">
                        <label class="form-label"><?= e($c['nome']) ?> (0–10)</label>
                        <input type="number" name="criterio_<?= e($c['nome']) ?>" class="form-control" min="0" max="10" step="0.5" required>
                    </div>
                <?php endforeach; ?>
                <div class="mb-3">
                    <label class="form-label">Comentário</label>
                    <textarea name="descricao" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-senac-primary">Registrar avaliação</button>
            </div>
        </div>
    </form>
</div>
