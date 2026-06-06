<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var array<string, mixed> $project */
$status = projeto_status_meta((string) ($project['situacao_projeto'] ?? ''));
$grade = $project['grade'] ?? null;
$isEvaluated = ($project['situacao_projeto'] ?? '') === 'avaliado' && $grade !== null;
$conceito = $isEvaluated ? nota_para_conceito((float) $grade) : null;
?>
<article class="app-project-card app-project-card--<?= e($status['modifier']) ?>"
         data-project-card
         data-project-id="<?= e($project['id_projeto']) ?>"
         data-project-title="<?= e($project['titulo'] ?? '') ?>"
         data-project-turma="<?= e($project['turma_label'] ?? '') ?>"
         data-project-descricao="<?= e($project['descricao'] ?? '') ?>"
         data-project-tecnologias="<?= e($project['tecnologias'] ?? '') ?>"
         data-project-repo="<?= e($project['link_repo_git'] ?? '') ?>"
         data-project-submitted="<?= e(format_date($project['submitted_at'] ?? null)) ?>"
         data-project-prazo="<?= e(format_date($project['prazo_especial'] ?? null)) ?>">
    <div class="app-project-card__header">
        <?= lucide_tag('folder-open', 'app-project-card__folder-icon') ?>
        <h3 class="app-project-card__title"><?= e($project['titulo'] ?? '') ?></h3>
    </div>

    <div class="app-project-card__body">
        <p class="app-project-card__meta">
            <span class="app-project-card__meta-label">Turma:</span>
            <?= e($project['turma_label'] ?? '—') ?>
        </p>

        <div class="app-project-status app-project-status--<?= e($status['modifier']) ?>">
            <div class="app-project-status__heading">
                <?= lucide_tag($status['icon'], 'app-project-status__icon') ?>
                <span class="app-project-status__label"><?= e($status['label']) ?></span>
            </div>
            <?php if ($status['description'] !== ''): ?>
                <p class="app-project-status__description"><?= e($status['description']) ?></p>
            <?php endif; ?>
        </div>

        <?php if ($isEvaluated && $conceito !== null): ?>
            <div class="app-project-grade">
                <div class="app-project-grade__conceito-row">
                    <span class="app-project-grade__label">Conceito final:</span>
                    <?php require dirname(__DIR__) . '/partials/app-conceito-badge.php'; ?>
                </div>
            </div>
        <?php endif; ?>

        <p class="app-project-card__meta">
            <span class="app-project-card__meta-label">Data envio:</span>
            <?= e(format_date($project['submitted_at'] ?? null)) ?>
        </p>
        <p class="app-project-card__meta">
            <span class="app-project-card__meta-label">Prazo:</span>
            <?= e(format_date($project['prazo_especial'] ?? null)) ?>
        </p>
    </div>

    <div class="app-project-card__actions">
        <button type="button" class="app-project-card__btn app-project-card__btn--view" data-project-view>
            <?= lucide_tag('eye', 'app-project-card__btn-icon') ?>
            <span>Ver</span>
        </button>
        <a href="/projetos/<?= e($project['id_projeto']) ?>/editar"
           class="app-project-card__btn app-project-card__btn--edit">
            <?= lucide_tag('pencil', 'app-project-card__btn-icon') ?>
            <span>Editar</span>
        </a>
        <button type="button" class="app-project-card__btn app-project-card__btn--delete"
                data-project-delete aria-label="Remover <?= e($project['titulo'] ?? 'projeto') ?>">
            <?= lucide_tag('trash-2', 'app-project-card__btn-icon') ?>
        </button>
    </div>
</article>
