<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var string $title */
/** @var string|null $subtitle */
/** @var string|null $backUrl */
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <?php if (!empty($backUrl)): ?>
        <a href="<?= e($backUrl) ?>" class="btn btn-light border" aria-label="Voltar">
            <i class="bi bi-arrow-left text-senac-blue"></i>
        </a>
    <?php endif; ?>
    <div>
        <h1 class="h3 fw-bold mb-1"><?= e($title) ?></h1>
        <?php if (!empty($subtitle)): ?>
            <p class="text-muted mb-0"><?= e($subtitle) ?></p>
        <?php endif; ?>
    </div>
</div>
