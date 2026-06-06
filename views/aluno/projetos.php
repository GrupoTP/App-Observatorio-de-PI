<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Meus Projetos';
        $headingSubtitle = 'Gerencie todos os seus projetos integradores';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <div class="app-filters-card">
            <div class="app-filters-card__title">
                <?= lucide_tag('filter', 'app-filters-card__title-icon') ?>
                <span>Filtros</span>
            </div>
            <form method="get" action="/projetos" class="app-filters-form" data-projetos-filters autocomplete="off">
                <div class="app-filters-form__grid">
                    <div class="app-filters-form__search">
                        <?= lucide_tag('search', 'app-filters-form__search-icon') ?>
                        <input type="search" name="q" class="app-filters-form__input"
                               placeholder="Buscar por título..." value="<?= e($search) ?>" autocomplete="off">
                    </div>
                    <select name="status" class="app-filters-form__select" autocomplete="off">
                        <option value="todos" <?= $status === 'todos' ? 'selected' : '' ?>>Todos os status</option>
                        <option value="enviado" <?= $status === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                        <option value="avaliado" <?= $status === 'avaliado' ? 'selected' : '' ?>>Avaliado</option>
                        <option value="em-correcao" <?= $status === 'em-correcao' ? 'selected' : '' ?>>Em correção</option>
                    </select>
                </div>
            </form>
        </div>

        <?php if ($projects === []): ?>
            <div class="app-projects-empty">
                <p class="app-projects-empty__text mb-0">
                    <?= !empty($hasFilters)
                        ? 'Nenhum projeto encontrado com os filtros aplicados'
                        : 'Você ainda não tem projetos cadastrados' ?>
                </p>
                <?php if (empty($hasFilters)): ?>
                    <a href="/submeter" class="app-action-btn app-action-btn--primary mt-4">
                        <?= lucide_tag('circle-plus', 'app-action-btn__icon') ?>
                        Submeter primeiro projeto
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="app-projects-grid">
                <?php foreach ($projects as $project): ?>
                    <?php require dirname(__DIR__) . '/partials/app-project-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="app-modal" id="project-delete-modal" hidden aria-hidden="true">
    <div class="app-modal__backdrop" data-modal-close></div>
    <div class="app-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="project-delete-title">
        <div class="app-modal__alert">
            <div class="app-modal__alert-icon" aria-hidden="true">⚠️</div>
            <div>
                <h2 class="app-modal__title" id="project-delete-title">Remover projeto</h2>
                <p class="app-modal__text mb-2">Tem certeza que deseja remover este projeto da sua lista?</p>
                <p class="app-modal__text app-modal__text--strong mb-0">Ele ficará invisível e não poderá mais ser acessado.</p>
            </div>
        </div>
        <form method="post" action="" id="project-delete-form" class="app-modal__actions">
            <?= csrf_field() ?>
            <button type="button" class="app-action-btn app-action-btn--secondary flex-fill" data-modal-close>
                Cancelar
            </button>
            <button type="submit" class="app-action-btn app-action-btn--danger flex-fill">Remover</button>
        </form>
    </div>
</div>
