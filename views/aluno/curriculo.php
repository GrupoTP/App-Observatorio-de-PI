<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php
$s = $curriculo ?? [];
$csrfToken = $csrfToken ?? '';
$userName = user_display_name($user ?? []);
$userEmail = $user['email_institucional'] ?? '';
?>
<script>
window.OPI_CURRICULO = {
    data: <?= json_encode($s, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>,
    csrf: <?= json_encode($csrfToken) ?>,
    userName: <?= json_encode($userName) ?>,
    userEmail: <?= json_encode($userEmail) ?>
};
</script>

<div class="curriculo-app">

    <!-- ── blue banner ─────────────────────────────────────────────────── -->
    <div class="curriculo-banner">
        <div class="curriculo-banner__inner">
            <a href="/portfolio" class="curriculo-banner__back" aria-label="Voltar para Portfólio">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </a>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" class="curriculo-banner__icon" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <div>
                <h1 class="curriculo-banner__title">Currículo/Portfólio</h1>
                <p class="curriculo-banner__subtitle">Monte seu currículo profissional e visualize em tempo real</p>
            </div>
        </div>
    </div>

    <!-- ── two-column grid ─────────────────────────────────────────────── -->
    <div class="curriculo-grid">

        <!-- LEFT: editor ────────────────────────────────────────────────── -->
        <div class="curriculo-editor" id="curriculo-editor">

            <!-- § Dados Pessoais e Contato -------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Dados Pessoais e Contato</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="dadosPessoais"
                               <?= !empty($s['visibility']['dadosPessoais'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div class="cv-field">
                        <label class="cv-field__label">Nome Social</label>
                        <input class="cv-input" data-st="nomeSocial"
                               placeholder="Como gostaria de ser chamado(a)"
                               value="<?= e($s['nomeSocial'] ?? '') ?>">
                    </div>
                    <div class="cv-field">
                        <label class="cv-field__label">Gênero</label>
                        <select class="cv-input" data-st="genero">
                            <?php foreach (['','Masculino','Feminino','Não-binário','Prefiro não informar'] as $opt): ?>
                                <option<?= ($s['genero'] ?? '') === $opt ? ' selected' : '' ?>><?= e($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cv-grid-2">
                        <div class="cv-field">
                            <label class="cv-field__label">País <span class="cv-required">*</span></label>
                            <input class="cv-input" data-st="pais" placeholder="Brasil"
                                   value="<?= e($s['pais'] ?? '') ?>">
                        </div>
                        <div class="cv-field">
                            <label class="cv-field__label">Nacionalidade <span class="cv-required">*</span></label>
                            <input class="cv-input" data-st="nacionalidade" placeholder="Brasileira"
                                   value="<?= e($s['nacionalidade'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="cv-field">
                        <label class="cv-field__label">Telefone Principal</label>
                        <input class="cv-input" data-st="telefone" placeholder="(00) 00000-0000"
                               value="<?= e($s['telefone'] ?? '') ?>">
                    </div>
                    <label class="cv-check-row">
                        <input type="checkbox" data-stb="telefoneWhatsApp"
                               <?= !empty($s['telefoneWhatsApp']) ? 'checked' : '' ?>>
                        <span>WhatsApp</span>
                    </label>
                    <div class="cv-field">
                        <label class="cv-field__label">Telefone Secundário</label>
                        <input class="cv-input" data-st="telefoneSecundario" placeholder="(00) 00000-0000"
                               value="<?= e($s['telefoneSecundario'] ?? '') ?>">
                    </div>
                    <label class="cv-check-row">
                        <input type="checkbox" data-stb="telefoneSecundarioWhatsApp"
                               <?= !empty($s['telefoneSecundarioWhatsApp']) ? 'checked' : '' ?>>
                        <span>WhatsApp</span>
                    </label>
                    <div class="cv-field">
                        <label class="cv-field__label">E-mail Principal <span class="cv-required">*</span></label>
                        <input class="cv-input" data-st="email" type="email"
                               value="<?= e($s['email'] ?? $userEmail) ?>">
                    </div>
                    <div class="cv-grid-2">
                        <div class="cv-field">
                            <label class="cv-field__label">Cidade</label>
                            <input class="cv-input" data-st="cidade" value="<?= e($s['cidade'] ?? '') ?>">
                        </div>
                        <div class="cv-field">
                            <label class="cv-field__label">Estado</label>
                            <input class="cv-input" data-st="estado" maxlength="2"
                                   value="<?= e($s['estado'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="cv-field">
                        <label class="cv-field__label">LinkedIn</label>
                        <input class="cv-input" data-st="linkedin"
                               placeholder="https://linkedin.com/in/seu-perfil"
                               value="<?= e($s['linkedin'] ?? '') ?>">
                    </div>
                    <label class="cv-check-row">
                        <input type="checkbox" data-stb="linkedinVisivel"
                               <?= ($s['linkedinVisivel'] ?? true) ? 'checked' : '' ?>>
                        <span>Visível no currículo</span>
                    </label>
                    <div class="cv-field">
                        <label class="cv-field__label">GitHub</label>
                        <input class="cv-input" data-st="github"
                               placeholder="https://github.com/seu-usuario"
                               value="<?= e($s['github'] ?? '') ?>">
                    </div>
                    <label class="cv-check-row">
                        <input type="checkbox" data-stb="githubVisivel"
                               <?= ($s['githubVisivel'] ?? true) ? 'checked' : '' ?>>
                        <span>Visível no currículo</span>
                    </label>
                    <div class="cv-field">
                        <label class="cv-field__label">Site Pessoal</label>
                        <input class="cv-input" data-st="site" placeholder="https://seu-site.com"
                               value="<?= e($s['site'] ?? '') ?>">
                    </div>
                    <label class="cv-check-row">
                        <input type="checkbox" data-stb="siteVisivel"
                               <?= ($s['siteVisivel'] ?? true) ? 'checked' : '' ?>>
                        <span>Visível no currículo</span>
                    </label>
                    <div class="cv-field">
                        <label class="cv-field__label">Portfólio</label>
                        <input class="cv-input" data-st="portfolio"
                               placeholder="https://seu-portfolio.com"
                               value="<?= e($s['portfolio'] ?? '') ?>">
                    </div>
                    <label class="cv-check-row">
                        <input type="checkbox" data-stb="portfolioVisivel"
                               <?= ($s['portfolioVisivel'] ?? true) ? 'checked' : '' ?>>
                        <span>Visível no currículo</span>
                    </label>
                </div>
            </div>

            <!-- § Formação Acadêmica --------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Formação Acadêmica</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="formacaoAcademica"
                               <?= ($s['visibility']['formacaoAcademica'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="formacoes-list"></div>
                    <button type="button" class="cv-add-btn" id="add-formacao">
                        <span class="cv-add-btn__icon">+</span> Adicionar Formação
                    </button>
                </div>
            </div>

            <!-- § Experiência Profissional --------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Experiência Profissional</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="experienciaProfissional"
                               <?= ($s['visibility']['experienciaProfissional'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="experiencias-list"></div>
                    <button type="button" class="cv-add-btn" id="add-experiencia">
                        <span class="cv-add-btn__icon">+</span> Adicionar Experiência
                    </button>
                </div>
            </div>

            <!-- § Licenças e Certificados ---------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Licenças e Certificados</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="licencas"
                               <?= ($s['visibility']['licencas'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="licencas-list"></div>
                    <button type="button" class="cv-add-btn" id="add-licenca">
                        <span class="cv-add-btn__icon">+</span> Adicionar Certificado
                    </button>
                </div>
            </div>

            <!-- § Projetos ------------------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Projetos</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="projetos"
                               <?= ($s['visibility']['projetos'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="projetos-list"></div>
                    <button type="button" class="cv-add-btn" id="add-projeto">
                        <span class="cv-add-btn__icon">+</span> Adicionar Projeto
                    </button>
                </div>
            </div>

            <!-- § Outros Cursos -------------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Outros Cursos</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="outrosCursos"
                               <?= ($s['visibility']['outrosCursos'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="outros-cursos-list"></div>
                    <button type="button" class="cv-add-btn" id="add-curso">
                        <span class="cv-add-btn__icon">+</span> Adicionar Curso
                    </button>
                </div>
            </div>

            <!-- § Habilidades ---------------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Habilidades</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="habilidades"
                               <?= ($s['visibility']['habilidades'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div>
                        <h3 class="cv-subheading">Hard Skills (Técnicas)</h3>
                        <div class="cv-skills-area" id="hard-skills-tags"></div>
                        <div class="cv-skill-input-row">
                            <input class="cv-input" id="nova-hard-skill" placeholder="Ex: Python, SQL, React…">
                            <button type="button" class="cv-skill-add-btn cv-skill-add-btn--hard" id="add-hard-skill">+</button>
                        </div>
                    </div>
                    <div>
                        <h3 class="cv-subheading">Soft Skills (Comportamentais)</h3>
                        <div class="cv-skills-area" id="soft-skills-tags"></div>
                        <div class="cv-skill-input-row">
                            <input class="cv-input" id="nova-soft-skill" placeholder="Ex: Liderança, Criatividade…">
                            <button type="button" class="cv-skill-add-btn cv-skill-add-btn--soft" id="add-soft-skill">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- § Idiomas -------------------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Idiomas</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="idiomas"
                               <?= ($s['visibility']['idiomas'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="idiomas-list"></div>
                    <button type="button" class="cv-add-btn" id="add-idioma">
                        <span class="cv-add-btn__icon">+</span> Adicionar Idioma
                    </button>
                </div>
            </div>

            <!-- § Diversidade ---------------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Diversidade</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="diversidade"
                               <?= ($s['visibility']['diversidade'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body">
                    <div id="diversidade-list" class="cv-stack"></div>
                </div>
            </div>

            <!-- § Horários de Contato -------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Horários de Contato</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="horariosContato"
                               <?= ($s['visibility']['horariosContato'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div id="horarios-list"></div>
                    <div class="cv-grid-2 mt-2">
                        <div class="cv-field">
                            <label class="cv-field__label">Disponibilidade de Mudança</label>
                            <select class="cv-input" id="disponibilidade-mudanca">
                                <?php foreach (['','Sim','Não'] as $opt): ?>
                                    <option<?= ($s['disponibilidadeMudanca'] ?? '') === $opt ? ' selected' : '' ?>><?= e($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="cv-field">
                            <label class="cv-field__label">Disponibilidade de Viagem</label>
                            <select class="cv-input" id="disponibilidade-viagem">
                                <?php foreach (['','Sim','Não'] as $opt): ?>
                                    <option<?= ($s['disponibilidadeViagem'] ?? '') === $opt ? ' selected' : '' ?>><?= e($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- § Tipo de Trabalho Buscado --------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Tipo de Trabalho Buscado</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="tipoTrabalho"
                               <?= ($s['visibility']['tipoTrabalho'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <?php foreach (['Jovem aprendiz','Estágio','Trainee','CLT','PJ','Freelancer','Projeto','Temporário'] as $tipo): ?>
                        <label class="cv-check-row">
                            <input type="checkbox" data-tipo-trabalho="1" value="<?= e($tipo) ?>"
                                   <?= in_array($tipo, $s['tiposTrabalho'] ?? [], true) ? 'checked' : '' ?>>
                            <span><?= e($tipo) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- § Disponibilidade e Modalidade ----------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Disponibilidade e Modalidade</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="disponibilidade"
                               <?= ($s['visibility']['disponibilidade'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div class="cv-field">
                        <label class="cv-field__label">Disponibilidade para início</label>
                        <select class="cv-input" id="disponibilidade-inicio">
                            <?php foreach (['Imediata','15 dias','30 dias','60 dias'] as $opt): ?>
                                <option<?= ($s['disponibilidade'] ?? 'Imediata') === $opt ? ' selected' : '' ?>><?= e($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cv-field">
                        <label class="cv-field__label">Modelo de Trabalho</label>
                        <?php foreach (['Presencial','Híbrido','100% Remoto'] as $modelo): ?>
                            <label class="cv-check-row">
                                <input type="checkbox" data-modelo-trabalho="1" value="<?= e($modelo) ?>"
                                       <?= in_array($modelo, $s['modeloTrabalho'] ?? [], true) ? 'checked' : '' ?>>
                                <span><?= e($modelo) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="cv-field">
                        <label class="cv-field__label">Jornada</label>
                        <select class="cv-input" id="jornada">
                            <?php foreach (['Período Integral','Meio Período','Flexível'] as $opt): ?>
                                <option<?= ($s['jornada'] ?? 'Período Integral') === $opt ? ' selected' : '' ?>><?= e($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- § Pretensão Salarial --------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Pretensão Salarial</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="pretensaoSalarial"
                               <?= ($s['visibility']['pretensaoSalarial'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body cv-stack">
                    <div class="cv-item cv-item--compact">
                        <label class="cv-check-row">
                            <input type="checkbox" id="bolsa-deslocamento"
                                   <?= !empty($s['bolsaDeslocamento']) ? 'checked' : '' ?>>
                            <span>Bolsa para cobrir deslocamento e alimentação</span>
                        </label>
                        <?php if (!empty($s['bolsaDeslocamento'])): ?>
                        <div class="cv-sub-check">
                            <label class="cv-check-row cv-check-row--small">
                                <input type="checkbox" id="bolsa-deslocamento-visivel"
                                       <?= ($s['bolsaDeslocamentoVisivel'] ?? true) ? 'checked' : '' ?>>
                                <span>Visível no currículo</span>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="cv-item cv-item--compact">
                        <div class="cv-field">
                            <label class="cv-field__label">A considerar a partir de</label>
                            <input class="cv-input" id="considerar-partir" placeholder="R$ 3.000,00"
                                   value="<?= e($s['considerarAPartir'] ?? '') ?>">
                        </div>
                        <label class="cv-check-row cv-check-row--small">
                            <input type="checkbox" id="considerar-visivel"
                                   <?= ($s['considerarAPartirVisivel'] ?? true) ? 'checked' : '' ?>>
                            <span>Visível no currículo</span>
                        </label>
                    </div>
                    <div class="cv-item cv-item--compact">
                        <label class="cv-field__label">Faixa Salarial Desejada</label>
                        <div class="cv-grid-2">
                            <div class="cv-field">
                                <label class="cv-field__label">Mínima</label>
                                <input class="cv-input" id="faixa-minima" placeholder="R$ 3.000,00"
                                       value="<?= e($s['faixaMinima'] ?? '') ?>">
                            </div>
                            <div class="cv-field">
                                <label class="cv-field__label">Máxima</label>
                                <input class="cv-input" id="faixa-maxima" placeholder="R$ 5.000,00"
                                       value="<?= e($s['faixaMaxima'] ?? '') ?>">
                            </div>
                        </div>
                        <label class="cv-check-row cv-check-row--small">
                            <input type="checkbox" id="faixa-visivel"
                                   <?= ($s['faixaSalarialVisivel'] ?? true) ? 'checked' : '' ?>>
                            <span>Visível no currículo</span>
                        </label>
                    </div>
                    <div class="cv-item cv-item--compact">
                        <label class="cv-check-row">
                            <input type="checkbox" id="exp-pratica"
                                   <?= !empty($s['experienciaPratica']) ? 'checked' : '' ?>>
                            <span>Quero adquirir experiência prática na área</span>
                        </label>
                        <?php if (!empty($s['experienciaPratica'])): ?>
                        <div class="cv-sub-check">
                            <label class="cv-check-row cv-check-row--small">
                                <input type="checkbox" id="exp-pratica-visivel"
                                       <?= ($s['experienciaPraticaVisivel'] ?? true) ? 'checked' : '' ?>>
                                <span>Visível no currículo</span>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- § Permissão de Contato ------------------------------------- -->
            <div class="cv-card">
                <div class="cv-card__body">
                    <label class="cv-check-row cv-check-row--large">
                        <input type="checkbox" data-stb="permissaoContato"
                               <?= !empty($s['permissaoContato']) ? 'checked' : '' ?>>
                        <span>Autorizo empresas parceiras do SENAC a entrarem em contato comigo</span>
                    </label>
                </div>
            </div>

            <!-- § Sobre Mim ------------------------------------------------ -->
            <div class="cv-card">
                <div class="cv-card__header">
                    <h2 class="cv-card__title">Sobre Mim</h2>
                    <label class="cv-card__visibility">
                        <input type="checkbox" class="cv-vis-check" data-vis-key="sobre"
                               <?= ($s['visibility']['sobre'] ?? true) ? 'checked' : '' ?>>
                        <span>Aparecer</span>
                    </label>
                </div>
                <div class="cv-card__body">
                    <textarea class="cv-input cv-textarea cv-textarea--tall" id="sobre-textarea"
                              maxlength="2600"
                              placeholder="Conte um pouco sobre você, seus objetivos profissionais e o que te motiva…"><?= e($s['sobre'] ?? '') ?></textarea>
                    <p class="cv-char-count" id="sobre-count">0/2600</p>
                </div>
            </div>

            <!-- § Sticky Save Button --------------------------------------- -->
            <div class="cv-save-bar">
                <button type="button" id="btn-save" class="cv-save-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Salvar Currículo
                </button>
                <span id="save-status" class="cv-save-status" role="alert" aria-live="polite"></span>
            </div>

        </div><!-- /curriculo-editor -->

        <!-- RIGHT: preview ──────────────────────────────────────────────── -->
        <div class="curriculo-preview-pane">
            <div class="cv-preview-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" class="cv-preview-header__icon" aria-hidden="true">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Preview do Currículo</span>
            </div>
            <div id="curriculo-preview" class="curriculo-preview"></div>
        </div>

    </div><!-- /curriculo-grid -->
</div><!-- /curriculo-app -->

<script src="/assets/js/curriculo.js?v=<?= e(asset_version('assets/js/curriculo.js')) ?>"></script>
