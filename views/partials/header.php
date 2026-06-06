<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

use App\Auth\SessionAuth;

$headerTitle = $headerTitle ?? 'Observatório PI';
?>
<header class="app-header">
    <div class="app-header__start">
        <button type="button" class="menu-toggle" id="menuToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobileMenuPanel">
            <span class="menu-toggle__icon-wrap" id="menuIconOpen">
                <?= lucide_tag('menu', 'menu-toggle__icon') ?>
            </span>
            <span class="menu-toggle__icon-wrap d-none" id="menuIconClose">
                <?= lucide_tag('x', 'menu-toggle__icon') ?>
            </span>
        </button>
        <div class="app-header__brand">
            <div class="app-header__logo-wrap">
                <img src="/assets/img/senac-logo.png" alt="Faculdade Senac" class="app-header__logo" width="80" height="40">
            </div>
            <div class="app-header__titles">
                <span class="app-header__title"><?= e($headerTitle) ?></span>
                <?php if (SessionAuth::isAdmin()): ?>
                    <span class="app-header__badge d-none d-md-inline">Painel Admin</span>
                <?php elseif (SessionAuth::isProfessor()): ?>
                    <span class="app-header__badge d-none d-md-inline">Painel Professor</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="app-header__spacer" aria-hidden="true"></div>
</header>
