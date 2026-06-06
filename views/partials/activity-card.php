<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var array<string, mixed> $project */
$status = (string) ($project['situacao_projeto'] ?? '');
$isEvaluated = $status === 'avaliado';
$grade = $project['grade'] ?? null;
?>
<article class="activity-card">
    <div class="activity-card__row">
        <div class="activity-card__body">
            <h3 class="activity-card__title"><?= e($project['titulo'] ?? '') ?></h3>
            <p class="activity-card__meta">
                enviado em <?= e(format_date($project['submitted_at'] ?? null)) ?>
            </p>
        </div>
        <div class="activity-card__status">
            <?php if ($isEvaluated && $grade !== null): ?>
                <span class="activity-status-badge activity-status-badge--evaluated">
                    <?= lucide_tag('circle-check-big', 'activity-status-badge__icon') ?>
                    Avaliado - Nota <?= e((string) $grade) ?>
                </span>
            <?php else: ?>
                <span class="activity-status-badge activity-status-badge--pending">
                    <?= lucide_tag('clock', 'activity-status-badge__icon') ?>
                    Enviado - Aguardando
                </span>
            <?php endif; ?>
        </div>
    </div>
</article>
