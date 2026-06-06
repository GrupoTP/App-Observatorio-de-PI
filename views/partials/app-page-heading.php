<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var string $headingTitle */
/** @var string $headingSubtitle */
/** @var string|null $backUrl */
$backUrl = $backUrl ?? '/dashboard';
?>
<div class="app-page-heading">
    <a href="<?= e($backUrl) ?>" class="app-page-heading__back" aria-label="Voltar para Início">
        <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
    </a>
    <div>
        <h1 class="app-page-heading__title"><?= e($headingTitle) ?></h1>
        <p class="app-page-heading__subtitle"><?= e($headingSubtitle) ?></p>
    </div>
</div>
