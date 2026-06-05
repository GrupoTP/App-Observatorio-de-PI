<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Prazos e Entregas';
    $subtitle = 'Acompanhe os prazos das suas turmas';
    $backUrl = '/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <?php if ($turmas === []): ?>
        <div class="alert alert-info">Nenhuma matrícula ativa encontrada.</div>
    <?php else: ?>
        <div class="table-responsive card">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Curso / Turma</th>
                        <th>Módulo</th>
                        <th>Prazo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turmas as $t): ?>
                        <?php
                        $sit = $t['situacao_projeto'] ?? null;
                        $statusLabel = $sit === null ? 'Pendente' : situacao_label((string) $sit);
                        $badge = $sit === 'avaliado' ? 'bg-success' : ($sit === 'enviado' ? 'bg-warning text-dark' : 'bg-secondary');
                        ?>
                        <tr>
                            <td><?= e($t['nome_curso']) ?><br><small class="text-muted"><?= e($t['nome_turma']) ?></small></td>
                            <td><?= e($t['modulo']) ?></td>
                            <td><?= e(format_datetime($t['prazo_projetos'])) ?></td>
                            <td><span class="badge <?= $badge ?>"><?= e($statusLabel) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
