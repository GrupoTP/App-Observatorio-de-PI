<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <?php /* ─── Heading ─── */ ?>
        <div class="app-pi-header__text">
            <a href="/admin/dashboard" class="app-page-heading__back" aria-label="Voltar">
                <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
            </a>
            <div>
                <h1 class="app-page-heading__title">Gerenciar Projetos</h1>
                <p class="app-page-heading__subtitle">Visualize, avalie e gerencie todos os projetos submetidos</p>
            </div>
        </div>

        <?php /* ─── Filters ─── */ ?>
        <div class="app-form-card">
            <form method="get" class="app-alunos-filters">
                <div class="app-pi-filters__fields">
                    <div class="app-field">
                        <label for="q" class="app-field__label">Buscar</label>
                        <div class="app-field__icon-wrap">
                            <?= lucide_tag('search', 'app-field__icon') ?>
                            <input type="search"
                                   id="q" name="q"
                                   class="app-field__input app-field__input--icon"
                                   placeholder="Nome do aluno ou projeto..."
                                   value="<?= e($search) ?>">
                        </div>
                    </div>

                    <div class="app-field">
                        <label for="status" class="app-field__label">Status</label>
                        <select name="status" id="status" class="app-field__input">
                            <option value="todos"      <?= $status === 'todos'       ? 'selected' : '' ?>>Todos os status</option>
                            <option value="enviado"    <?= $status === 'enviado'     ? 'selected' : '' ?>>Aguardando Avaliação</option>
                            <option value="em-correcao" <?= $status === 'em-correcao' ? 'selected' : '' ?>>Em Correção</option>
                            <option value="avaliado"   <?= $status === 'avaliado'    ? 'selected' : '' ?>>Avaliados</option>
                        </select>
                    </div>

                    <div class="app-field">
                        <label for="course" class="app-field__label">Curso</label>
                        <select name="course" id="course" class="app-field__input">
                            <?php foreach ($courses as $c): ?>
                            <option value="<?= e($c) ?>" <?= $course === $c ? 'selected' : '' ?>>
                                <?= e($c === 'todos' ? 'Todos os cursos' : $c) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="app-field app-field--action">
                        <label class="app-field__label">&nbsp;</label>
                        <button type="submit" class="app-action-btn app-action-btn--blue">
                            <?= lucide_tag('search', 'app-action-btn__icon') ?>
                            Filtrar
                        </button>
                    </div>
                </div>

                <div class="app-alunos-filters__meta">
                    <p class="app-alunos-count">
                        <strong><?= count($projects) ?></strong>
                        projeto<?= count($projects) !== 1 ? 's' : '' ?>
                        encontrado<?= count($projects) !== 1 ? 's' : '' ?>
                    </p>
                    <?php if ($search !== '' || $status !== 'todos' || ($course !== 'todos')): ?>
                    <a href="/admin/projetos" class="app-alunos-filters__clear">Limpar filtros</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php /* ─── Empty state ─── */ ?>
        <?php if (empty($projects)): ?>
        <div class="app-empty-state">
            <?= lucide_tag('folder-open', 'app-empty-state__icon') ?>
            <h3 class="app-empty-state__title">Nenhum projeto encontrado</h3>
            <p class="app-empty-state__subtitle">Tente ajustar os filtros de busca</p>
            <a href="/admin/projetos" class="app-action-btn app-action-btn--blue app-action-btn--sm mt-2">
                Limpar filtros
            </a>
        </div>

        <?php else: ?>

        <?php /* ─── Table (desktop) ─── */ ?>
        <div class="app-projetos-table-wrap">
            <table class="app-projetos-table">
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Projeto</th>
                        <th>Curso / Módulo</th>
                        <th>Prazo</th>
                        <th>Status</th>
                        <th>Conceito</th>
                        <th class="app-projetos-table__th--center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p):
                        $situacao  = $p['situacao_projeto'] ?? 'em-andamento';
                        $conceito  = null;
                        if (!empty($p['conceito_medio'])) {
                            $conceito = nota_para_conceito((float) $p['conceito_medio']);
                        }
                        $badgeCfg = match ($situacao) {
                            'avaliado'    => ['modifier' => 'success', 'icon' => 'check-circle', 'label' => 'Avaliado'],
                            'enviado'     => ['modifier' => 'warning', 'icon' => 'clock',        'label' => 'Aguardando Avaliação'],
                            'em-correcao' => ['modifier' => 'info',    'icon' => 'edit-2',       'label' => 'Em Correção'],
                            default       => ['modifier' => 'default', 'icon' => 'file-text',    'label' => 'Em Andamento'],
                        };
                    ?>
                    <tr class="app-projetos-table__row">
                        <td class="app-projetos-table__cell">
                            <p class="app-projetos-table__student-name">
                                <?= e(trim(($p['nome_civil_nome'] ?? '') . ' ' . ($p['nome_civil_sobrenome'] ?? ''))) ?>
                            </p>
                            <p class="app-projetos-table__student-email">
                                <?= e($p['email_institucional'] ?? '') ?>
                            </p>
                        </td>
                        <td class="app-projetos-table__cell">
                            <p class="app-projetos-table__project-title">
                                <?= e($p['titulo']) ?>
                            </p>
                            <?php if (!empty($p['nome_grupo'])): ?>
                            <p class="app-projetos-table__group-name">
                                <?= e($p['nome_grupo']) ?>
                            </p>
                            <?php endif; ?>
                        </td>
                        <td class="app-projetos-table__cell">
                            <p class="app-projetos-table__course"><?= e($p['nome_curso'] ?? '') ?></p>
                            <p class="app-projetos-table__module"><?= e($p['modulo'] ?? '') ?></p>
                        </td>
                        <td class="app-projetos-table__cell app-projetos-table__cell--date">
                            <?php if (!empty($p['prazo_especial'])): ?>
                            <?= e(date('d/m/Y', strtotime($p['prazo_especial']))) ?>
                            <?php else: ?>
                            <span class="app-projetos-table__no-date">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="app-projetos-table__cell">
                            <span class="app-pi-status app-pi-status--<?= e($badgeCfg['modifier']) ?>">
                                <?= lucide_tag($badgeCfg['icon'], 'app-pi-status__icon') ?>
                                <?= e($badgeCfg['label']) ?>
                            </span>
                        </td>
                        <td class="app-projetos-table__cell app-projetos-table__cell--conceito">
                            <?php if ($conceito !== null): ?>
                            <span class="app-mencao-badge app-mencao-badge--<?= e($conceito['modifier']) ?>">
                                <span class="app-mencao-badge__code"><?= e($conceito['code']) ?></span>
                            </span>
                            <?php else: ?>
                            <span class="app-projetos-table__no-date">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="app-projetos-table__cell app-projetos-table__cell--actions">
                            <div class="app-projetos-table__actions">
                                <a href="/admin/projetos/<?= e($p['id_projeto']) ?>"
                                   class="app-projetos-table__action-btn"
                                   title="Visualizar">
                                    <?= lucide_tag('eye', '') ?>
                                </a>
                                <?php if ($situacao !== 'avaliado'): ?>
                                <a href="/admin/projetos/<?= e($p['id_projeto']) ?>/avaliar"
                                   class="app-projetos-table__action-btn app-projetos-table__action-btn--green"
                                   title="Avaliar">
                                    <?= lucide_tag('check-circle', '') ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php /* ─── Cards (mobile fallback) ─── */ ?>
        <div class="app-projetos-cards">
            <?php foreach ($projects as $p):
                $situacao = $p['situacao_projeto'] ?? 'em-andamento';
                $conceito = null;
                if (!empty($p['conceito_medio'])) {
                    $conceito = nota_para_conceito((float) $p['conceito_medio']);
                }
                $badgeCfg = match ($situacao) {
                    'avaliado'    => ['modifier' => 'success', 'icon' => 'check-circle', 'label' => 'Avaliado'],
                    'enviado'     => ['modifier' => 'warning', 'icon' => 'clock',        'label' => 'Aguardando Avaliação'],
                    'em-correcao' => ['modifier' => 'info',    'icon' => 'edit-2',       'label' => 'Em Correção'],
                    default       => ['modifier' => 'default', 'icon' => 'file-text',    'label' => 'Em Andamento'],
                };
            ?>
            <div class="app-pi-card">
                <div class="app-pi-card__main">
                    <div class="app-pi-card__info">
                        <p class="app-pi-detalhes__member-name">
                            <?= e(trim(($p['nome_civil_nome'] ?? '') . ' ' . ($p['nome_civil_sobrenome'] ?? ''))) ?>
                        </p>
                        <p class="app-pi-detalhes__member-email"><?= e($p['email_institucional'] ?? '') ?></p>
                        <h3 class="app-pi-card__project mt-2"><?= e($p['titulo']) ?></h3>
                        <p class="app-pi-detalhes__label"><?= e($p['nome_curso'] ?? '') ?> · <?= e($p['modulo'] ?? '') ?></p>
                    </div>
                    <div class="app-pi-card__side">
                        <span class="app-pi-status app-pi-status--<?= e($badgeCfg['modifier']) ?>">
                            <?= lucide_tag($badgeCfg['icon'], 'app-pi-status__icon') ?>
                            <?= e($badgeCfg['label']) ?>
                        </span>
                        <?php if ($conceito !== null): ?>
                        <span class="app-mencao-badge app-mencao-badge--<?= e($conceito['modifier']) ?>">
                            <span class="app-mencao-badge__code"><?= e($conceito['code']) ?></span>
                            &nbsp;— <?= e($conceito['label']) ?>
                        </span>
                        <?php endif; ?>
                        <div class="app-pi-card__actions-row">
                            <a href="/admin/projetos/<?= e($p['id_projeto']) ?>"
                               class="app-action-btn app-action-btn--blue app-action-btn--sm">
                                <?= lucide_tag('eye', 'app-action-btn__icon') ?>
                                Ver
                            </a>
                            <?php if ($situacao !== 'avaliado'): ?>
                            <a href="/admin/projetos/<?= e($p['id_projeto']) ?>/avaliar"
                               class="app-action-btn app-action-btn--green app-action-btn--sm">
                                <?= lucide_tag('check-circle', 'app-action-btn__icon') ?>
                                Avaliar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div>
</div>
