<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

if (!isset($flash) || $flash === null) {
    return;
}

$isError = ($flash['type'] ?? '') === 'error';
$icon = $isError ? 'bi-exclamation-circle' : 'bi-check-circle';
$modifier = $isError ? 'auth-alert--error' : 'auth-alert--success';
?>
<div class="auth-alert <?= $modifier ?> mb-4" role="alert">
    <i class="bi <?= $icon ?>" aria-hidden="true"></i>
    <div><?= e($flash['message']) ?></div>
</div>
