<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page app-page--narrow">
    <div class="app-page-stack">
        <?php
        $headingTitle = 'Submeter novo projeto';
        $headingSubtitle = 'Preencha os dados do seu projeto integrador';
        $backUrl = '/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <form method="post" action="/submeter" enctype="multipart/form-data" class="app-form-card app-form-stack"
              data-submeter-form autocomplete="off">
            <?= csrf_field() ?>

            <div class="app-field">
                <label for="titulo" class="app-field__label">
                    Título do projeto <span class="text-senac-error">*</span>
                </label>
                <input type="text" id="titulo" name="titulo" class="app-field__input" required autocomplete="off"
                       placeholder="Digite o título do projeto" value="<?= old('titulo') ?>">
            </div>

            <div class="app-field">
                <label for="descricao" class="app-field__label">
                    Descrição resumida <span class="text-senac-error">*</span>
                </label>
                <textarea id="descricao" name="descricao" class="app-field__textarea" rows="5" required autocomplete="off"
                          maxlength="500" placeholder="Descreva o projeto em até 500 caracteres"
                          data-char-count="descricao-count"><?= old('descricao') ?></textarea>
                <div class="app-field__meta">
                    <p class="app-field__helper mb-0">
                        Inclua informações sobre objetivos, tecnologias utilizadas e resultados
                    </p>
                    <p class="app-field__counter mb-0" id="descricao-count" aria-live="polite">0/500</p>
                </div>
            </div>

            <?php $turmas = $turmas ?? []; ?>
            <div class="app-field">
                <label for="cod_turma" class="app-field__label">
                    Turma <span class="text-senac-error">*</span>
                </label>
                <select id="cod_turma" name="cod_turma" class="app-field__input" required autocomplete="off"<?= $turmas === [] ? ' disabled' : '' ?>>
                    <?php if ($turmas === []): ?>
                        <option value="">Nenhuma turma disponível</option>
                    <?php else: ?>
                        <option value="" disabled<?= old('cod_turma') === '' ? ' selected' : '' ?>>
                            Selecione a turma
                        </option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?= e($turma['cod_turma']) ?>"
                                <?= old('cod_turma', count($turmas) === 1 ? (string) $turma['cod_turma'] : '') === (string) $turma['cod_turma'] ? 'selected' : '' ?>>
                                <?= e(turma_display_label($turma)) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if ($turmas === []): ?>
                    <p class="app-field__helper text-senac-error mb-0" role="alert">
                        Nenhuma turma ativa encontrada para sua matrícula.
                    </p>
                <?php endif; ?>
            </div>

            <div class="app-field">
                <label for="link_repo_git" class="app-field__label">
                    Link do repositório Git <span class="text-senac-error">*</span>
                </label>
                <input type="url" id="link_repo_git" name="link_repo_git" class="app-field__input" required autocomplete="off"
                       placeholder="https://github.com/usuario/projeto" value="<?= old('link_repo_git') ?>">
                <p class="app-field__helper mb-0">URL completa do repositório Git do projeto</p>
            </div>

            <div class="app-field">
                <label for="tecnologias" class="app-field__label">
                    Tecnologias utilizadas <span class="text-senac-error">*</span>
                </label>
                <input type="text" id="tecnologias" name="tecnologias" class="app-field__input" required autocomplete="off"
                       placeholder="Ex: Python, Django, PostgreSQL, React" value="<?= old('tecnologias') ?>">
                <p class="app-field__helper mb-0">
                    Liste as principais tecnologias, frameworks e ferramentas utilizadas
                </p>
            </div>

            <div class="app-field">
                <span class="app-field__label d-block">
                    Anexos do projeto <span class="text-senac-error">*</span>
                </span>
                <?php
                $attachmentRequired = true;
                $allowMultiple = true;
                require dirname(__DIR__) . '/partials/app-attachment-rows.php';
                ?>
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
                <button type="submit" class="app-action-btn app-action-btn--primary"<?= $turmas === [] ? ' disabled' : '' ?>>
                    Enviar projeto
                </button>
            </div>
        </form>
    </div>
</div>
