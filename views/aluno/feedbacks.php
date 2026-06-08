<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php
/** @var list<array<string, mixed>> $feedbacks */
$allCodes = [];
foreach ($feedbacks as $fb) {
    $code = $fb['conceito_final']['code'] ?? null;
    if ($code !== null) {
        $allCodes[$code] = true;
    }
}
$filterCode = $_GET['conceito'] ?? 'todas';
$sortAsc = ($_GET['ordem'] ?? 'desc') === 'asc';

$visibleFeedbacks = $feedbacks;
if ($filterCode !== 'todas') {
    $visibleFeedbacks = array_filter(
        $feedbacks,
        static fn ($fb) => ($fb['conceito_final']['code'] ?? null) === $filterCode,
    );
}
usort($visibleFeedbacks, static function ($a, $b) use ($sortAsc): int {
    $orderMap = ['AE' => 5, 'O' => 4, 'B' => 3, 'ANS' => 2, 'I' => 1];
    $va = $orderMap[$a['conceito_final']['code'] ?? ''] ?? 0;
    $vb = $orderMap[$b['conceito_final']['code'] ?? ''] ?? 0;
    return $sortAsc ? $va - $vb : $vb - $va;
});

$borderClass = ['ae' => 'app-feedback-card--ae', 'o' => 'app-feedback-card--o', 'b' => 'app-feedback-card--b', 'ans' => 'app-feedback-card--ans', 'i' => 'app-feedback-card--i'];
?>
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Feedbacks e Avaliações';
        $headingSubtitle = 'Avaliações dos professores sobre seus projetos';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <?php if ($feedbacks !== []): ?>
        <div class="app-filters-card">
            <div class="app-filters-card__title">
                <?= lucide_tag('filter', 'app-filters-card__title-icon') ?>
                <span>Filtrar por conceito</span>
                <a href="?conceito=<?= e($filterCode) ?>&ordem=<?= $sortAsc ? 'desc' : 'asc' ?>"
                   class="app-feedback-sort ms-auto">
                    <?= lucide_tag('arrow-down-up', 'app-feedback-sort__icon') ?>
                    <?= $sortAsc ? 'Pior → Melhor' : 'Melhor → Pior' ?>
                </a>
            </div>
            <div class="app-feedback-filters">
                <a href="?conceito=todas&ordem=<?= $sortAsc ? 'asc' : 'desc' ?>"
                   class="app-feedback-filter-btn <?= $filterCode === 'todas' ? 'app-feedback-filter-btn--active' : '' ?>">
                    Todas (<?= count($feedbacks) ?>)
                </a>
                <?php foreach (conceitos_senac() as $conceito): ?>
                    <?php
                    $code = $conceito['code'];
                    $countForCode = count(array_filter($feedbacks, static fn ($f) => ($f['conceito_final']['code'] ?? null) === $code));
                    if ($countForCode === 0) {
                        continue;
                    }
                    $isActive = $filterCode === $code;
                    ?>
                    <a href="?conceito=<?= e($code) ?>&ordem=<?= $sortAsc ? 'asc' : 'desc' ?>"
                       class="app-feedback-filter-btn app-feedback-filter-btn--<?= e($conceito['modifier']) ?> <?= $isActive ? 'app-feedback-filter-btn--active' : '' ?>">
                        <strong><?= e($code) ?></strong> — <?= e($conceito['label']) ?> (<?= $countForCode ?>)
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($feedbacks === []): ?>
            <div class="app-feedback-empty">
                <?= lucide_tag('message-square', 'app-feedback-empty__icon') ?>
                <h2 class="app-feedback-empty__title">Nenhum feedback ainda</h2>
                <p class="app-feedback-empty__text mb-0">
                    Os feedbacks dos professores aparecerão aqui após a avaliação dos seus projetos.
                </p>
            </div>
        <?php elseif (count($visibleFeedbacks) === 0): ?>
            <div class="app-feedback-empty">
                <?= lucide_tag('message-square', 'app-feedback-empty__icon') ?>
                <h2 class="app-feedback-empty__title">Nenhum feedback com esse conceito</h2>
                <p class="app-feedback-empty__text mb-0">
                    Tente selecionar outro conceito para ver mais feedbacks.
                </p>
            </div>
        <?php else: ?>
            <div class="app-feedback-list">
                <?php foreach ($visibleFeedbacks as $fb): ?>
                    <?php
                    $conceitoFinal = $fb['conceito_final'] ?? null;
                    $modifier = $conceitoFinal['modifier'] ?? '';
                    ?>
                    <article class="app-feedback-card app-feedback-card--<?= e($modifier) ?>">

                        <div class="app-feedback-card__header">
                            <span class="app-feedback-card__msg-icon" aria-hidden="true">
                                <?= lucide_tag('message-square', 'app-feedback-card__msg-icon-svg') ?>
                            </span>
                            <div class="app-feedback-card__header-body">
                                <h2 class="app-feedback-card__project-title"><?= e($fb['projeto_titulo'] ?? '') ?></h2>
                                <p class="app-feedback-card__meta mb-0">
                                    Avaliado por: <strong><?= e(trim(($fb['nome_civil_nome'] ?? '') . ' ' . ($fb['nome_civil_sobrenome'] ?? ''))) ?></strong>
                                    &nbsp;·&nbsp; <?= e(format_date($fb['data'] ?? null)) ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($conceitoFinal !== null): ?>
                            <div class="app-feedback-card__conceito-box">
                                <p class="app-feedback-card__conceito-label mb-0">Conceito de avaliação:</p>
                                <?php $conceito = $conceitoFinal; ?>
                                <?php require dirname(__DIR__) . '/partials/app-conceito-badge.php'; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($fb['rubrica'])): ?>
                            <div class="app-feedback-card__section">
                                <h3 class="app-feedback-card__section-title">Rubrica de avaliação</h3>
                                <div class="app-feedback-card__rubrica">
                                    <?php foreach ($fb['rubrica'] as $r): ?>
                                        <?php $conceito = nota_para_conceito((float) $r['conceito']); ?>
                                        <div class="app-feedback-rubrica-item app-feedback-rubrica-item--<?= e($conceito['modifier']) ?>">
                                            <p class="app-feedback-rubrica-item__criterio mb-2"><?= e($r['criterio']) ?></p>
                                            <div class="d-flex justify-content-center">
                                                <?php require dirname(__DIR__) . '/partials/app-conceito-badge.php'; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($fb['descricao'])): ?>
                            <div class="app-feedback-card__comment">
                                <h3 class="app-feedback-card__comment-title">
                                    <?= lucide_tag('message-square', 'app-feedback-card__comment-icon') ?>
                                    Comentário do professor
                                </h3>
                                <p class="app-feedback-card__comment-text mb-0"><?= e($fb['descricao']) ?></p>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
