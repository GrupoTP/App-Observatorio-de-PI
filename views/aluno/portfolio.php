<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php
$projectCount = count($projects);
$hasExcellenceBadge = false;
foreach ($projects as $portfolioProject) {
    if (($portfolioProject['conceito']['code'] ?? '') === 'AE') {
        $hasExcellenceBadge = true;
        break;
    }
}
?>
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Meu Portfólio Profissional';
        $headingSubtitle = 'Projetos concluídos e aprovados';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <div class="app-portfolio-actions">
            <a href="/curriculo" class="app-action-btn app-action-btn--primary">
                <?= lucide_tag('file-text', 'app-action-btn__icon') ?>
                Gerar currículo
            </a>
            <a href="/configuracoes" class="app-action-btn app-action-btn--secondary">
                <?= lucide_tag('external-link', 'app-action-btn__icon') ?>
                Compartilhar portfólio
            </a>
        </div>

        <?php if ($projects === []): ?>
            <div class="app-portfolio-empty">
                <?= lucide_tag('award', 'app-portfolio-empty__icon') ?>
                <h2 class="app-portfolio-empty__title">Nenhum projeto aprovado ainda</h2>
                <p class="app-portfolio-empty__text mb-0">
                    Continue trabalhando nos seus projetos. Eles aparecerão aqui após a avaliação,
                    com conceito a partir de Bom e com autorização para empresas parceiras.
                </p>
            </div>
        <?php else: ?>
            <div class="app-portfolio-list">
                <?php foreach ($projects as $project): ?>
                    <?php require dirname(__DIR__) . '/partials/app-portfolio-project-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="app-portfolio-badges-card" aria-labelledby="portfolio-badges-title">
            <h2 class="app-portfolio-badges-card__title" id="portfolio-badges-title">
                <?= lucide_tag('award', 'app-portfolio-badges-card__title-icon') ?>
                Selos conquistados
            </h2>
            <?php if ($projectCount === 0 && !$hasExcellenceBadge): ?>
                <p class="app-portfolio-badges-card__empty mb-0">
                    Os selos aparecem conforme você conclui projetos no portfólio.
                </p>
            <?php else: ?>
                <ul class="app-portfolio-badges">
                    <?php if ($projectCount >= 1): ?>
                        <li class="app-portfolio-badge">
                            <span class="app-portfolio-badge__icon app-portfolio-badge__icon--yellow" aria-hidden="true">
                                <?= lucide_tag('award', 'app-portfolio-badge__icon-svg') ?>
                            </span>
                            <span class="app-portfolio-badge__label">Primeiro projeto</span>
                        </li>
                    <?php endif; ?>
                    <?php if ($projectCount >= 3): ?>
                        <li class="app-portfolio-badge">
                            <span class="app-portfolio-badge__icon app-portfolio-badge__icon--green" aria-hidden="true">
                                <?= lucide_tag('award', 'app-portfolio-badge__icon-svg') ?>
                            </span>
                            <span class="app-portfolio-badge__label">Três projetos</span>
                        </li>
                    <?php endif; ?>
                    <?php if ($hasExcellenceBadge): ?>
                        <li class="app-portfolio-badge">
                            <span class="app-portfolio-badge__icon app-portfolio-badge__icon--blue" aria-hidden="true">
                                <?= lucide_tag('award', 'app-portfolio-badge__icon-svg') ?>
                            </span>
                            <span class="app-portfolio-badge__label">Excelência (AE)</span>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>
