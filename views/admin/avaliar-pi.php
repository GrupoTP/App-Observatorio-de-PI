<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <?php /* ─── Header ─── */ ?>
        <div class="app-pi-header">
            <div class="app-pi-header__text">
                <a href="/admin/pi/<?= e($project['id_projeto']) ?>" class="app-page-heading__back" aria-label="Voltar">
                    <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
                </a>
                <div>
                    <h1 class="app-page-heading__title">Avaliar Projeto Integrador</h1>
                    <p class="app-page-heading__subtitle">Avalie em grupo ou individualmente</p>
                </div>
            </div>
        </div>

        <?php /* ─── Info box ─── */ ?>
        <div class="app-info-box">
            <?= lucide_tag('info', 'app-info-box__icon') ?>
            <div>
                <p class="app-info-box__title"><strong>Funcionalidades:</strong></p>
                <ul class="app-info-box__list">
                    <li><strong>Avaliação em Grupo:</strong> todos os membros recebem a mesma nota</li>
                    <li><strong>Avaliação Individual:</strong> atribua notas diferentes para cada membro (sobrepõe a nota em grupo)</li>
                    <li>Todas as avaliações de PI aparecerão na tela de <strong>Feedbacks</strong> dos alunos</li>
                </ul>
            </div>
        </div>

        <?php /* ─── Group summary card ─── */ ?>
        <div class="app-form-card">
            <div class="app-pi-avaliar__group-header">
                <?= lucide_tag('users', 'app-pi-card__group-icon') ?>
                <div>
                    <h2 class="app-pi-card__name"><?= e($project['nome_grupo'] ?? $project['titulo']) ?></h2>
                    <h3 class="app-pi-card__project"><?= e($project['titulo']) ?></h3>
                </div>
            </div>

            <dl class="app-pi-detalhes__grid mt-3">
                <div class="app-pi-detalhes__field">
                    <span class="app-pi-detalhes__label">Curso:</span>
                    <span class="app-pi-detalhes__value"><?= e($project['nome_curso'] ?? '') ?></span>
                </div>
                <div class="app-pi-detalhes__field">
                    <span class="app-pi-detalhes__label">Módulo:</span>
                    <span class="app-pi-detalhes__value"><?= e($project['modulo'] ?? '') ?></span>
                </div>
                <?php if (!empty($project['prazo_especial'])): ?>
                <div class="app-pi-detalhes__field">
                    <span class="app-pi-detalhes__label">Prazo:</span>
                    <span class="app-pi-detalhes__value"><?= e(date('d/m/Y', strtotime($project['prazo_especial']))) ?></span>
                </div>
                <?php endif; ?>
            </dl>

            <?php if (!empty($alunos)): ?>
            <div class="app-pi-avaliar__members">
                <p class="app-pi-avaliar__members-label">Membros do Grupo:</p>
                <div class="app-pi-avaliar__member-pills">
                    <?php foreach ($alunos as $a): ?>
                    <span class="app-pi-avaliar__member-pill">
                        <?= e(user_display_name($a)) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php /* Alert if already evaluated */ ?>
            <?php if ($existingFeedback !== null && $existingConceito !== null): ?>
            <div class="app-pi-avaliar__existing-alert">
                <?= lucide_tag('alert-triangle', 'app-pi-avaliar__alert-icon') ?>
                <p>
                    <strong>Atenção:</strong> Este grupo já possui uma avaliação em grupo com conceito
                    <span class="app-mencao-badge app-mencao-badge--<?= e($existingConceito['modifier']) ?>" style="vertical-align:middle">
                        <span class="app-mencao-badge__code"><?= e($existingConceito['code']) ?></span>
                        &nbsp;— <?= e($existingConceito['label']) ?>
                    </span>.
                    Você pode criar avaliações individuais adicionais ou sobrescrever a avaliação do grupo.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <?php /* ─── Evaluation form ─── */ ?>
        <form method="post" action="/admin/pi/<?= e($project['id_projeto']) ?>/avaliar" class="app-page-stack" id="avaliar-form">
            <?= csrf_field() ?>

            <?php /* ─── Evaluation type toggle ─── */ ?>
            <div class="app-form-card">
                <h3 class="app-pi-form-section-title" style="margin-bottom:1rem">
                    Tipo de Avaliação
                </h3>

                <div class="app-pi-avaliar__type-grid">
                    <label class="app-pi-avaliar__type-card" id="type-grupo-card">
                        <input type="radio" name="tipo_avaliacao" value="grupo"
                               <?= ($tipoInicial ?? 'grupo') !== 'individual' ? 'checked' : '' ?>
                               onchange="onTypeChange(this)">
                        <div class="app-pi-avaliar__type-inner">
                            <?= lucide_tag('users', 'app-pi-avaliar__type-icon') ?>
                            <div>
                                <p class="app-pi-avaliar__type-name">Avaliação em Grupo</p>
                                <p class="app-pi-avaliar__type-desc">Mesma nota para todos os membros</p>
                            </div>
                        </div>
                    </label>

                    <label class="app-pi-avaliar__type-card" id="type-individual-card">
                        <input type="radio" name="tipo_avaliacao" value="individual"
                               <?= ($tipoInicial ?? 'grupo') === 'individual' ? 'checked' : '' ?>
                               onchange="onTypeChange(this)">
                        <div class="app-pi-avaliar__type-inner">
                            <?= lucide_tag('user', 'app-pi-avaliar__type-icon') ?>
                            <div>
                                <p class="app-pi-avaliar__type-name">Avaliação Individual</p>
                                <p class="app-pi-avaliar__type-desc">Nota específica por aluno</p>
                            </div>
                        </div>
                    </label>
                </div>

                <?php /* Student selector (shown only for individual) */ ?>
                <div class="app-field mt-3" id="aluno-selector"
                     style="<?= ($tipoInicial ?? 'grupo') !== 'individual' ? 'display:none' : '' ?>">
                    <label class="app-field__label" for="id_aluno">
                        Selecione o Aluno: <span class="app-field__required">*</span>
                    </label>
                    <select name="id_aluno" id="id_aluno" class="app-field__input"
                            <?= ($tipoInicial ?? 'grupo') === 'individual' ? 'required' : '' ?>>
                        <option value="">— Selecione um aluno —</option>
                        <?php foreach ($alunos as $a): ?>
                        <option value="<?= e($a['id_usuario']) ?>">
                            <?= e(user_display_name($a)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php /* ─── Criteria ─── */ ?>
            <div class="app-form-card">
                <div class="app-pi-form-section-title" style="justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <?= lucide_tag('file-text', 'app-pi-detalhes__section-icon') ?>
                        <h3 style="margin:0">Critérios de Avaliação (<?= count($criteria) ?>)</h3>
                    </div>
                </div>

                <div class="app-pi-avaliar__criteria">
                    <?php foreach ($criteria as $i => $c):
                        $fieldName    = 'criterio_' . $c['id_criterio'];
                        $selectedCode = '';
                    ?>
                    <div class="app-pi-avaliar__criterion-box">
                        <div class="app-pi-avaliar__criterion-heading">
                            <span class="app-pi-avaliar__criterion-label">
                                <?= $i + 1 ?>. <?= e($c['nome']) ?> <span class="app-field__required">*</span>
                            </span>
                        </div>
                        <?php require dirname(__DIR__) . '/partials/app-conceito-slider.php'; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php /* ─── Average concept ─── */ ?>
                <div class="app-pi-avaliar__media-final" id="media-final-box">
                    <div class="app-pi-avaliar__media-label">
                        <p class="app-pi-avaliar__media-title">Conceito Médio</p>
                        <p class="app-pi-avaliar__media-subtitle">Calculado automaticamente com base nos critérios</p>
                    </div>
                    <div id="media-conceito-display" class="app-pi-avaliar__media-badge">
                        <span class="app-mencao-badge app-mencao-badge--b">
                            <span class="app-mencao-badge__code">B</span>
                            &nbsp;— Bom
                        </span>
                    </div>
                </div>

                <div class="app-field" style="margin-top:1.25rem">
                    <label class="app-field__label app-pi-detalhes__label--section" for="descricao"
                           id="feedback-label">
                        Feedback do Grupo
                    </label>
                    <textarea id="descricao"
                              name="descricao"
                              class="app-field__input app-field__textarea"
                              rows="4"
                              required
                              placeholder="Forneça comentários construtivos, destacando pontos fortes e sugestões de melhoria..."></textarea>
                </div>
            </div>

            <?php /* ─── Actions ─── */ ?>
            <div class="app-form-actions">
                <a href="/admin/pi/<?= e($project['id_projeto']) ?>"
                   class="app-action-btn app-action-btn--outline">
                    Cancelar
                </a>
                <button type="submit" class="app-action-btn app-action-btn--blue">
                    <?= lucide_tag('save', 'app-action-btn__icon') ?>
                    Finalizar Avaliação
                </button>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    // ── Concepts ordered same as the slider partial ──
    var CONCEPTS = [
        { code: 'I',   label: 'Insuficiente',             modifier: 'i',   score: 0 },
        { code: 'ANS', label: 'Ainda Não Suficiente',     modifier: 'ans', score: 1 },
        { code: 'B',   label: 'Bom',                      modifier: 'b',   score: 2 },
        { code: 'O',   label: 'Ótimo',                    modifier: 'o',   score: 3 },
        { code: 'AE',  label: 'Atendido com Excelência',  modifier: 'ae',  score: 4 },
    ];

    function updateSliderDisplay(range) {
        var idx = parseInt(range.value, 10);
        range.style.setProperty('--val', idx);
        var concept = CONCEPTS[Math.min(Math.max(idx, 0), 4)];
        var wrapper = range.closest('.conceito-slider');
        if (!wrapper) return;

        var sliderId = wrapper.dataset.sliderId;

        var hiddenInput = document.getElementById(sliderId + '_value');
        if (hiddenInput) hiddenInput.value = concept.code;

        var display = document.getElementById(sliderId + '_display');
        if (display) {
            CONCEPTS.forEach(function (c) {
                display.classList.remove('conceito-slider__display--' + c.modifier);
            });
            display.classList.add('conceito-slider__display--' + concept.modifier);

            var codeSpan = display.querySelector('.conceito-slider__display-code');
            var labelSpan = display.querySelector('.conceito-slider__display-label');
            if (codeSpan) codeSpan.textContent = concept.code;
            if (labelSpan) labelSpan.textContent = concept.label;
        }
    }

    function updateMediaConceito() {
        var hiddenInputs = document.querySelectorAll('#avaliar-form input[type=hidden][name^="criterio_"]');
        if (hiddenInputs.length === 0) return;

        var totalScore = 0;
        hiddenInputs.forEach(function (inp) {
            var code = (inp.value || 'B').toUpperCase();
            var c = CONCEPTS.find(function (x) { return x.code === code; }) || CONCEPTS[2];
            totalScore += c.score;
        });
        var avgScore = Math.round(totalScore / hiddenInputs.length);
        var avgConcept = CONCEPTS[Math.min(Math.max(avgScore, 0), 4)];

        var box = document.getElementById('media-conceito-display');
        if (box) {
            box.innerHTML =
                '<span class="app-mencao-badge app-mencao-badge--' + avgConcept.modifier + '">' +
                '<span class="app-mencao-badge__code">' + avgConcept.code + '</span>' +
                '&nbsp;— ' + avgConcept.label +
                '</span>';
        }
    }

    // Update slider display and average when range input changes
    document.addEventListener('input', function (e) {
        if (e.target && e.target.type === 'range' && e.target.closest('.conceito-slider')) {
            updateSliderDisplay(e.target);
            updateMediaConceito();
        }
    });

    // ── Type toggle ──
    function updateTypeCards() {
        document.querySelectorAll('.app-pi-avaliar__type-card').forEach(function (card) {
            var radio = card.querySelector('input[type=radio]');
            card.classList.toggle('app-pi-avaliar__type-card--active', radio.checked);
        });
    }

    function updateFeedbackLabel(isIndividual) {
        var lbl = document.getElementById('feedback-label');
        if (lbl) lbl.textContent = isIndividual ? 'Feedback do Aluno' : 'Feedback do Grupo';
    }

    window.onTypeChange = function (radio) {
        var selector   = document.getElementById('aluno-selector');
        var alunoSelect = document.getElementById('id_aluno');
        var isIndividual = radio.value === 'individual';

        selector.style.display = isIndividual ? '' : 'none';
        alunoSelect.required   = isIndividual;
        if (!isIndividual) alunoSelect.value = '';

        updateTypeCards();
        updateFeedbackLabel(isIndividual);
    };

    // Init
    updateTypeCards();
    var checked = document.querySelector('.app-pi-avaliar__type-card input[type=radio]:checked');
    if (checked) updateFeedbackLabel(checked.value === 'individual');
    updateMediaConceito();
})();
</script>
