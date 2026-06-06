<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = $pageTitle;
    $subtitle = '';
    $backUrl = '/admin/pi';
    require dirname(__DIR__) . '/partials/page-header.php';
    $p = $project;
    $coauthorIds = $coauthorIds ?? [];
    ?>

    <form method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>
        <div class="card"><div class="card-body row g-3">
            <div class="col-md-6"><label class="form-label">Nome do grupo</label>
                <input name="nome_grupo" class="form-control" value="<?= e($p['nome_grupo'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Título do PI *</label>
                <input name="titulo" class="form-control" required value="<?= e($p['titulo'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Turma *</label>
                <select name="cod_turma" class="form-select" required>
                    <?php foreach ($turmas as $t): ?>
                        <option value="<?= e($t['cod_turma']) ?>" <?= ($p['cod_turma'] ?? '') === $t['cod_turma'] ? 'selected' : '' ?>>
                            <?= e($t['nome_curso']) ?> — <?= e($t['nome_turma']) ?>
                        </option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-6"><label class="form-label">Aluno submissor *</label>
                <select name="id_submissor" class="form-select" required>
                    <?php foreach ($alunos as $a): ?>
                        <option value="<?= e($a['id_usuario']) ?>" <?= ($p['id_usuario_submissor'] ?? '') === $a['id_usuario'] ? 'selected' : '' ?>>
                            <?= e(user_display_name($a)) ?>
                        </option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-12"><label class="form-label">Coautores</label>
                <select name="membros[]" class="form-select" multiple size="4">
                    <?php foreach ($alunos as $a): ?>
                        <option value="<?= e($a['id_usuario']) ?>" <?= in_array($a['id_usuario'], $coauthorIds ?? [], true) ? 'selected' : '' ?>>
                            <?= e(user_display_name($a)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Segure Ctrl para selecionar vários</div></div>
            <div class="col-12"><label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"><?= e($p['descricao'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label">Repositório Git</label>
                <input name="link_repo_git" class="form-control" value="<?= e($p['link_repo_git'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">Status</label>
                <select name="situacao_projeto" class="form-select">
                    <option value="em-andamento">Em andamento</option>
                    <option value="enviado" <?= ($p['situacao_projeto'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                    <option value="avaliado" <?= ($p['situacao_projeto'] ?? '') === 'avaliado' ? 'selected' : '' ?>>Avaliado</option>
                </select></div>
            <div class="col-12"><button type="submit" class="btn btn-senac-primary">Salvar grupo PI</button></div>
        </div></div>
    </form>
</div>
