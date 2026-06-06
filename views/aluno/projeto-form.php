<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = $pageTitle;
    $subtitle = 'Atualize as informações do seu projeto';
    $backUrl = '/projetos';
    require dirname(__DIR__) . '/partials/page-header.php';
    $p = $project;
    ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-control" required value="<?= e($p['titulo']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome do grupo (opcional)</label>
                    <input type="text" name="nome_grupo" class="form-control" value="<?= e($p['nome_grupo'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição *</label>
                    <textarea name="descricao" class="form-control" rows="4" required><?= e($p['descricao']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Link do repositório Git</label>
                    <input type="url" name="link_repo_git" class="form-control" value="<?= e($p['link_repo_git']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tecnologias</label>
                    <input type="text" name="tecnologias" class="form-control" value="<?= e($p['tecnologias']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Novos anexos (opcional)</label>
                    <?php
                    $attachmentRequired = false;
                    $allowMultiple = true;
                    require dirname(__DIR__) . '/partials/app-attachment-rows.php';
                    ?>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="publico" value="1" id="publico" <?= !empty($p['publico']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="publico">Permitir visualização no portfólio público</label>
                </div>
                <button type="submit" class="btn btn-senac-primary">Salvar alterações</button>
            </form>
        </div>
    </div>
</div>
