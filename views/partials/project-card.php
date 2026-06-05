<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var array<string, mixed> $project */
/** @var bool $showActions */
$showActions = $showActions ?? true;
?>
<div class="card project-card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
            <div>
                <h2 class="h6 fw-bold mb-1"><?= e($project['titulo']) ?></h2>
                <?php if (!empty($project['nome_grupo'])): ?>
                    <small class="text-muted"><?= e($project['nome_grupo']) ?></small>
                <?php endif; ?>
                <p class="small text-muted mb-2"><?= e($project['nome_curso'] ?? '') ?> — <?= e($project['modulo'] ?? '') ?></p>
                <span class="badge badge-status <?= situacao_badge_class($project['situacao_projeto']) ?>">
                    <?= e(situacao_label($project['situacao_projeto'])) ?>
                </span>
            </div>
            <div class="text-end small text-muted">
                Prazo: <?= e(format_date($project['prazo_especial'] ?? null)) ?>
            </div>
        </div>
        <?php if ($showActions && \App\Auth\SessionAuth::isAluno()): ?>
        <div class="d-flex gap-2 mt-3 flex-wrap">
            <a href="/projetos/<?= e($project['id_projeto']) ?>/editar" class="btn btn-sm btn-senac-outline">Editar</a>
            <form method="post" action="/projetos/<?= e($project['id_projeto']) ?>/excluir" class="d-inline"
                  onsubmit="return confirm('Confirma exclusão deste projeto?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
