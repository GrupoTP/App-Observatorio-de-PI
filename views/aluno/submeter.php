<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Submeter Novo Projeto';
    $subtitle = 'Preencha os dados do seu projeto integrador';
    $backUrl = '/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="/submeter" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Título do projeto *</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome do grupo (opcional)</label>
                    <input type="text" name="nome_grupo" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição *</label>
                    <textarea name="descricao" class="form-control" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Link do repositório (GitHub)</label>
                    <input type="url" name="link_github" class="form-control" placeholder="https://github.com/...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tecnologias utilizadas</label>
                    <input type="text" name="tecnologias" class="form-control" placeholder="PHP, MySQL, Bootstrap">
                </div>
                <div class="mb-3">
                    <label class="form-label">Arquivo do projeto (PDF, ZIP ou imagem)</label>
                    <input type="file" name="arquivo" class="form-control" accept=".pdf,.zip,.png,.jpg,.jpeg">
                    <div class="form-text">Máximo 10 MB</div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="publico" value="1" id="publico">
                    <label class="form-check-label" for="publico">Exibir no portfólio após aprovação</label>
                </div>
                <button type="submit" class="btn btn-senac-primary btn-lg">Enviar projeto</button>
            </form>
        </div>
    </div>
</div>
