<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

use App\Auth\SessionAuth;

$headerTitle = $headerTitle ?? 'Observatório PI';
$isStaff = SessionAuth::isAdmin() || SessionAuth::isProfessor();
?>
<header class="app-header d-flex align-items-center justify-content-between px-3 px-md-4">
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="menu-toggle" id="menuToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobileMenuPanel">
            <i class="bi bi-list fs-3" id="menuIconOpen"></i>
            <i class="bi bi-x-lg fs-3 d-none" id="menuIconClose"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
            <span class="logo-box">SENAC</span>
            <div>
                <div class="fw-bold"><?= e($headerTitle) ?></div>
                <?php if ($isStaff): ?>
                <small class="text-senac-yellow">Painel <?= SessionAuth::isAdmin() ? 'Admin' : 'Professor' ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div style="width:48px"></div>
</header>
