<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php

use App\Auth\SessionAuth;

$isAdmin = SessionAuth::isAdmin();
$panelTitle = $isAdmin ? 'Painel Administrativo' : 'Painel do Professor';
?>
<div class="app-page">
    <div class="app-page__section">
        <?php
        $headingTitle = $panelTitle;
        $headingSubtitle = 'Bem-vindo, ' . ($userName ?? '');
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <div class="row g-3 g-md-4 mb-4 mb-md-5">
            <div class="col-sm-6 col-lg-3">
                <?php
                $value = $totalProjects;
                $label = 'Total de Projetos';
                $color = 'blue';
                $lucideIcon = 'file-text';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
            <div class="col-sm-6 col-lg-3">
                <?php
                $value = $totalStudents;
                $label = 'Total de Alunos';
                $color = 'yellow';
                $lucideIcon = 'users';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
            <div class="col-sm-6 col-lg-3">
                <?php
                $value = $pending;
                $label = 'Aguardando Avaliação';
                $color = 'orange';
                $lucideIcon = 'clock';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
            <div class="col-sm-6 col-lg-3">
                <?php
                $value = $evaluated;
                $label = 'Projetos Avaliados';
                $color = 'green';
                $lucideIcon = 'circle-check-big';
                require dirname(__DIR__) . '/partials/stats-card-metric.php';
                ?>
            </div>
        </div>

        <?php if (SessionAuth::isProfessor() && !$isAdmin): ?>
            <div class="admin-info-box mb-4 mb-md-5">
                <h2 class="admin-info-box__title">Central de Avaliações</h2>
                <p class="admin-info-box__text mb-0">
                    Use <strong>Gerenciar Alunos</strong> para avaliar alunos individualmente com notas e feedbacks.
                    Use <strong>Gerenciar PI</strong> para criar grupos, definir prazos e avaliar Projetos Integradores
                    (em grupo ou individual). Todas as avaliações aparecerão na tela de Feedbacks dos alunos.
                </p>
            </div>
        <?php endif; ?>

        <div class="row g-3 g-md-4 mb-4 mb-md-5">
            <?php if ($isAdmin): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="/admin/projetos" class="admin-action-card admin-action-card--blue">
                        <div>
                            <h2 class="admin-action-card__title">Gerenciar Projetos</h2>
                            <p class="admin-action-card__desc">Avaliar e gerenciar todos os projetos</p>
                        </div>
                        <i class="bi bi-bar-chart-line admin-action-card__icon" aria-hidden="true"></i>
                    </a>
                </div>
            <?php endif; ?>
            <div class="col-md-6 col-lg-4">
                <a href="/admin/alunos" class="admin-action-card admin-action-card--yellow">
                    <div>
                        <h2 class="admin-action-card__title">Gerenciar Alunos</h2>
                        <p class="admin-action-card__desc">
                            <?= $isAdmin ? 'Visualizar e gerenciar alunos' : 'Avaliar e atribuir notas aos alunos' ?>
                        </p>
                    </div>
                    <i class="bi bi-people admin-action-card__icon" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="/admin/pi" class="admin-action-card admin-action-card--purple">
                    <div>
                        <h2 class="admin-action-card__title">Gerenciar PI</h2>
                        <p class="admin-action-card__desc">Grupos e avaliações de Projetos Integradores</p>
                    </div>
                    <i class="bi bi-diagram-3 admin-action-card__icon" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <div class="admin-panel-card mb-4 mb-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="admin-panel-card__title mb-0">Projetos Recentes</h2>
                    <a href="/admin/projetos" class="admin-panel-card__link">Ver todos</a>
                </div>

                <?php if ($recent === []): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-text display-6 d-block mb-2" aria-hidden="true"></i>
                        Nenhum projeto enviado
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table table mb-0">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th class="d-none d-md-table-cell">Projeto</th>
                                    <th class="d-none d-lg-table-cell">Data de Envio</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent as $project): ?>
                                    <?php
                                    $studentName = trim(($project['nome_civil_nome'] ?? '') . ' ' . ($project['nome_civil_sobrenome'] ?? ''));
                                    $status = $project['situacao_projeto'] ?? '';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small"><?= e($studentName) ?></div>
                                            <div class="text-muted small d-md-none"><?= e($project['titulo'] ?? '') ?></div>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <div class="small"><?= e($project['titulo'] ?? '') ?></div>
                                            <div class="text-muted smaller">
                                                <?= e($project['nome_curso'] ?? '') ?> - <?= e($project['modulo'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td class="d-none d-lg-table-cell text-muted small">
                                            <?= e(format_date($project['prazo_especial'] ?? null)) ?>
                                        </td>
                                        <td>
                                            <span class="admin-status-badge <?= admin_situacao_badge_class($status) ?>">
                                                <?= e(admin_situacao_label($status)) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="/admin/projetos/<?= e($project['id_projeto']) ?>/avaliar"
                                               class="admin-panel-card__link">
                                                <?= $status === 'enviado' ? 'Avaliar' : 'Ver' ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row g-3 g-md-4">
            <div class="col-md-6">
                <div class="admin-panel-card h-100">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-graph-up-arrow text-success fs-5" aria-hidden="true"></i>
                        <h2 class="admin-panel-card__title mb-0">Desempenho Geral</h2>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Média Geral:</span>
                        <span class="fs-4 fw-bold text-senac-blue">8.8</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Taxa de Aprovação:</span>
                        <span class="fs-4 fw-bold text-success">95%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="admin-panel-card h-100">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-clock text-warning fs-5" aria-hidden="true"></i>
                        <h2 class="admin-panel-card__title mb-0">Prazos Próximos</h2>
                    </div>
                    <p class="text-muted small mb-2">
                        <span class="fw-semibold text-dark"><?= (int) $pending ?> projeto<?= $pending === 1 ? '' : 's' ?></span>
                        com prazo nos próximos 7 dias
                    </p>
                    <a href="/admin/projetos" class="admin-panel-card__link">Ver detalhes →</a>
                </div>
            </div>
        </div>
    </div>
</div>
