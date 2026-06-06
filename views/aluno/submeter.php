<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page app-page--narrow">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Submeter novo projeto';
        $headingSubtitle = 'Preencha os dados do seu projeto integrador';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <form method="post" action="/submeter" enctype="multipart/form-data" class="app-form-card app-form-stack" novalidate>
            <?= csrf_field() ?>

            <div class="app-field">
                <label for="titulo" class="app-field__label">
                    Título do projeto <span class="text-senac-error">*</span>
                </label>
                <input type="text" id="titulo" name="titulo" class="app-field__input" required
                       placeholder="Digite o título do projeto" value="<?= old('titulo') ?>">
            </div>

            <div class="app-field">
                <label for="descricao" class="app-field__label">
                    Descrição resumida <span class="text-senac-error">*</span>
                </label>
                <textarea id="descricao" name="descricao" class="app-field__textarea" rows="5" required
                          maxlength="500" placeholder="Descreva o projeto em até 500 caracteres"
                          data-char-count="descricao-count"><?= old('descricao') ?></textarea>
                <div class="app-field__meta">
                    <p class="app-field__helper mb-0">
                        Inclua informações sobre objetivos, tecnologias utilizadas e resultados
                    </p>
                    <p class="app-field__counter mb-0" id="descricao-count" aria-live="polite">0/500</p>
                </div>
            </div>

            <div class="app-field">
                <label for="turma" class="app-field__label">
                    Turma <span class="text-senac-error">*</span>
                </label>
                <select id="turma" class="app-field__input" disabled aria-readonly="true">
                    <option selected><?= e($turmaLabel ?? '—') ?></option>
                </select>
                <?php if ($turma === null): ?>
                    <p class="app-field__helper text-senac-error mb-0" role="alert">
                        Nenhuma turma ativa encontrada para sua matrícula.
                    </p>
                <?php endif; ?>
            </div>

            <div class="app-field">
                <label for="link_github" class="app-field__label">
                    Link do repositório GitHub <span class="text-senac-error">*</span>
                </label>
                <input type="url" id="link_github" name="link_github" class="app-field__input" required
                       placeholder="https://github.com/usuario/projeto" value="<?= old('link_github') ?>">
                <p class="app-field__helper mb-0">URL completa do repositório do projeto no GitHub</p>
            </div>

            <div class="app-field">
                <label for="tecnologias" class="app-field__label">
                    Tecnologias utilizadas <span class="text-senac-error">*</span>
                </label>
                <input type="text" id="tecnologias" name="tecnologias" class="app-field__input" required
                       placeholder="Ex: Python, Django, PostgreSQL, React" value="<?= old('tecnologias') ?>">
                <p class="app-field__helper mb-0">
                    Liste as principais tecnologias, frameworks e ferramentas utilizadas
                </p>
            </div>

            <div class="app-field">
                <label class="app-field__label" for="arquivo">
                    Anexo do projeto <span class="text-senac-error">*</span>
                </label>
                <div class="app-file-upload" data-file-upload>
                    <input type="file" name="arquivo" id="arquivo" class="app-file-upload__input" required
                           accept=".pdf,.zip,.png,.jpg,.jpeg" data-file-input>
                    <label for="arquivo" class="app-file-upload__label">
                        <?= lucide_tag('upload', 'app-file-upload__icon') ?>
                        <span class="app-file-upload__placeholder" data-file-placeholder>
                            <span class="app-file-upload__title">Arraste ou clique para anexar</span>
                            <span class="app-file-upload__hint">Formatos: PDF, ZIP, PNG ou JPG (máx. 10 MB)</span>
                        </span>
                        <span class="app-file-upload__selected d-none" data-file-selected>
                            <span class="app-file-upload__title" data-file-name></span>
                            <span class="app-file-upload__hint" data-file-size></span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="app-field app-field--checkbox">
                <input type="checkbox" name="publico" value="1" id="publico" class="app-field__checkbox"
                    <?= old('publico') === '1' ? 'checked' : '' ?>>
                <label for="publico" class="app-field__checkbox-label">
                    Autorizar empresas parceiras a visualizar este projeto
                </label>
            </div>

            <div class="app-form-actions">
                <a href="/dashboard" class="app-action-btn app-action-btn--secondary">Cancelar</a>
                <button type="submit" class="app-action-btn app-action-btn--primary"<?= $turma === null ? ' disabled' : '' ?>>
                    Enviar projeto
                </button>
            </div>
        </form>
    </div>
</div>
