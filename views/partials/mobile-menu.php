<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

use App\Auth\SessionAuth;

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$studentItems = [
    ['icon' => 'home', 'label' => 'Início', 'path' => '/dashboard'],
    ['icon' => 'folder-open', 'label' => 'Meus Projetos', 'path' => '/projetos'],
    ['icon' => 'plus-circle', 'label' => 'Submeter Projeto', 'path' => '/submeter'],
    ['icon' => 'briefcase', 'label' => 'Meu Portfólio', 'path' => '/portfolio'],
    ['icon' => 'file-text', 'label' => 'Currículo/Portfólio', 'path' => '/curriculo'],
    ['icon' => 'message-square', 'label' => 'Feedbacks', 'path' => '/feedbacks'],
    ['icon' => 'clock', 'label' => 'Prazos', 'path' => '/prazos'],
    ['icon' => 'settings', 'label' => 'Configurações', 'path' => '/configuracoes'],
];

$adminItems = [
    ['icon' => 'shield', 'label' => 'Painel Administrativo', 'path' => '/admin/dashboard'],
    ['icon' => 'bar-chart-3', 'label' => 'Gerenciar Projetos', 'path' => '/admin/projetos'],
    ['icon' => 'users', 'label' => 'Gerenciar Alunos', 'path' => '/admin/alunos'],
    ['icon' => 'folder-git-2', 'label' => 'Gerenciar PI', 'path' => '/admin/pi'],
    ['icon' => 'user-plus', 'label' => 'Gerenciar Usuários', 'path' => '/admin/alunos/novo'],
    ['icon' => 'settings', 'label' => 'Configurações', 'path' => '/configuracoes'],
];

$professorItems = [
    ['icon' => 'shield', 'label' => 'Painel do Professor', 'path' => '/admin/dashboard'],
    ['icon' => 'users', 'label' => 'Gerenciar Alunos', 'path' => '/admin/alunos'],
    ['icon' => 'folder-git-2', 'label' => 'Gerenciar PI', 'path' => '/admin/pi'],
    ['icon' => 'file-text', 'label' => 'Currículo/Portfólio', 'path' => '/curriculo'],
    ['icon' => 'settings', 'label' => 'Configurações', 'path' => '/configuracoes'],
];

$parceiroItems = [
    ['icon' => 'folder-open', 'label' => 'Projetos Integradores', 'path' => '/parceiro'],
    ['icon' => 'settings',    'label' => 'Configurações',          'path' => '/configuracoes'],
];

if (SessionAuth::isAdmin()) {
    $menuItems = $adminItems;
} elseif (SessionAuth::isProfessor()) {
    $menuItems = $professorItems;
} elseif (SessionAuth::isParceiro()) {
    $menuItems = $parceiroItems;
} else {
    $menuItems = $studentItems;
}

$isAdmin = SessionAuth::isAdmin();
?>
<div class="mobile-menu-overlay" id="mobileMenuOverlay" hidden></div>
<nav class="mobile-menu-panel" id="mobileMenuPanel" aria-label="Menu principal" hidden>
    <div class="mobile-menu-panel__header">
        <div class="mobile-menu-panel__brand">
            <div class="app-header__logo-wrap">
                <img src="/assets/img/senac-logo.png" alt="Faculdade Senac" class="app-header__logo" width="80" height="40">
            </div>
            <div class="mobile-menu-panel__titles">
                <span class="mobile-menu-panel__title">Observatório PI</span>
                <?php if ($isAdmin): ?>
                    <span class="mobile-menu-panel__badge">Painel Administrativo</span>
                <?php endif; ?>
            </div>
        </div>
        <button type="button" class="menu-toggle mobile-menu-panel__close" id="menuCloseBtn" aria-label="Fechar menu">
            <?= lucide_tag('x', 'menu-toggle__icon') ?>
        </button>
    </div>

    <ul class="mobile-menu-panel__list">
        <?php foreach ($menuItems as $item): ?>
            <?php
            $isActive = rtrim($currentPath, '/') === rtrim($item['path'], '/');
            $itemClass = 'mobile-menu-item' . ($isActive ? ' mobile-menu-item--active' : '');
            ?>
            <li>
                <a href="<?= e($item['path']) ?>" class="<?= $itemClass ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                    <?= lucide_tag($item['icon'], 'mobile-menu-item__icon') ?>
                    <span class="mobile-menu-item__label"><?= e($item['label']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        <li class="mobile-menu-panel__logout">
            <form method="post" action="/logout" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="mobile-menu-item mobile-menu-item--logout">
                    <?= lucide_tag('log-out', 'mobile-menu-item__icon') ?>
                    <span class="mobile-menu-item__label">Sair</span>
                </button>
            </form>
        </li>
    </ul>
</nav>
