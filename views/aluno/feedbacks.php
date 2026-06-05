<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Feedbacks dos Professores';
    $subtitle = 'Avaliações e comentários recebidos';
    $backUrl = '/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <?php if ($feedbacks === []): ?>
        <div class="alert alert-info">Você ainda não recebeu feedbacks.</div>
    <?php else: ?>
        <?php foreach ($feedbacks as $fb): ?>
            <div class="card project-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                        <h2 class="h6 fw-bold mb-0"><?= e($fb['projeto_titulo']) ?></h2>
                        <?php if ($fb['media'] !== null): ?>
                            <span class="badge bg-success fs-6">Média: <?= e((string) $fb['media']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="small text-muted mb-2">
                        Professor(a): <?= e(trim(($fb['nome_civil_nome'] ?? '') . ' ' . ($fb['nome_civil_sobrenome'] ?? ''))) ?>
                        — <?= e(format_datetime($fb['data'])) ?>
                    </p>
                    <?php if (!empty($fb['rubrica'])): ?>
                        <ul class="list-unstyled small mb-2">
                            <?php foreach ($fb['rubrica'] as $r): ?>
                                <li><strong><?= e($r['criterio']) ?>:</strong> <?= e($r['conceito']) ?>/10</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <p class="mb-0"><?= e($fb['descricao']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
