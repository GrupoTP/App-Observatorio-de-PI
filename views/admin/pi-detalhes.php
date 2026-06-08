<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <?php /* ─── Header with actions ─── */ ?>
        <div class="app-pi-header">
            <div class="app-pi-header__text">
                <a href="/admin/pi" class="app-page-heading__back" aria-label="Voltar">
                    <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
                </a>
                <div>
                    <h1 class="app-page-heading__title"><?= e($project['nome_grupo'] ?? $project['titulo']) ?></h1>
                    <p class="app-page-heading__subtitle">Detalhes completos do grupo e avaliações</p>
                </div>
            </div>
            <div class="app-pi-header__actions">
                <a href="/admin/pi/<?= e($project['id_projeto']) ?>/editar"
                   class="app-action-btn app-action-btn--secondary app-action-btn--sm">
                    <?= lucide_tag('edit-2', 'app-action-btn__icon') ?>
                    Editar
                </a>
                <a href="/admin/pi/<?= e($project['id_projeto']) ?>/avaliar"
                   class="app-action-btn app-action-btn--green app-action-btn--sm">
                    <?= lucide_tag('check-circle', 'app-action-btn__icon') ?>
                    <?= $feedback ? 'Reavaliar' : 'Avaliar' ?>
                </a>
            </div>
        </div>

        <?php /* ─── Project info card ─── */ ?>
        <div class="app-form-card">
            <div class="app-pi-detalhes__info-header">
                <div class="app-pi-detalhes__info-body">
                    <h2 class="app-pi-detalhes__project-title"><?= e($project['titulo']) ?></h2>

                    <?php if (!empty($project['descricao'])): ?>
                    <p class="app-pi-detalhes__descricao"><?= e($project['descricao']) ?></p>
                    <?php endif; ?>

                    <div class="app-pi-detalhes__grid">
                        <div class="app-pi-detalhes__field">
                            <span class="app-pi-detalhes__label">Curso:</span>
                            <span class="app-pi-detalhes__value"><?= e($project['nome_curso'] ?? '') ?></span>
                        </div>
                        <div class="app-pi-detalhes__field">
                            <span class="app-pi-detalhes__label">Módulo:</span>
                            <span class="app-pi-detalhes__value"><?= e($project['modulo'] ?? '') ?></span>
                        </div>
                        <?php if (!empty($project['data_criacao'])): ?>
                        <div class="app-pi-detalhes__field">
                            <span class="app-pi-detalhes__label">Criado em:</span>
                            <span class="app-pi-detalhes__value"><?= e(date('d/m/Y', strtotime($project['data_criacao']))) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($project['prazo_especial'])): ?>
                        <div class="app-pi-detalhes__field">
                            <span class="app-pi-detalhes__label">Prazo:</span>
                            <span class="app-pi-detalhes__value"><?= e(date('d/m/Y', strtotime($project['prazo_especial']))) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                $situacao = $project['situacao_projeto'] ?? 'em-andamento';
                $badgeConfig = match ($situacao) {
                    'avaliado'    => ['modifier' => 'success', 'icon' => 'check-circle', 'label' => 'Avaliado'],
                    'enviado'     => ['modifier' => 'warning', 'icon' => 'clock',        'label' => 'Aguardando Avaliação'],
                    'em-correcao' => ['modifier' => 'info',    'icon' => 'edit-2',       'label' => 'Em Correção'],
                    default       => ['modifier' => 'default', 'icon' => 'file-text',    'label' => 'Em Andamento'],
                };
                ?>
                <span class="app-pi-status app-pi-status--<?= e($badgeConfig['modifier']) ?>">
                    <?= lucide_tag($badgeConfig['icon'], 'app-pi-status__icon') ?>
                    <?= e($badgeConfig['label']) ?>
                </span>
            </div>

            <?php /* ─── Members ─── */ ?>
            <div class="app-pi-detalhes__section">
                <div class="app-pi-detalhes__section-title">
                    <?= lucide_tag('users', 'app-pi-detalhes__section-icon') ?>
                    <h3>Membros do Grupo (<?= 1 + count($members) ?>)</h3>
                </div>
                <div class="app-pi-detalhes__members">
                    <?php
                    $allMembers = array_filter(
                        array_merge([$submitter], $members),
                        fn($m) => $m !== null
                    );
                    foreach ($allMembers as $m):
                    ?>
                    <div class="app-pi-detalhes__member-card">
                        <p class="app-pi-detalhes__member-name"><?= e(user_display_name($m)) ?></p>
                        <p class="app-pi-detalhes__member-email"><?= e($m['email_institucional'] ?? '') ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php /* ─── Evaluation card ─── */ ?>
        <?php if ($feedback): ?>
        <div class="app-form-card">
            <div class="app-pi-detalhes__section-title">
                <?= lucide_tag('file-text', 'app-pi-detalhes__section-icon') ?>
                <h3>Avaliação do Grupo</h3>
            </div>

            <?php
            // Compute overall concept from average of rubrica scores
            $scoreValues = array_map(fn($r) => (float) $r['conceito'], $rubricaScores);
            $avgScore = count($scoreValues) > 0 ? array_sum($scoreValues) / count($scoreValues) : null;
            $conceitoFinal = $avgScore !== null ? nota_para_conceito($avgScore) : null;
            ?>

            <?php if ($conceitoFinal !== null): ?>
            <div class="app-pi-detalhes__conceito-final">
                <p class="app-pi-detalhes__label app-pi-detalhes__label--section">Conceito Final:</p>
                <span class="app-mencao-badge app-mencao-badge--<?= e($conceitoFinal['modifier']) ?> app-mencao-badge--lg">
                    <span class="app-mencao-badge__code"><?= e($conceitoFinal['code']) ?></span>
                    &nbsp;— <?= e($conceitoFinal['label']) ?>
                </span>
            </div>
            <?php endif; ?>

            <?php if (!empty($rubricaScores)): ?>
            <div class="app-pi-detalhes__rubrica">
                <p class="app-pi-detalhes__rubrica-title">Rubrica de Avaliação:</p>
                <div class="app-pi-detalhes__rubrica-grid">
                    <?php foreach ($rubricaScores as $score):
                        $criterioNome = $rubricaCriteria[$score['criterio']] ?? $score['criterio'];
                        $nota = (float) $score['conceito'];
                        $conceito = nota_para_conceito($nota);
                    ?>
                    <div class="app-pi-detalhes__rubrica-item app-pi-detalhes__rubrica-item--<?= e($conceito['modifier']) ?>">
                        <p class="app-pi-detalhes__rubrica-criterio"><?= e($criterioNome) ?></p>
                        <span class="app-mencao-badge app-mencao-badge--<?= e($conceito['modifier']) ?>">
                            <span class="app-mencao-badge__code"><?= e($conceito['code']) ?></span>
                            &nbsp;— <?= e($conceito['label']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="app-pi-detalhes__feedback-body">
                <p class="app-pi-detalhes__label app-pi-detalhes__label--section">Feedback do Professor:</p>
                <p class="app-pi-detalhes__feedback-text"><?= e($feedback['descricao'] ?? '') ?></p>
                <p class="app-pi-detalhes__feedback-meta">
                    Avaliado em <?= e(format_datetime($feedback['data'])) ?>
                    <?php if (!empty($feedback['nome_civil_nome'])): ?>
                    por <?= e(trim($feedback['nome_civil_nome'] . ' ' . ($feedback['nome_civil_sobrenome'] ?? ''))) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php else: ?>

        <?php /* ─── Empty evaluation state ─── */ ?>
        <div class="app-empty-state">
            <?= lucide_tag('file-text', 'app-empty-state__icon') ?>
            <h3 class="app-empty-state__title">Nenhuma avaliação ainda</h3>
            <p class="app-empty-state__subtitle">Este grupo ainda não foi avaliado.</p>
            <a href="/admin/pi/<?= e($project['id_projeto']) ?>/avaliar"
               class="app-action-btn app-action-btn--green app-action-btn--sm mt-2">
                <?= lucide_tag('check-circle', 'app-action-btn__icon') ?>
                Avaliar Agora
            </a>
        </div>

        <?php endif; ?>

    </div>
</div>
