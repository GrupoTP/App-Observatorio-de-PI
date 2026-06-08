<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var array<string, mixed> $project */
$conceito = $project['conceito'] ?? null;
?>
<article class="app-portfolio-card">
    <div class="app-portfolio-card__icon" aria-hidden="true">
        <?= lucide_tag('award', 'app-portfolio-card__icon-svg') ?>
    </div>
    <div class="app-portfolio-card__body">
        <h2 class="app-portfolio-card__title"><?= e($project['titulo'] ?? '') ?></h2>

        <div class="app-portfolio-card__meta">
            <?php if ($conceito !== null): ?>
                <span class="app-portfolio-card__conceito-label">Conceito:</span>
                <?php require dirname(__DIR__) . '/partials/app-conceito-badge.php'; ?>
            <?php endif; ?>
            <span class="app-portfolio-card__approved">
                <span class="app-portfolio-card__approved-dot" aria-hidden="true"></span>
                Aprovado
            </span>
        </div>

        <p class="app-portfolio-card__description"><?= e($project['descricao'] ?? '') ?></p>

        <div class="app-portfolio-card__tags">
            <?php if (!empty($project['nome_curso'])): ?>
                <span class="app-portfolio-tag app-portfolio-tag--course"><?= e($project['nome_curso']) ?></span>
            <?php endif; ?>
            <?php if (!empty($project['modulo'])): ?>
                <span class="app-portfolio-tag app-portfolio-tag--module"><?= e($project['modulo']) ?></span>
            <?php endif; ?>
        </div>

        <a href="/projetos/<?= e($project['id_projeto']) ?>"
           class="app-action-btn app-action-btn--secondary app-portfolio-card__action">
            <?= lucide_tag('external-link', 'app-action-btn__icon') ?>
            Ver projeto
        </a>
    </div>
</article>
