<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Cadastrar Usuário';
    $subtitle = 'Novo aluno, professor ou coordenador';
    $backUrl = '/admin/alunos';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="/admin/alunos/novo">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nome</label><input name="nome" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Sobrenome</label><input name="sobrenome" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">E-mail institucional</label><input type="email" name="email" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">E-mail pessoal</label><input type="email" name="email_pessoal" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Perfil</label>
                        <select name="role" class="form-select">
                            <option value="aluno">Aluno</option>
                            <option value="professor">Professor</option>
                            <option value="coordenador">Coordenador / Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">Senha inicial</label><input type="password" name="senha" class="form-control" value="senac123"></div>
                    <div class="col-12"><label class="form-label">Turma (aluno/professor)</label>
                        <select name="cod_turma" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($turmas as $t): ?>
                                <option value="<?= e($t['cod_turma']) ?>"><?= e($t['nome_curso']) ?> — <?= e($t['nome_turma']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-senac-primary mt-4">Cadastrar</button>
            </form>
        </div>
    </div>
</div>
