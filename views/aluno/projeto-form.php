<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php $p = $project; ?>
<div class="app-page app-page--narrow">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Editar projeto';
        $headingSubtitle = 'Atualize as informações do seu projeto integrador';
        $backUrl = '/projetos';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <form method="post"
              action="<?= e($action) ?>"
              enctype="multipart/form-data"
              class="app-form-card app-form-stack"
              data-projeto-edit-form
              autocomplete="off">
            <?= csrf_field() ?>

            <div class="app-field">
                <label for="titulo" class="app-field__label">
                    Título do projeto <span class="text-senac-error">*</span>
                </label>
                <input type="text"
                       id="titulo"
                       name="titulo"
                       class="app-field__input"
                       required
                       autocomplete="off"
                       placeholder="Digite o título do projeto"
                       value="<?= e($p['titulo']) ?>">
            </div>

            <div class="app-field">
                <label for="nome_grupo" class="app-field__label">Nome do grupo</label>
                <input type="text"
                       id="nome_grupo"
                       name="nome_grupo"
                       class="app-field__input"
                       autocomplete="off"
                       placeholder="Ex.: Grupo Alpha"
                       value="<?= e($p['nome_grupo'] ?? '') ?>">
                <p class="app-field__helper mb-0">Opcional — use se o projeto foi desenvolvido em equipe</p>
            </div>

            <div class="app-field">
                <label for="descricao" class="app-field__label">
                    Descrição resumida <span class="text-senac-error">*</span>
                </label>
                <textarea id="descricao"
                          name="descricao"
                          class="app-field__textarea"
                          rows="5"
                          required
                          autocomplete="off"
                          maxlength="500"
                          placeholder="Descreva o projeto em até 500 caracteres"
                          data-char-count="descricao-count"><?= e($p['descricao']) ?></textarea>
                <div class="app-field__meta">
                    <p class="app-field__helper mb-0">
                        Inclua informações sobre objetivos, tecnologias utilizadas e resultados
                    </p>
                    <p class="app-field__counter mb-0" id="descricao-count" aria-live="polite">0/500</p>
                </div>
            </div>

            <div class="app-field">
                <span class="app-field__label d-block">Turma</span>
                <p class="app-field__readonly mb-0"><?= e(turma_display_label($p)) ?></p>
                <p class="app-field__helper mb-0">A turma não pode ser alterada após o envio do projeto</p>
            </div>

            <div class="app-field">
                <label for="link_repo_git" class="app-field__label">
                    Link do repositório Git <span class="text-senac-error">*</span>
                </label>
                <input type="url"
                       id="link_repo_git"
                       name="link_repo_git"
                       class="app-field__input"
                       required
                       autocomplete="off"
                       placeholder="https://github.com/usuario/projeto"
                       value="<?= e($p['link_repo_git']) ?>">
                <p class="app-field__helper mb-0">URL completa do repositório Git do projeto</p>
            </div>

            <div class="app-field">
                <label for="tecnologias" class="app-field__label">
                    Tecnologias utilizadas <span class="text-senac-error">*</span>
                </label>
                <input type="text"
                       id="tecnologias"
                       name="tecnologias"
                       class="app-field__input"
                       required
                       autocomplete="off"
                       placeholder="Ex: Python, Django, PostgreSQL, React"
                       value="<?= e($p['tecnologias']) ?>">
                <p class="app-field__helper mb-0">
                    Liste as principais tecnologias, frameworks e ferramentas utilizadas
                </p>
            </div>

            <div class="app-field">
                <span class="app-field__label d-block">Anexos do projeto</span>
                <?php
                $attachments = $attachments ?? [];
                require dirname(__DIR__) . '/partials/app-existing-attachments.php';
                ?>
                <p class="app-field__helper text-senac-error mb-0 d-none"
                   role="alert"
                   data-projeto-edit-attachment-error>
                    O projeto deve ter ao menos um anexo.
                </p>
                <p class="app-field__helper mb-2 mt-3">Adicionar novos anexos (opcional)</p>
                <?php
                $attachmentRequired = false;
                $allowMultiple = true;
                require dirname(__DIR__) . '/partials/app-attachment-rows.php';
                ?>
            </div>

            <div class="app-field app-field--checkbox">
                <input type="checkbox"
                       name="publico"
                       value="1"
                       id="publico"
                       class="app-field__checkbox"
                    <?= !empty($p['publico']) ? 'checked' : '' ?>>
                <label for="publico" class="app-field__checkbox-label">
                    Autorizar empresas parceiras a visualizar este projeto
                </label>
            </div>

            <div class="app-form-actions">
                <a href="/projetos" class="app-action-btn app-action-btn--secondary">Cancelar</a>
                <button type="submit" class="app-action-btn app-action-btn--primary">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>
