<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <div class="app-pi-header__text">
            <a href="/parceiro" class="app-page-heading__back" aria-label="Voltar">
                <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
            </a>
            <div>
                <h1 class="app-page-heading__title"><?= e($project['titulo']) ?></h1>
                <p class="app-page-heading__subtitle">
                    <?= e($project['nome_curso'] ?? '') ?> — <?= e($project['modulo'] ?? '') ?>
                </p>
            </div>
        </div>

        <?php /* ─── Project info ─── */ ?>
        <div class="app-form-card">
            <div class="app-pi-detalhes__info-header">
                <div class="app-pi-detalhes__info-body">
                    <?php if (!empty($project['descricao'])): ?>
                    <p class="app-pi-detalhes__descricao"><?= e($project['descricao']) ?></p>
                    <?php endif; ?>

                    <div class="app-pi-detalhes__grid">
                        <?php if (!empty($project['tecnologias'])): ?>
                        <div class="app-pi-detalhes__field" style="grid-column:span 2">
                            <span class="app-pi-detalhes__label">Tecnologias:</span>
                            <span class="app-pi-detalhes__value"><?= e($project['tecnologias']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($project['link_repo_git'])): ?>
                        <div class="app-pi-detalhes__field" style="grid-column:span 2">
                            <span class="app-pi-detalhes__label">Repositório:</span>
                            <a href="<?= e($project['link_repo_git']) ?>"
                               target="_blank" rel="noopener"
                               class="app-pi-detalhes__value">
                                <?= e($project['link_repo_git']) ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <span class="app-pi-status app-pi-status--success">
                    <?= lucide_tag('check-circle', 'app-pi-status__icon') ?>
                    Avaliado
                </span>
            </div>

            <?php /* Members */ ?>
            <div class="app-pi-detalhes__section">
                <div class="app-pi-detalhes__section-title">
                    <?= lucide_tag('users', 'app-pi-detalhes__section-icon') ?>
                    <h3>Membros (<?= 1 + count($members) ?>)</h3>
                </div>
                <div class="app-pi-detalhes__members">
                    <?php
                    $allMembers = array_filter(array_merge([$submitter], $members));
                    foreach ($allMembers as $m):
                    ?>
                    <div class="app-pi-detalhes__member-card">
                        <p class="app-pi-detalhes__member-name"><?= e(user_display_name($m)) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php /* ─── Evaluation ─── */ ?>
        <?php if ($feedback): ?>
        <div class="app-form-card">
            <div class="app-pi-detalhes__section-title">
                <?= lucide_tag('file-text', 'app-pi-detalhes__section-icon') ?>
                <h3>Avaliação</h3>
            </div>

            <?php
            $scoreValues = array_map(fn($r) => (float) $r['conceito'], $rubricaScores);
            $avg = count($scoreValues) > 0 ? array_sum($scoreValues) / count($scoreValues) : null;
            $conceitoFinal = $avg !== null ? nota_para_conceito($avg) : null;
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
                        $conceito = nota_para_conceito((float) $score['conceito']);
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

            <?php if (!empty($feedback['descricao'])): ?>
            <div class="app-pi-detalhes__feedback-body">
                <p class="app-pi-detalhes__label app-pi-detalhes__label--section">Feedback do Professor:</p>
                <p class="app-pi-detalhes__feedback-text"><?= e($feedback['descricao']) ?></p>
                <p class="app-pi-detalhes__feedback-meta">
                    Avaliado em <?= e(format_datetime($feedback['data'])) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
