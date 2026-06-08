<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php
/**
 * @var list<array<string, mixed>> $turmas
 * @var array<string, list<array<string, mixed>>> $byMonth
 */
function prazo_status_info(array $t): array
{
    $sit = $t['situacao_projeto'] ?? null;
    $ts  = strtotime($t['prazo_projetos'] ?? '') ?: 0;
    $now = time();

    if ($sit === 'avaliado' || $sit === 'enviado') {
        return [
            'icon' => 'circle-check-big',
            'text' => 'Entregue',
            'modifier' => 'submitted',
        ];
    }

    if ($ts > 0 && $ts < $now) {
        return [
            'icon' => 'circle-x',
            'text' => 'Prazo vencido',
            'modifier' => 'overdue',
        ];
    }

    if ($ts > 0) {
        $days = (int) ceil(($ts - $now) / 86400);
        if ($days <= 3) {
            return [
                'icon' => 'circle-alert',
                'text' => $days . ' ' . ($days === 1 ? 'dia restante' : 'dias restantes'),
                'modifier' => 'urgent',
            ];
        }

        return [
            'icon' => 'clock',
            'text' => $days . ' dias restantes',
            'modifier' => 'pending',
        ];
    }

    return [
        'icon' => 'clock',
        'text' => 'Sem prazo',
        'modifier' => 'pending',
    ];
}
?>
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Prazos de Entrega';
        $headingSubtitle = 'Acompanhe os prazos dos seus projetos';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <?php if ($turmas === []): ?>
            <div class="app-prazos-empty">
                <?= lucide_tag('calendar', 'app-prazos-empty__icon') ?>
                <h2 class="app-prazos-empty__title">Nenhum prazo registrado</h2>
                <p class="app-prazos-empty__text mb-0">Os prazos das suas turmas aparecerão aqui.</p>
            </div>
        <?php else: ?>
            <?php foreach ($byMonth as $monthLabel => $items): ?>
                <section class="app-prazos-month" aria-labelledby="month-<?= e(md5($monthLabel)) ?>">
                    <div class="app-prazos-month__heading">
                        <?= lucide_tag('calendar', 'app-prazos-month__icon') ?>
                        <h2 class="app-prazos-month__title" id="month-<?= e(md5($monthLabel)) ?>">
                            <?= e($monthLabel) ?>
                        </h2>
                    </div>

                    <div class="app-prazos-list">
                        <?php foreach ($items as $t): ?>
                            <?php $statusInfo = prazo_status_info($t); ?>
                            <article class="app-prazo-card app-prazo-card--<?= e($statusInfo['modifier']) ?>">
                                <div class="app-prazo-card__body">
                                    <h3 class="app-prazo-card__turma">
                                        <?= e($t['nome_curso'] ?? '—') ?>
                                    </h3>
                                    <div class="app-prazo-card__meta">
                                        <span><?= e($t['nome_turma'] ?? '') ?></span>
                                        <?php if (!empty($t['modulo'])): ?>
                                            <span class="app-prazo-card__meta-sep" aria-hidden="true">·</span>
                                            <span><?= e($t['modulo']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($t['prazo_projetos'])): ?>
                                            <span class="app-prazo-card__meta-sep" aria-hidden="true">·</span>
                                            <?= lucide_tag('calendar', 'app-prazo-card__cal-icon') ?>
                                            <strong>Prazo:</strong>&nbsp;<?= e(format_datetime($t['prazo_projetos'])) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($t['id_projeto'])): ?>
                                        <a href="/projetos/<?= e($t['id_projeto']) ?>"
                                           class="app-prazo-card__project-link">
                                            Ver projeto
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="app-prazo-card__status app-prazo-status--<?= e($statusInfo['modifier']) ?>">
                                    <?= lucide_tag($statusInfo['icon'], 'app-prazo-card__status-icon') ?>
                                    <?= e($statusInfo['text']) ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
