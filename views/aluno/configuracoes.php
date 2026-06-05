<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Configurações';
    $subtitle = 'Perfil e preferências da conta';
    $backUrl = \App\Auth\SessionAuth::isAluno() ? '/dashboard' : '/admin/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <form method="post" action="/configuracoes">
        <?= csrf_field() ?>
        <div class="card mb-4">
            <div class="card-header fw-semibold">Dados pessoais</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome_civil_nome" class="form-control" required value="<?= e($user['nome_civil_nome']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sobrenome</label>
                    <input type="text" name="nome_civil_sobrenome" class="form-control" required value="<?= e($user['nome_civil_sobrenome']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome social (opcional)</label>
                    <input type="text" name="nome_social_nome" class="form-control" value="<?= e($user['nome_social_nome'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sobrenome social</label>
                    <input type="text" name="nome_social_sobrenome" class="form-control" value="<?= e($user['nome_social_sobrenome'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">E-mail institucional</label>
                    <input type="email" class="form-control" disabled value="<?= e($user['email_institucional']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">E-mail pessoal</label>
                    <input type="email" name="email_pessoal" class="form-control" value="<?= e($user['email_pessoal']) ?>">
                </div>
            </div>
        </div>

        <?php if ($aluno !== null): ?>
        <div class="card mb-4">
            <div class="card-header fw-semibold">Portfólio</div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="portfolio_publico" value="1" id="portfolio_publico"
                        <?= !empty($aluno['portfolio_publico']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="portfolio_publico">Portfólio público</label>
                </div>
                <div class="mt-3">
                    <label class="form-label">Notificações</label>
                    <select name="notificacoes" class="form-select">
                        <option value="">Nenhuma</option>
                        <option value="email" <?= ($aluno['notificacoes'] ?? '') === 'email' ? 'selected' : '' ?>>E-mail</option>
                    </select>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header fw-semibold">Alterar senha</div>
            <div class="card-body">
                <label class="form-label">Nova senha (deixe em branco para manter)</label>
                <input type="password" name="nova_senha" class="form-control" autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-senac-primary">Salvar configurações</button>
    </form>
</div>
