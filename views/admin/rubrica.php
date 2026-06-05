<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Configurar Rubrica';
    $subtitle = 'Critérios de avaliação por turma';
    $backUrl = '/admin/pi';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <form method="get" class="mb-3">
        <label class="form-label">Turma</label>
        <div class="input-group">
            <select name="turma" class="form-select" onchange="this.form.submit()">
                <?php foreach ($turmas as $t): ?>
                    <option value="<?= e($t['cod_turma']) ?>" <?= $codTurma === $t['cod_turma'] ? 'selected' : '' ?>>
                        <?= e($t['nome_curso']) ?> — <?= e($t['nome_turma']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <form method="post" action="/admin/pi/rubrica">
        <?= csrf_field() ?>
        <input type="hidden" name="cod_turma" value="<?= e($codTurma) ?>">
        <div class="card"><div class="card-body" id="rubricaRows">
            <?php if ($criteria === []): ?>
                <?php $criteria = [['nome' => 'Funcionalidade', 'peso' => 1], ['nome' => 'Documentação', 'peso' => 1], ['nome' => 'Criatividade', 'peso' => 1]]; ?>
            <?php endif; ?>
            <?php foreach ($criteria as $i => $c): ?>
                <div class="row g-2 mb-2 rubrica-row">
                    <div class="col-md-7"><input name="nome[]" class="form-control" value="<?= e($c['nome']) ?>" placeholder="Critério" required></div>
                    <div class="col-md-3"><input name="peso[]" type="number" step="0.1" class="form-control" value="<?= e((string) $c['peso']) ?>" placeholder="Peso"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addRubricaRow()">+ Critério</button>
            <button type="submit" class="btn btn-senac-primary float-end">Salvar rubrica</button>
        </div></div>
    </form>
</div>
<script>
function addRubricaRow() {
    const container = document.getElementById('rubricaRows');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 rubrica-row';
    row.innerHTML = '<div class="col-md-7"><input name="nome[]" class="form-control" placeholder="Critério" required></div><div class="col-md-3"><input name="peso[]" type="number" step="0.1" class="form-control" value="1" placeholder="Peso"></div>';
    container.appendChild(row);
}
</script>
