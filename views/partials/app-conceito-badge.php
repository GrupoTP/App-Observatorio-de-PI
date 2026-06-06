<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var array{code: string, label: string, modifier: string}|null $conceito */
$conceito = $conceito ?? null;
if ($conceito === null) {
    return;
}
?>
<span class="app-mencao-badge app-mencao-badge--<?= e($conceito['modifier']) ?>">
    <span class="app-mencao-badge__code"><?= e($conceito['code']) ?></span>
    <span class="app-mencao-badge__sep">-</span>
    <span><?= e($conceito['label']) ?></span>
</span>
