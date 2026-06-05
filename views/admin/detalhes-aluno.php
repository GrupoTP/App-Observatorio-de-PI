<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = user_display_name($user);
    $subtitle = $user['email_institucional'];
    $backUrl = '/admin/alunos';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <h2 class="h6 fw-bold">Dados acadêmicos</h2>
                <?php if ($turma): ?>
                    <p class="small mb-1"><strong>Curso:</strong> <?= e($turma['nome_curso']) ?></p>
                    <p class="small mb-0"><strong>Turma:</strong> <?= e($turma['nome_turma']) ?> — <?= e($turma['modulo']) ?></p>
                <?php else: ?>
                    <p class="text-muted small">Sem matrícula ativa</p>
                <?php endif; ?>
            </div></div>
        </div>
        <div class="col-md-8">
            <h2 class="h6 fw-bold mb-3">Projetos</h2>
            <?php foreach ($projects as $project): ?>
                <?php $showActions = false; require dirname(__DIR__) . '/partials/project-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
