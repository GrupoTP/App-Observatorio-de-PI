<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Gerador de Currículo';
    $subtitle = 'Monte seu currículo com base no portfólio';
    $backUrl = '/portfolio';
    require dirname(__DIR__) . '/partials/page-header.php';
    $c = $curriculo;
    ?>

    <form method="post" action="/curriculo">
        <?= csrf_field() ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Resumo profissional</label>
                    <textarea name="resumo" class="form-control" rows="3"><?= e($c['resumo'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Formação</label>
                    <textarea name="formacao" class="form-control" rows="3"><?= e($c['formacao'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Experiência</label>
                    <textarea name="experiencia" class="form-control" rows="4"><?= e($c['experiencia'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Habilidades</label>
                    <textarea name="habilidades" class="form-control" rows="2"><?= e($c['habilidades'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contato</label>
                    <input type="text" name="contato" class="form-control" value="<?= e($c['contato'] ?? ($user['email_institucional'] ?? '')) ?>">
                </div>
                <button type="submit" class="btn btn-senac-primary">Salvar currículo</button>
            </div>
        </div>
    </form>

    <?php if (!empty($c)): ?>
        <div class="card border-primary">
            <div class="card-header bg-senac-blue text-white">Pré-visualização</div>
            <div class="card-body">
                <h2 class="h5"><?= e(user_display_name($user)) ?></h2>
                <p><?= e($c['resumo'] ?? '') ?></p>
                <h3 class="h6 text-senac-blue">Formação</h3>
                <p class="small"><?= nl2br(e($c['formacao'] ?? '')) ?></p>
                <h3 class="h6 text-senac-blue">Experiência</h3>
                <p class="small"><?= nl2br(e($c['experiencia'] ?? '')) ?></p>
                <h3 class="h6 text-senac-blue">Habilidades</h3>
                <p class="small"><?= e($c['habilidades'] ?? '') ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
