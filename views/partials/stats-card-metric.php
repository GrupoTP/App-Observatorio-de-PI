<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var int|string $value */
/** @var string $label */
/** @var string $color blue|yellow|orange|green */
/** @var string $lucideIcon Lucide icon name */
?>
<div class="stats-metric-card">
    <div class="stats-metric-card__row">
        <div class="stats-metric-card__icon stats-metric-card__icon--<?= e($color) ?>">
            <?= lucide_tag($lucideIcon, 'stats-metric-card__lucide') ?>
        </div>
        <div class="stats-metric-card__value stats-metric-card__value--<?= e($color) ?>">
            <?= e((string) $value) ?>
        </div>
    </div>
    <p class="stats-metric-card__label"><?= e($label) ?></p>
</div>
