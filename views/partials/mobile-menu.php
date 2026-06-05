<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

use App\Auth\SessionAuth;

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$studentItems = [
    ['icon' => 'bi-house', 'label' => 'Início', 'path' => '/dashboard'],
    ['icon' => 'bi-folder', 'label' => 'Meus Projetos', 'path' => '/projetos'],
    ['icon' => 'bi-plus-circle', 'label' => 'Submeter Projeto', 'path' => '/submeter'],
    ['icon' => 'bi-briefcase', 'label' => 'Meu Portfólio', 'path' => '/portfolio'],
    ['icon' => 'bi-file-person', 'label' => 'Currículo/Portfólio', 'path' => '/curriculo'],
    ['icon' => 'bi-chat-left-text', 'label' => 'Feedbacks', 'path' => '/feedbacks'],
    ['icon' => 'bi-clock', 'label' => 'Prazos', 'path' => '/prazos'],
    ['icon' => 'bi-gear', 'label' => 'Configurações', 'path' => '/configuracoes'],
];

$adminItems = [
    ['icon' => 'bi-shield', 'label' => 'Painel Administrativo', 'path' => '/admin/dashboard'],
    ['icon' => 'bi-bar-chart', 'label' => 'Gerenciar Projetos', 'path' => '/admin/projetos'],
    ['icon' => 'bi-people', 'label' => 'Gerenciar Alunos', 'path' => '/admin/alunos'],
    ['icon' => 'bi-diagram-3', 'label' => 'Gerenciar PI', 'path' => '/admin/pi'],
    ['icon' => 'bi-person-plus', 'label' => 'Gerenciar Usuários', 'path' => '/admin/alunos/novo'],
    ['icon' => 'bi-gear', 'label' => 'Configurações', 'path' => '/configuracoes'],
];

$professorItems = [
    ['icon' => 'bi-shield', 'label' => 'Painel do Professor', 'path' => '/admin/dashboard'],
    ['icon' => 'bi-people', 'label' => 'Gerenciar Alunos', 'path' => '/admin/alunos'],
    ['icon' => 'bi-diagram-3', 'label' => 'Gerenciar PI', 'path' => '/admin/pi'],
    ['icon' => 'bi-file-person', 'label' => 'Currículo/Portfólio', 'path' => '/curriculo'],
    ['icon' => 'bi-gear', 'label' => 'Configurações', 'path' => '/configuracoes'],
];

if (SessionAuth::isAdmin()) {
    $menuItems = $adminItems;
} elseif (SessionAuth::isProfessor()) {
    $menuItems = $professorItems;
} else {
    $menuItems = $studentItems;
}
?>
<div class="mobile-menu-overlay" id="mobileMenuOverlay" hidden></div>
<nav class="mobile-menu-panel" id="mobileMenuPanel" aria-label="Menu principal" hidden>
    <div class="p-3 bg-senac-blue text-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Menu</span>
        <button type="button" class="btn btn-sm text-senac-yellow border-0" id="menuCloseBtn" aria-label="Fechar menu"><i class="bi bi-x-lg"></i></button>
    </div>
    <?php foreach ($menuItems as $item): ?>
        <?php $active = rtrim($currentPath, '/') === rtrim($item['path'], '/') ? 'active' : ''; ?>
        <a href="<?= e($item['path']) ?>" class="<?= $active ?>">
            <i class="bi <?= e($item['icon']) ?> fs-5 text-senac-blue"></i>
            <?= e($item['label']) ?>
        </a>
    <?php endforeach; ?>
    <form method="post" action="/logout" class="m-0">
        <?= csrf_field() ?>
        <button type="submit" class="w-100 border-0 bg-transparent text-start d-flex align-items-center gap-3 px-4 py-3 text-danger">
            <i class="bi bi-box-arrow-right fs-5"></i> Sair
        </button>
    </form>
</nav>
