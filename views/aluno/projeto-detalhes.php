<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php
$p = $project;
$status = projeto_status_meta((string) ($p['situacao_projeto'] ?? ''));
$isEvaluated = ($p['situacao_projeto'] ?? '') === 'avaliado' && ($conceito ?? null) !== null;
?>
<div class="app-page app-page--narrow">
    <div class="app-page-stack">
        <?php
        $headingTitle = $p['titulo'] ?? 'Detalhes do projeto';
        $headingSubtitle = turma_display_label($p);
        $backUrl = '/projetos';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <div class="app-form-card app-project-detail">
            <div class="app-project-detail__status app-project-status app-project-status--<?= e($status['modifier']) ?>">
                <div class="app-project-status__heading">
                    <?= lucide_tag($status['icon'], 'app-project-status__icon') ?>
                    <span class="app-project-status__label"><?= e($status['label']) ?></span>
                </div>
                <?php if ($status['description'] !== ''): ?>
                    <p class="app-project-status__description mb-0"><?= e($status['description']) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($isEvaluated && ($conceito ?? null) !== null): ?>
                <div class="app-project-grade">
                    <div class="app-project-grade__conceito-row">
                        <span class="app-project-grade__label">Conceito final:</span>
                        <?php require dirname(__DIR__) . '/partials/app-conceito-badge.php'; ?>
                    </div>
                </div>
            <?php endif; ?>

            <dl class="app-project-view">
                <?php if (!empty($p['nome_grupo'])): ?>
                    <div class="app-project-view__row">
                        <dt>Nome do grupo</dt>
                        <dd><?= e($p['nome_grupo']) ?></dd>
                    </div>
                <?php endif; ?>
                <div class="app-project-view__row">
                    <dt>Turma</dt>
                    <dd><?= e(turma_display_label($p)) ?></dd>
                </div>
                <div class="app-project-view__row">
                    <dt>Descrição</dt>
                    <dd><?= e($p['descricao'] ?? '—') ?></dd>
                </div>
                <div class="app-project-view__row">
                    <dt>Tecnologias</dt>
                    <dd><?= e($p['tecnologias'] ?? '—') ?></dd>
                </div>
                <div class="app-project-view__row">
                    <dt>Repositório Git</dt>
                    <dd>
                        <?php if (!empty($p['link_repo_git'])): ?>
                            <a href="<?= e($p['link_repo_git']) ?>" target="_blank" rel="noopener">
                                <?= e($p['link_repo_git']) ?>
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="app-project-view__row">
                    <dt>Data de envio</dt>
                    <dd><?= e(format_date($submittedAt ?? null)) ?></dd>
                </div>
                <div class="app-project-view__row">
                    <dt>Prazo</dt>
                    <dd><?= e(format_date($p['prazo_especial'] ?? null)) ?></dd>
                </div>
                <div class="app-project-view__row">
                    <dt>Portfólio público</dt>
                    <dd><?= !empty($p['publico']) ? 'Autorizado para empresas parceiras' : 'Não autorizado' ?></dd>
                </div>
            </dl>

            <div class="app-project-detail__section">
                <h2 class="app-project-detail__section-title">Alunos do projeto</h2>
                <?php if (($students ?? []) === []): ?>
                    <p class="app-project-detail__empty mb-0">Nenhum aluno vinculado a este projeto.</p>
                <?php else: ?>
                    <?php require dirname(__DIR__) . '/partials/app-project-detail-students.php'; ?>
                <?php endif; ?>
            </div>

            <div class="app-project-detail__section">
                <h2 class="app-project-detail__section-title">Anexos</h2>
                <?php if (($attachments ?? []) === []): ?>
                    <p class="app-project-detail__empty mb-0">Nenhum anexo enviado para este projeto.</p>
                <?php else: ?>
                    <?php require dirname(__DIR__) . '/partials/app-project-detail-attachments.php'; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($feedback)): ?>
                <div class="app-project-detail__section">
                    <h2 class="app-project-detail__section-title">Feedback do professor</h2>
                    <p class="app-project-detail__feedback-meta mb-2">
                        <?= e(trim(($feedback['nome_civil_nome'] ?? '') . ' ' . ($feedback['nome_civil_sobrenome'] ?? ''))) ?>
                        — <?= e(format_datetime($feedback['data'] ?? null)) ?>
                    </p>
                    <?php if (!empty($feedback['rubrica'])): ?>
                        <ul class="app-project-detail__rubrica list-unstyled mb-3">
                            <?php foreach ($feedback['rubrica'] as $r): ?>
                                <?php $criterioConceito = nota_para_conceito((float) $r['conceito']); ?>
                                <li class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <strong><?= e($r['criterio']) ?>:</strong>
                                    <?php $conceito = $criterioConceito; ?>
                                    <?php require dirname(__DIR__) . '/partials/app-conceito-badge.php'; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <p class="app-project-detail__feedback-text mb-0"><?= e($feedback['descricao'] ?? '') ?></p>
                </div>
            <?php endif; ?>

            <div class="app-form-actions">
                <a href="/projetos" class="app-action-btn app-action-btn--secondary">Voltar</a>
                <?php if (!empty($isOwner)): ?>
                    <a href="/projetos/<?= e($p['id_projeto']) ?>/editar"
                       class="app-action-btn app-action-btn--primary">
                        Editar projeto
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
