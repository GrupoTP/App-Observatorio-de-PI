<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <div class="app-pi-header__text">
            <div>
                <h1 class="app-page-heading__title">Projetos Integradores</h1>
                <p class="app-page-heading__subtitle">Projetos avaliados e disponíveis para visualização</p>
            </div>
        </div>

        <div class="app-form-card">
            <form method="get" class="app-alunos-filters">
                <div class="app-pi-filters__fields" style="grid-template-columns:1fr auto">
                    <div class="app-field">
                        <label for="q" class="app-field__label">Buscar</label>
                        <div class="app-field__icon-wrap">
                            <?= lucide_tag('search', 'app-field__icon') ?>
                            <input type="search" id="q" name="q"
                                   class="app-field__input app-field__input--icon"
                                   placeholder="Nome do projeto ou aluno..."
                                   value="<?= e($search) ?>">
                        </div>
                    </div>
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
                        <strong><?= count($projects) ?></strong>
                        projeto<?= count($projects) !== 1 ? 's' : '' ?> encontrado<?= count($projects) !== 1 ? 's' : '' ?>
                    </p>
                </div>
            </form>
        </div>

        <?php if (empty($projects)): ?>
        <div class="app-empty-state">
            <?= lucide_tag('folder-open', 'app-empty-state__icon') ?>
            <h3 class="app-empty-state__title">Nenhum projeto disponível</h3>
            <p class="app-empty-state__subtitle">Ainda não há projetos avaliados e compartilhados publicamente.</p>
        </div>
        <?php else: ?>
        <div class="app-projetos-table-wrap">
            <table class="app-projetos-table">
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Projeto</th>
                        <th>Curso / Módulo</th>
                        <th>Conceito</th>
                        <th class="app-projetos-table__th--center">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p):
                        $conceito = null;
                        if (!empty($p['conceito_medio'])) {
                            $conceito = nota_para_conceito((float) $p['conceito_medio']);
                        }
                    ?>
                    <tr class="app-projetos-table__row">
                        <td class="app-projetos-table__cell">
                            <p class="app-projetos-table__student-name">
                                <?= e(trim(($p['nome_civil_nome'] ?? '') . ' ' . ($p['nome_civil_sobrenome'] ?? ''))) ?>
                            </p>
                        </td>
                        <td class="app-projetos-table__cell">
                            <p class="app-projetos-table__project-title"><?= e($p['titulo']) ?></p>
                        </td>
                        <td class="app-projetos-table__cell">
                            <p class="app-projetos-table__course"><?= e($p['nome_curso'] ?? '') ?></p>
                            <p class="app-projetos-table__module"><?= e($p['modulo'] ?? '') ?></p>
                        </td>
                        <td class="app-projetos-table__cell app-projetos-table__cell--conceito">
                            <?php if ($conceito !== null): ?>
                            <span class="app-mencao-badge app-mencao-badge--<?= e($conceito['modifier']) ?>">
                                <span class="app-mencao-badge__code"><?= e($conceito['code']) ?></span>
                                &nbsp;— <?= e($conceito['label']) ?>
                            </span>
                            <?php else: ?>
                            <span class="app-projetos-table__no-date">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="app-projetos-table__cell app-projetos-table__cell--actions">
                            <a href="/parceiro/projetos/<?= e($p['id_projeto']) ?>"
                               class="app-projetos-table__action-btn" title="Ver detalhes">
                                <?= lucide_tag('eye', '') ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>
