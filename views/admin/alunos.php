<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">
        <?php
        $headingTitle    = 'Gerenciar Alunos';
        $headingSubtitle = 'Consulte e avalie os alunos cadastrados no sistema';
        $backUrl         = '/admin/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <?php /* ─── Filters ─── */ ?>
        <div class="app-form-card">
            <form method="get" class="app-alunos-filters">
                <div class="app-alunos-filters__fields">
                    <div class="app-field">
                        <label for="q" class="app-field__label">Buscar</label>
                        <div class="app-field__icon-wrap">
                            <?= lucide_tag('search', 'app-field__icon') ?>
                            <input type="search"
                                   id="q"
                                   name="q"
                                   class="app-field__input app-field__input--icon"
                                   placeholder="Nome, e-mail ou curso..."
                                   value="<?= e($search) ?>">
                        </div>
                    </div>

                    <?php if (!empty($cursos)): ?>
                    <div class="app-field">
                        <label for="curso" class="app-field__label">Curso</label>
                        <select name="curso" id="curso" class="app-field__input">
                            <option value="">Todos os cursos</option>
                            <?php foreach ($cursos as $c): ?>
                            <option value="<?= e($c['id_curso']) ?>"
                                <?= $cursoId === $c['id_curso'] ? 'selected' : '' ?>>
                                <?= e($c['nome_curso']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="app-field app-field--action">
                        <label class="app-field__label">&nbsp;</label>
                        <button type="submit" class="app-action-btn app-action-btn--blue">
                            <?= lucide_tag('search', 'app-action-btn__icon') ?>
                            Buscar
                        </button>
                    </div>
                </div>

                <div class="app-alunos-filters__meta">
                    <p class="app-alunos-count">
                        <strong><?= count($alunos) ?></strong>
                        aluno<?= count($alunos) !== 1 ? 's' : '' ?>
                        encontrado<?= count($alunos) !== 1 ? 's' : '' ?>
                    </p>
                    <?php if ($search !== '' || $cursoId !== ''): ?>
                    <a href="/admin/alunos" class="app-alunos-filters__clear">Limpar filtros</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php /* ─── Student cards ─── */ ?>
        <?php if (empty($alunos)): ?>
        <div class="app-empty-state">
            <?= lucide_tag('users', 'app-empty-state__icon') ?>
            <h3 class="app-empty-state__title">Nenhum aluno encontrado</h3>
            <p class="app-empty-state__subtitle">Tente ajustar os filtros de busca</p>
            <?php if ($search !== '' || $cursoId !== ''): ?>
            <a href="/admin/alunos" class="app-action-btn app-action-btn--secondary mt-2">
                Limpar filtros
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="app-alunos-list">
            <?php foreach ($alunos as $a):
                $alunoId     = $a['id_usuario'];
                $projects    = $projectsByAluno[$alunoId] ?? [];
                $hasProjects = !empty($projects);
            ?>
            <div class="app-aluno-card" id="aluno-card-<?= e($alunoId) ?>">

                <?php /* ── Card header (always visible) ── */ ?>
                <div class="app-aluno-card__header">
                    <div class="app-aluno-card__info">
                        <h3 class="app-aluno-card__name"><?= e(user_display_name($a)) ?></h3>
                        <p class="app-aluno-card__meta">
                            <?= e($a['email_institucional']) ?>
                            <?php if (!empty($a['nome_curso'])): ?>
                            <span class="app-aluno-card__sep">•</span>
                            <?= e($a['nome_curso']) ?>
                            <?php if (!empty($a['modulo'])): ?>
                            <span class="app-aluno-card__sep">•</span>
                            <?= e($a['modulo']) ?>
                            <?php endif; ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="app-aluno-card__actions">
                        <button type="button"
                                class="app-action-btn app-action-btn--blue app-action-btn--sm app-aluno-eval-toggle"
                                data-target="eval-<?= e($alunoId) ?>"
                                aria-expanded="false">
                            <?= lucide_tag('edit-2', 'app-action-btn__icon') ?>
                            <span class="app-aluno-eval-toggle__label">Avaliar</span>
                        </button>
                    </div>
                </div>

                <?php /* ── Evaluation panel (hidden by default) ── */ ?>
                <div class="app-aluno-card__eval-panel" id="eval-<?= e($alunoId) ?>" hidden>
                    <div class="app-aluno-card__eval-divider"></div>

                    <?php if (!$hasProjects): ?>
                    <?php /* No projects */ ?>
                    <div class="app-aluno-no-projects">
                        <?= lucide_tag('folder-open', 'app-aluno-no-projects__icon') ?>
                        <p>Este aluno ainda não submeteu nenhum projeto.</p>
                    </div>

                    <?php else: ?>
                    <?php /* Project list — always starts visible; eval forms start hidden */ ?>
                    <div class="app-aluno-project-list" id="proj-list-<?= e($alunoId) ?>">
                        <p class="app-aluno-project-list__title">
                            <?= lucide_tag('layers', '') ?>
                            Selecione o projeto a avaliar:
                        </p>
                        <?php foreach ($projects as $p): ?>
                        <button type="button"
                                class="app-aluno-project-item app-aluno-project-btn"
                                data-aluno="<?= e($alunoId) ?>"
                                data-project="<?= e($p['id_projeto']) ?>">
                            <div class="app-aluno-project-item__body">
                                <span class="app-aluno-project-item__title"><?= e($p['titulo']) ?></span>
                                <span class="app-aluno-project-item__meta">
                                    <?= e($p['nome_turma'] ?? '') ?>
                                    <?php if (!empty($p['situacao_projeto'])): ?>
                                    • <?= e($p['situacao_projeto']) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?= lucide_tag('arrow-right', 'app-aluno-project-item__arrow') ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <?php /* Inline eval form per project — each starts hidden */ ?>
                    <?php foreach ($projects as $p): ?>
                    <div class="app-aluno-project-eval"
                         id="proj-eval-<?= e($p['id_projeto']) ?>"
                         hidden>

                        <div class="app-aluno-eval-back-bar">
                            <button type="button"
                                    class="app-aluno-eval-back"
                                    data-aluno="<?= e($alunoId) ?>">
                                <?= lucide_tag('arrow-left', 'app-action-btn__icon') ?>
                                Voltar
                            </button>
                            <span class="app-aluno-eval-back-bar__title">
                                <?= lucide_tag('folder', '') ?>
                                <?= e($p['titulo']) ?>
                            </span>
                        </div>

                        <form method="post"
                              action="/admin/projetos/<?= e($p['id_projeto']) ?>/avaliar"
                              class="app-aluno-eval-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_redirect" value="/admin/alunos">

                            <?php if (!empty($criteria)): ?>
                            <div class="app-aluno-criteria-list">
                                <?php foreach ($criteria as $idx => $c): ?>
                                <div class="app-aluno-criterion">
                                    <div class="app-aluno-criterion__header">
                                        <span class="app-aluno-criterion__number"><?= $idx + 1 ?></span>
                                        <div>
                                            <p class="app-aluno-criterion__name"><?= e($c['nome']) ?></p>
                                            <?php if (!empty($c['descricao'])): ?>
                                            <p class="app-aluno-criterion__desc"><?= e($c['descricao']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php
                                    $fieldName    = 'criterio_' . $c['nome'];
                                    $selectedCode = '';
                                    require __DIR__ . '/../partials/app-conceito-slider.php';
                                    ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <div class="app-field mt-3">
                                <label class="app-field__label">
                                    Feedback para o aluno <span class="text-senac-error">*</span>
                                </label>
                                <textarea name="descricao"
                                          class="app-field__textarea"
                                          rows="3"
                                          required
                                          placeholder="Forneça um feedback construtivo sobre o desempenho do aluno..."></textarea>
                            </div>

                            <div class="app-aluno-eval-form__actions">
                                <button type="button"
                                        class="app-action-btn app-action-btn--secondary app-action-btn--sm app-aluno-eval-cancel"
                                        data-target="eval-<?= e($alunoId) ?>">
                                    <?= lucide_tag('x', 'app-action-btn__icon') ?>
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="app-action-btn app-action-btn--primary app-action-btn--sm">
                                    <?= lucide_tag('check-circle', 'app-action-btn__icon') ?>
                                    Salvar avaliação
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php /* end eval panel */ ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (\App\Auth\SessionAuth::isAdmin()): ?>
        <div class="app-form-actions app-form-actions--left">
            <a href="/admin/alunos/novo" class="app-action-btn app-action-btn--secondary">
                <?= lucide_tag('user-plus', 'app-action-btn__icon') ?>
                Cadastrar aluno
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    // Lowest → highest: index 0 = I, index 4 = AE (matches the PHP partial order)
    const concepts = [
        { code: 'I',   label: 'Insuficiente',             modifier: 'i'   },
        { code: 'ANS', label: 'Ainda Não Suficiente',     modifier: 'ans' },
        { code: 'B',   label: 'Bom',                     modifier: 'b'   },
        { code: 'O',   label: 'Ótimo',                   modifier: 'o'   },
        { code: 'AE',  label: 'Atendido com Excelência', modifier: 'ae'  },
    ];

    function initSliders(container) {
        container.querySelectorAll('.conceito-slider__range').forEach(function (range) {
            if (range.dataset.init) return;
            range.dataset.init = '1';

            const sliderId = range.id;
            const display  = document.getElementById(sliderId + '_display');
            const hidden   = document.getElementById(sliderId + '_value');

            function update() {
                const idx     = parseInt(range.value, 10);
                const concept = concepts[idx];
                display.className = 'conceito-slider__display conceito-slider__display--' + concept.modifier;
                display.querySelector('.conceito-slider__display-code').textContent  = concept.code;
                display.querySelector('.conceito-slider__display-label').textContent = concept.label;
                hidden.value = concept.code;
                range.style.setProperty('--val', idx);
            }

            range.addEventListener('input', update);
            update();
        });
    }

    // ── Toggle main eval panel ──
    document.querySelectorAll('.app-aluno-eval-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const panel  = document.getElementById(btn.dataset.target);
            const isOpen = !panel.hidden;

            panel.hidden = isOpen;
            btn.setAttribute('aria-expanded', String(!isOpen));
            btn.querySelector('.app-aluno-eval-toggle__label').textContent = isOpen ? 'Avaliar' : 'Cancelar';

            if (!isOpen) {
                // Init sliders that are now visible (single-project case)
                initSliders(panel);
            }
        });
    });

    // ── Cancel: close entire eval panel ──
    document.querySelectorAll('.app-aluno-eval-cancel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const panel = document.getElementById(btn.dataset.target);
            panel.hidden = true;

            const card      = panel.closest('.app-aluno-card');
            const toggleBtn = card && card.querySelector('.app-aluno-eval-toggle');
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.querySelector('.app-aluno-eval-toggle__label').textContent = 'Avaliar';
            }
        });
    });

    // ── Project button: show inline eval form ──
    document.querySelectorAll('.app-aluno-project-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alunoId   = btn.dataset.aluno;
            const projectId = btn.dataset.project;
            const list      = document.getElementById('proj-list-' + alunoId);
            const evalPanel = document.getElementById('proj-eval-' + projectId);

            if (!evalPanel) return;

            // Hide all project eval panels for this student
            document.querySelectorAll('[id^="proj-eval-"]').forEach(function (el) {
                if (el.closest('#aluno-card-' + alunoId)) el.hidden = true;
            });

            list.hidden    = true;
            evalPanel.hidden = false;

            // Init sliders now that the panel is visible
            initSliders(evalPanel);
        });
    });

    // ── Back button: hide eval form, show project list ──
    document.querySelectorAll('.app-aluno-eval-back').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alunoId = btn.dataset.aluno;
            const list    = document.getElementById('proj-list-' + alunoId);

            // Hide all eval panels for this student
            document.querySelectorAll('[id^="proj-eval-"]').forEach(function (el) {
                if (el.closest('#aluno-card-' + alunoId)) el.hidden = true;
            });

            list.hidden = false;
        });
    });

    // Init sliders that may already be visible (e.g. single-project cards)
    document.querySelectorAll('.app-aluno-card__eval-panel:not([hidden])').forEach(initSliders);
}());
</script>
