<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <?php /* ─── Page heading with action buttons ─── */ ?>
        <div class="app-pi-header">
            <div class="app-pi-header__text">
                <a href="/admin/dashboard" class="app-page-heading__back" aria-label="Voltar">
                    <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
                </a>
                <div>
                    <h1 class="app-page-heading__title">Gerenciar Projetos Integradores</h1>
                    <p class="app-page-heading__subtitle">
                        Crie grupos, defina prazos, avalie projetos e gerencie rubricas de avaliação
                    </p>
                </div>
            </div>
            <div class="app-pi-header__actions">
                <a href="/admin/pi/rubrica"
                   class="app-action-btn app-action-btn--sm app-action-btn--rubrica">
                    <?= lucide_tag('settings', 'app-action-btn__icon') ?>
                    Configurar Rubrica
                </a>
                <a href="/admin/pi/novo"
                   class="app-action-btn app-action-btn--sm app-action-btn--blue">
                    <?= lucide_tag('plus', 'app-action-btn__icon') ?>
                    Novo Grupo PI
                </a>
            </div>
        </div>

        <?php /* ─── Info box ─── */ ?>
        <div class="app-info-box">
            <?= lucide_tag('info', 'app-info-box__icon') ?>
            <div>
                <p class="app-info-box__title mb-1"><strong>Funcionalidades:</strong></p>
                <ul class="app-info-box__list">
                    <li><strong>Avaliar Grupo:</strong> atribui a mesma nota a todos os membros do grupo</li>
                    <li><strong>Individual:</strong> abre a tela de alunos para atribuir notas diferentes a cada membro (sobrepõe a nota em grupo)</li>
                    <li><strong>Configurar Rubrica:</strong> adicione, remova ou renomeie critérios de avaliação</li>
                    <li><strong>Feedbacks:</strong> todas as avaliações aparecerão na tela de Feedbacks dos alunos</li>
                </ul>
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
                                   id="q"
                                   name="q"
                                   class="app-field__input app-field__input--icon"
                                   placeholder="Nome do grupo ou projeto..."
                                   value="<?= e($search) ?>">
                        </div>
                    </div>

                    <div class="app-field">
                        <label for="status" class="app-field__label">Status</label>
                        <select name="status" id="status" class="app-field__input">
                            <option value="todos" <?= $status === 'todos' ? 'selected' : '' ?>>Todos os status</option>
                            <option value="em-andamento" <?= $status === 'em-andamento' ? 'selected' : '' ?>>Em Andamento</option>
                            <option value="enviado" <?= $status === 'enviado' ? 'selected' : '' ?>>Aguardando Avaliação</option>
                            <option value="avaliado" <?= $status === 'avaliado' ? 'selected' : '' ?>>Avaliados</option>
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
                        <strong><?= count($groups) ?></strong>
                        grupo<?= count($groups) !== 1 ? 's' : '' ?>
                        encontrado<?= count($groups) !== 1 ? 's' : '' ?>
                    </p>
                    <?php if ($search !== '' || $status !== 'todos' || ($course !== 'todos' && $course !== '')): ?>
                    <a href="/admin/pi" class="app-alunos-filters__clear">Limpar filtros</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php /* ─── Group cards ─── */ ?>
        <?php if (empty($groups)): ?>
        <div class="app-empty-state">
            <?= lucide_tag('users', 'app-empty-state__icon') ?>
            <h3 class="app-empty-state__title">Nenhum grupo encontrado</h3>
            <p class="app-empty-state__subtitle">
                <?= ($search !== '' || $status !== 'todos') ? 'Tente ajustar os filtros de busca' : 'Crie o primeiro grupo de Projeto Integrador' ?>
            </p>
            <a href="/admin/pi/novo" class="app-action-btn app-action-btn--blue app-action-btn--sm mt-2">
                <?= lucide_tag('plus', 'app-action-btn__icon') ?>
                Novo Grupo PI
            </a>
        </div>
        <?php else: ?>
        <div class="app-alunos-list">
            <?php foreach ($groups as $g):
                $situacao = $g['situacao_projeto'] ?? 'em-andamento';

                // Build members string: submitter + co-authors
                $membros = trim($g['submitter_nome'] ?? '');
                if (!empty($g['coautores_nomes'])) {
                    $membros .= ($membros !== '' ? ', ' : '') . $g['coautores_nomes'];
                }

                // Concept from last feedback (stored as internal numeric → convert to label)
                $ultimoConceito = null;
                if (!empty($g['ultimo_conceito'])) {
                    $nota = (float) $g['ultimo_conceito'];
                    $ultimoConceito = nota_para_conceito($nota);
                }

                // Status badge config
                $badgeConfig = match ($situacao) {
                    'avaliado'    => ['modifier' => 'success',  'icon' => 'check-circle', 'label' => 'Avaliado'],
                    'enviado'     => ['modifier' => 'warning',  'icon' => 'clock',        'label' => 'Aguardando Avaliação'],
                    'em-correcao' => ['modifier' => 'info',     'icon' => 'edit-2',       'label' => 'Em Correção'],
                    default       => ['modifier' => 'default',  'icon' => 'file-text',    'label' => 'Em Andamento'],
                };
            ?>
            <div class="app-pi-card">
                <div class="app-pi-card__main">
                    <?php /* Left: group info */ ?>
                    <div class="app-pi-card__info">
                        <div class="app-pi-card__name-row">
                            <?= lucide_tag('users', 'app-pi-card__group-icon') ?>
                            <h3 class="app-pi-card__name">
                                <?= e($g['nome_grupo'] ?? $g['titulo']) ?>
                            </h3>
                        </div>

                        <h4 class="app-pi-card__project"><?= e($g['titulo']) ?></h4>

                        <dl class="app-pi-card__details">
                            <?php if ($membros !== ''): ?>
                            <div class="app-pi-card__detail">
                                <dt>Membros:</dt>
                                <dd><?= e($membros) ?></dd>
                            </div>
                            <?php endif; ?>
                            <div class="app-pi-card__detail">
                                <dt>Curso:</dt>
                                <dd><?= e($g['nome_curso'] ?? '') ?></dd>
                            </div>
                            <div class="app-pi-card__detail">
                                <dt>Módulo:</dt>
                                <dd><?= e($g['modulo'] ?? '') ?></dd>
                            </div>
                            <div class="app-pi-card__detail">
                                <dt>Prazo:</dt>
                                <dd>
                                    <?php if (!empty($g['prazo_especial'])): ?>
                                    <?= e(date('d/m/Y', strtotime($g['prazo_especial']))) ?>
                                    <?php else: ?>
                                    <span class="app-pi-card__no-prazo">Não definido</span>
                                    <?php endif; ?>
                                    <a href="/admin/pi/<?= e($g['id_projeto']) ?>/editar"
                                       class="app-pi-card__edit-prazo"
                                       title="Editar prazo">
                                        <?= lucide_tag('edit-2', '') ?>
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <?php /* Right: status + concept + actions */ ?>
                    <div class="app-pi-card__side">
                        <span class="app-pi-status app-pi-status--<?= e($badgeConfig['modifier']) ?>">
                            <?= lucide_tag($badgeConfig['icon'], 'app-pi-status__icon') ?>
                            <?= e($badgeConfig['label']) ?>
                        </span>

                        <?php if ($ultimoConceito !== null): ?>
                        <div class="app-pi-card__conceito">
                            <p class="app-pi-card__conceito-label">Nota do grupo</p>
                            <span class="app-mencao-badge app-mencao-badge--<?= e($ultimoConceito['modifier']) ?>">
                                <span class="app-mencao-badge__code"><?= e($ultimoConceito['code']) ?></span>
                                &nbsp;— <?= e($ultimoConceito['label']) ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <div class="app-pi-card__actions">
                            <div class="app-pi-card__actions-row">
                                <a href="/admin/pi/<?= e($g['id_projeto']) ?>"
                                   class="app-action-btn app-action-btn--blue app-action-btn--sm">
                                    <?= lucide_tag('eye', 'app-action-btn__icon') ?>
                                    Detalhes
                                </a>
                                <a href="/admin/pi/<?= e($g['id_projeto']) ?>/editar"
                                   class="app-action-btn app-action-btn--secondary app-action-btn--sm">
                                    <?= lucide_tag('edit-2', 'app-action-btn__icon') ?>
                                    Editar
                                </a>
                            </div>
                            <div class="app-pi-card__actions-row">
                                <a href="/admin/pi/<?= e($g['id_projeto']) ?>/avaliar"
                                   class="app-action-btn app-action-btn--green app-action-btn--sm">
                                    <?= lucide_tag('users', 'app-action-btn__icon') ?>
                                    Avaliar Grupo
                                </a>
                                <a href="/admin/pi/<?= e($g['id_projeto']) ?>/avaliar?type=individual"
                                   class="app-action-btn app-action-btn--purple app-action-btn--sm">
                                    <?= lucide_tag('edit-2', 'app-action-btn__icon') ?>
                                    Individual
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
