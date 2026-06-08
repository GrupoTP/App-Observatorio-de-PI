<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $p = $project;
        $coauthorIds = $coauthorIds ?? [];
        $isEditing = $p !== null;
        $headingTitle    = $isEditing ? 'Editar Grupo de PI' : 'Novo Grupo de PI';
        $headingSubtitle = $isEditing ? 'Atualize as informações do grupo' : 'Crie um novo grupo de Projeto Integrador';
        ?>

        <?php /* ─── Page heading ─── */ ?>
        <div class="app-pi-header">
            <div class="app-pi-header__text">
                <a href="/admin/pi" class="app-page-heading__back" aria-label="Voltar">
                    <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
                </a>
                <div>
                    <h1 class="app-page-heading__title"><?= e($headingTitle) ?></h1>
                    <p class="app-page-heading__subtitle"><?= e($headingSubtitle) ?></p>
                </div>
            </div>
        </div>

        <form method="post" action="<?= e($action) ?>" class="app-page-stack" id="pi-form">
            <?= csrf_field() ?>

            <?php /* ─── Section 1: Group info ─── */ ?>
            <div class="app-form-card">
                <div class="app-pi-form-section-title">
                    <?= lucide_tag('users', 'app-pi-detalhes__section-icon') ?>
                    <h2>Informações do Grupo</h2>
                </div>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="nome_grupo">Nome do Grupo</label>
                        <input id="nome_grupo"
                               name="nome_grupo"
                               type="text"
                               class="app-field__input"
                               placeholder="Ex: Grupo Alpha"
                               value="<?= e($p['nome_grupo'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label class="app-field__label" for="prazo_especial">Prazo de Entrega</label>
                        <input id="prazo_especial"
                               name="prazo_especial"
                               type="date"
                               class="app-field__input"
                               value="<?= e($p['prazo_especial'] ?? '') ?>">
                    </div>
                </div>

                <div class="app-field">
                    <label class="app-field__label" for="titulo">Título do Projeto *</label>
                    <input id="titulo"
                           name="titulo"
                           type="text"
                           class="app-field__input"
                           placeholder="Ex: Sistema de Gestão Escolar"
                           required
                           value="<?= e($p['titulo'] ?? '') ?>">
                </div>

                <div class="app-field">
                    <label class="app-field__label" for="cod_turma">Turma *</label>
                    <select id="cod_turma" name="cod_turma" class="app-field__input" required>
                        <?php foreach ($turmas as $t): ?>
                        <option value="<?= e($t['cod_turma']) ?>"
                            <?= ($p['cod_turma'] ?? '') === $t['cod_turma'] ? 'selected' : '' ?>>
                            <?= e($t['nome_curso']) ?> — <?= e($t['nome_turma']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="app-field">
                    <label class="app-field__label" for="id_submissor">Aluno Submissor *</label>
                    <select id="id_submissor" name="id_submissor" class="app-field__input" required>
                        <?php foreach ($alunos as $a): ?>
                        <option value="<?= e($a['id_usuario']) ?>"
                            <?= ($p['id_usuario_submissor'] ?? '') === $a['id_usuario'] ? 'selected' : '' ?>>
                            <?= e(user_display_name($a)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="link_repo_git">Repositório Git</label>
                        <input id="link_repo_git"
                               name="link_repo_git"
                               type="url"
                               class="app-field__input"
                               placeholder="https://github.com/..."
                               value="<?= e($p['link_repo_git'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label class="app-field__label" for="situacao_projeto">Status</label>
                        <select id="situacao_projeto" name="situacao_projeto" class="app-field__input">
                            <option value="em-andamento" <?= ($p['situacao_projeto'] ?? 'em-andamento') === 'em-andamento' ? 'selected' : '' ?>>Em andamento</option>
                            <option value="enviado"      <?= ($p['situacao_projeto'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="avaliado"     <?= ($p['situacao_projeto'] ?? '') === 'avaliado' ? 'selected' : '' ?>>Avaliado</option>
                        </select>
                    </div>
                </div>

                <div class="app-field">
                    <label class="app-field__label" for="descricao">Descrição</label>
                    <textarea id="descricao"
                              name="descricao"
                              class="app-field__input app-field__textarea"
                              rows="3"
                              placeholder="Descreva o projeto..."><?= e($p['descricao'] ?? '') ?></textarea>
                </div>
            </div>

            <?php /* ─── Section 2: Members (pill selector) ─── */ ?>
            <div class="app-form-card" id="members-section">
                <div class="app-pi-form-section-title">
                    <?= lucide_tag('user-plus', 'app-pi-detalhes__section-icon') ?>
                    <h2>Membros do Grupo (<span id="member-count"><?= count($coauthorIds) ?></span>)</h2>
                </div>

                <?php /* Selected members pills */ ?>
                <div class="app-pi-form__pills-label">Alunos Selecionados (coautores):</div>
                <div class="app-pi-form__pills" id="selected-pills">
                    <?php foreach ($alunos as $a):
                        if (!in_array($a['id_usuario'], $coauthorIds, true)) continue;
                    ?>
                    <span class="app-pi-form__pill" data-id="<?= e($a['id_usuario']) ?>">
                        <input type="hidden" name="membros[]" value="<?= e($a['id_usuario']) ?>">
                        <?= e(user_display_name($a)) ?>
                        <button type="button" class="app-pi-form__pill-remove" onclick="removeMember(this)">
                            <?= lucide_tag('x', '') ?>
                        </button>
                    </span>
                    <?php endforeach; ?>

                    <span class="app-pi-form__pills-empty" id="pills-empty"
                          style="<?= !empty($coauthorIds) ? 'display:none' : '' ?>">
                        Nenhum coautor selecionado ainda
                    </span>
                </div>

                <?php /* Available students list */ ?>
                <div class="app-pi-form__available-label">Adicionar Alunos:</div>
                <div class="app-pi-form__available" id="available-list">
                    <?php foreach ($alunos as $a): ?>
                    <button type="button"
                            class="app-pi-form__available-item <?= in_array($a['id_usuario'], $coauthorIds, true) ? 'app-pi-form__available-item--hidden' : '' ?>"
                            data-id="<?= e($a['id_usuario']) ?>"
                            data-name="<?= e(user_display_name($a)) ?>"
                            onclick="addMember(this)">
                        <span class="app-pi-form__available-name"><?= e(user_display_name($a)) ?></span>
                        <?= lucide_tag('user-plus', 'app-pi-form__available-icon') ?>
                    </button>
                    <?php endforeach; ?>

                    <p class="app-pi-form__available-empty" id="available-empty"
                       style="<?= count($alunos) > count($coauthorIds) ? 'display:none' : '' ?>">
                        Todos os alunos disponíveis já foram adicionados.
                    </p>
                </div>
            </div>

            <?php /* ─── Actions ─── */ ?>
            <div class="app-form-actions">
                <a href="/admin/pi" class="app-action-btn app-action-btn--outline">
                    Cancelar
                </a>
                <button type="submit" class="app-action-btn app-action-btn--blue">
                    <?= lucide_tag('save', 'app-action-btn__icon') ?>
                    <?= $isEditing ? 'Salvar Alterações' : 'Criar Grupo' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    function updateEmptyStates() {
        const pills    = document.querySelectorAll('#selected-pills .app-pi-form__pill');
        const empty    = document.getElementById('pills-empty');
        const avItems  = document.querySelectorAll('#available-list .app-pi-form__available-item');
        const avEmpty  = document.getElementById('available-empty');
        const counter  = document.getElementById('member-count');

        empty.style.display   = pills.length === 0 ? '' : 'none';
        const visibleAv = [...avItems].filter(el => !el.classList.contains('app-pi-form__available-item--hidden'));
        avEmpty.style.display = visibleAv.length === 0 ? '' : 'none';
        counter.textContent   = pills.length;
    }

    window.addMember = function (btn) {
        const id   = btn.dataset.id;
        const name = btn.dataset.name;

        // Hide from available list
        btn.classList.add('app-pi-form__available-item--hidden');

        // Add pill
        const pill = document.createElement('span');
        pill.className   = 'app-pi-form__pill';
        pill.dataset.id  = id;
        pill.innerHTML   =
            `<input type="hidden" name="membros[]" value="${id}">` +
            `${name}` +
            `<button type="button" class="app-pi-form__pill-remove" onclick="removeMember(this)">` +
            `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>` +
            `</button>`;

        document.getElementById('pills-empty').before(pill);
        updateEmptyStates();
    };

    window.removeMember = function (btn) {
        const pill = btn.closest('.app-pi-form__pill');
        const id   = pill.dataset.id;

        // Show in available list
        const avItem = document.querySelector(`#available-list [data-id="${id}"]`);
        if (avItem) avItem.classList.remove('app-pi-form__available-item--hidden');

        pill.remove();
        updateEmptyStates();
    };

    updateEmptyStates();
})();
</script>
