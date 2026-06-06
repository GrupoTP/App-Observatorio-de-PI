<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Olá, ' . ($userName ?? '') . '!';
        $headingSubtitle = 'Bem-vindo ao Observatório de Projetos Integradores';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <div class="row g-4">
            <div class="col-md-4">
                <?php
                $value = $sentCount;
                $label = 'Projetos enviados';
                $color = 'yellow';
                $lucideIcon = 'file-text';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
            <div class="col-md-4">
                <?php
                $value = $evaluatedCount;
                $label = 'Projetos avaliados';
                $color = 'green';
                $lucideIcon = 'circle-check-big';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
            <div class="col-md-4">
                <?php
                $value = $upcomingCount;
                $label = 'Prazo próximo';
                $color = 'orange';
                $lucideIcon = 'clock';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
        </div>

        <div class="app-quick-actions">
            <a href="/submeter" class="app-action-btn app-action-btn--primary">
                <?= lucide_tag('circle-plus', 'app-action-btn__icon') ?>
                Submeter novo projeto
            </a>
            <a href="/portfolio" class="app-action-btn app-action-btn--secondary">
                <?= lucide_tag('briefcase', 'app-action-btn__icon') ?>
                Ver meu portfólio
            </a>
        </div>

        <div>
            <h2 class="app-section-title mb-4">Atividades recentes</h2>

            <?php if ($recentProjects === []): ?>
                <div class="activity-empty text-center py-5">
                    <?= lucide_tag('file-text', 'activity-empty__icon') ?>
                    <p class="activity-empty__text mb-4">Nenhuma atividade recente</p>
                    <a href="/submeter" class="app-action-btn app-action-btn--primary activity-empty__cta">
                        <?= lucide_tag('circle-plus', 'app-action-btn__icon') ?>
                        Submeter primeiro projeto
                    </a>
                </div>
            <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($recentProjects as $project): ?>
                        <?php require dirname(__DIR__) . '/partials/activity-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
