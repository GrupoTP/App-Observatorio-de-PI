/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 * Curriculo mini-app — vanilla JS, no external dependencies beyond lucide (loaded separately).
 */

window.addEventListener('load', function () {
    'use strict';

    const cfg = window.OPI_CURRICULO || {};
    const SAVE_URL = '/curriculo';
    const CSRF = cfg.csrf || '';
    const USER_NAME = cfg.userName || '';
    const USER_EMAIL = cfg.userEmail || '';

    // ── helpers ───────────────────────────────────────────────────────────
    function uid() { return Date.now().toString(36) + Math.random().toString(36).slice(2, 7); }
    function e(v) { return String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMonth(v) {
        if (!v) return '';
        const parts = v.split('-');
        const y = parts[0];
        const m = parts[1];
        if (!m) return y;
        const months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        const name = months[parseInt(m, 10) - 1];
        return name ? `${name}/${y}` : `${m}/${y}`;
    }

    // ── default state ─────────────────────────────────────────────────────
    const DIAS = ['Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado','Domingo'];
    const DEFAULT = {
        visibility: {
            dadosPessoais: true, formacaoAcademica: true, experienciaProfissional: true,
            licencas: true, projetos: true, outrosCursos: true, habilidades: true,
            idiomas: true, disponibilidade: true, sobre: true, diversidade: true,
            horariosContato: true, tipoTrabalho: true, pretensaoSalarial: true,
        },
        nomeSocial: '', genero: '', pais: '', nacionalidade: '',
        telefone: '', telefoneWhatsApp: false, telefoneSecundario: '', telefoneSecundarioWhatsApp: false,
        email: USER_EMAIL, linkedin: '', linkedinVisivel: true, github: '', githubVisivel: true,
        site: '', siteVisivel: true, portfolio: '', portfolioVisivel: true,
        fotoPerfilVisivel: true, cidade: '', estado: '', permissaoContato: false,
        sobre: '',
        formacoes: [],
        experiencias: [],
        licencas: [],
        projetos: [],
        outrosCursos: [],
        hardSkills: [], softSkills: [],
        idiomas: [],
        diversidade: [
            { tipo: 'LGBTQIA+', selecionado: false, aparecer: false },
            { tipo: 'Mulher', selecionado: false, aparecer: false },
            { tipo: 'PCD - Pessoas com Deficiência', selecionado: false, aparecer: false },
            { tipo: 'Negro', selecionado: false, aparecer: false },
            { tipo: 'Pardo', selecionado: false, aparecer: false },
            { tipo: 'Indígena', selecionado: false, aparecer: false },
        ],
        horariosSemana: DIAS.map(dia => ({
            dia,
            slots: [['', ''], ['', ''], ['', ''], ['', '']],
        })),
        disponibilidadeMudanca: '', disponibilidadeViagem: '',
        disponibilidade: 'Imediata', modeloTrabalho: [], jornada: 'Período Integral',
        tiposTrabalho: [],
        bolsaDeslocamento: false, bolsaDeslocamentoVisivel: true,
        considerarAPartir: '', considerarAPartirVisivel: true,
        faixaMinima: '', faixaMaxima: '', faixaSalarialVisivel: true,
        experienciaPratica: false, experienciaPraticaVisivel: true,
    };

    // ── state init ────────────────────────────────────────────────────────
    function mergeDeep(target, source) {
        const out = Object.assign({}, target);
        for (const key of Object.keys(source)) {
            if (source[key] !== null && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                out[key] = mergeDeep(target[key] ?? {}, source[key]);
            } else if (source[key] !== undefined) {
                out[key] = source[key];
            }
        }
        return out;
    }

    let state = mergeDeep(DEFAULT, cfg.data || {});
    // Guarantee arrays exist
    ['formacoes','experiencias','licencas','projetos','outrosCursos','hardSkills','softSkills','idiomas','diversidade','horariosSemana','tiposTrabalho','modeloTrabalho'].forEach(k => {
        if (!Array.isArray(state[k])) state[k] = DEFAULT[k];
    });
    if (state.horariosSemana.length === 0) state.horariosSemana = DEFAULT.horariosSemana;
    if (state.diversidade.length === 0) state.diversidade = DEFAULT.diversidade;

    // ── re-render helpers ─────────────────────────────────────────────────
    function set(path, value) {
        const parts = path.split('.');
        const last = parts.pop();
        let cur = state;
        for (const p of parts) cur = cur[p];
        cur[last] = value;
        renderPreview();
    }

    function setIdx(arr, idx, field, value) {
        state[arr][idx][field] = value;
        renderPreview();
    }

    function setSlot(diaIdx, slotIdx, startOrEnd, value) {
        state.horariosSemana[diaIdx].slots[slotIdx][startOrEnd === 'start' ? 0 : 1] = value;
        renderPreview();
    }

    // ── section card builder ──────────────────────────────────────────────
    function sectionCard(title, visKey, bodyHtml, { noVisibility = false } = {}) {
        return `
        <div class="cv-card">
          <div class="cv-card__header">
            <h2 class="cv-card__title">${e(title)}</h2>
            ${noVisibility ? '' : `
            <label class="cv-card__visibility">
              <input type="checkbox" class="cv-vis-check" data-vis-key="${e(visKey)}"
                     ${state.visibility[visKey] ? 'checked' : ''}>
              <span>Aparecer</span>
            </label>`}
          </div>
          <div class="cv-card__body">${bodyHtml}</div>
        </div>`;
    }

    function addBtn(label, id) {
        return `<button type="button" class="cv-add-btn" id="${id}">
                  <span class="cv-add-btn__icon">+</span> ${e(label)}
                </button>`;
    }

    function deleteBtn(listKey, idx) {
        return `<button type="button" class="cv-delete-btn"
                        data-delete-list="${listKey}" data-delete-idx="${idx}"
                        title="Remover">✕</button>`;
    }

    function field(label, inputHtml, required = false) {
        return `<div class="cv-field">
                  <label class="cv-field__label">${e(label)}${required ? ' <span class="cv-required">*</span>' : ''}</label>
                  ${inputHtml}
                </div>`;
    }

    function inp(attrs) {
        return `<input class="cv-input" ${attrs}>`;
    }

    function sel(attrs, options, current) {
        const opts = options.map(o => `<option${o === current ? ' selected' : ''}>${e(o)}</option>`).join('');
        return `<select class="cv-input" ${attrs}>${opts}</select>`;
    }

    function textarea(attrs, value) {
        return `<textarea class="cv-input cv-textarea" ${attrs}>${e(value)}</textarea>`;
    }

    function grid2(...items) {
        return `<div class="cv-grid-2">${items.join('')}</div>`;
    }

    function checkRow(label, checked, attrs) {
        return `<label class="cv-check-row">
                  <input type="checkbox" ${checked ? 'checked' : ''} ${attrs}> <span>${e(label)}</span>
                </label>`;
    }

    // ── section renderers ─────────────────────────────────────────────────

    function renderDadosPessoais() {
        const s = state;
        const body = `
          ${field('Nome Social', inp(`data-st="nomeSocial" value="${e(s.nomeSocial)}" placeholder="Como gostaria de ser chamado(a)"`))}
          ${field('Gênero', sel(`data-st="genero"`,
                ['', 'Masculino', 'Feminino', 'Não-binário', 'Prefiro não informar'], s.genero || ''))}
          ${grid2(
              field('País', inp(`data-st="pais" value="${e(s.pais)}" placeholder="Brasil"`), true),
              field('Nacionalidade', inp(`data-st="nacionalidade" value="${e(s.nacionalidade)}" placeholder="Brasileira"`), true)
          )}
          ${field('Telefone Principal', inp(`data-st="telefone" value="${e(s.telefone)}" placeholder="(00) 00000-0000"`))}
          ${checkRow('WhatsApp', s.telefoneWhatsApp, 'data-stb="telefoneWhatsApp"')}
          ${field('Telefone Secundário', inp(`data-st="telefoneSecundario" value="${e(s.telefoneSecundario)}" placeholder="(00) 00000-0000"`))}
          ${checkRow('WhatsApp', s.telefoneSecundarioWhatsApp, 'data-stb="telefoneSecundarioWhatsApp"')}
          ${field('E-mail Principal', inp(`data-st="email" type="email" value="${e(s.email)}"`), true)}
          ${grid2(
              field('Cidade', inp(`data-st="cidade" value="${e(s.cidade)}"`)),
              field('Estado', inp(`data-st="estado" value="${e(s.estado)}" maxlength="2"`))
          )}
          ${field('LinkedIn', inp(`data-st="linkedin" value="${e(s.linkedin)}" placeholder="https://linkedin.com/in/..."`)) +
            checkRow('Visível no currículo', s.linkedinVisivel, 'data-stb="linkedinVisivel"')}
          ${field('GitHub', inp(`data-st="github" value="${e(s.github)}" placeholder="https://github.com/..."`)) +
            checkRow('Visível no currículo', s.githubVisivel, 'data-stb="githubVisivel"')}
          ${field('Site Pessoal', inp(`data-st="site" value="${e(s.site)}" placeholder="https://seu-site.com"`)) +
            checkRow('Visível no currículo', s.siteVisivel, 'data-stb="siteVisivel"')}
          ${field('Portfólio', inp(`data-st="portfolio" value="${e(s.portfolio)}" placeholder="https://seu-portfolio.com"`)) +
            checkRow('Visível no currículo', s.portfolioVisivel, 'data-stb="portfolioVisivel"')}`;
        return sectionCard('Dados Pessoais e Contato', 'dadosPessoais', body);
    }

    function formacaoItemHTML(f, idx) {
        return `<div class="cv-item" data-item-id="${f.id}">
          <div class="cv-item__header">
            ${sel(`data-il-sel="formacoes" data-il-idx="${idx}" data-il-field="nivel"`,
                ['Ensino Médio','Técnico','Tecnólogo','Graduação','Pós-Graduação','Mestrado','Doutorado'], f.nivel)}
            ${deleteBtn('formacoes', idx)}
          </div>
          ${field('Instituição', inp(`data-il="formacoes" data-il-idx="${idx}" data-il-field="instituicao" value="${e(f.instituicao)}"`))}
          ${field('Curso', inp(`data-il="formacoes" data-il-idx="${idx}" data-il-field="curso" value="${e(f.curso)}"`))}
          ${grid2(
              field('Início', inp(`data-il="formacoes" data-il-idx="${idx}" data-il-field="dataInicio" type="month" value="${e(f.dataInicio)}"`)),
              field('Fim', inp(`data-il="formacoes" data-il-idx="${idx}" data-il-field="dataFim" type="month" value="${e(f.dataFim)}"`)
          ))}
          ${sel(`data-il-sel="formacoes" data-il-idx="${idx}" data-il-field="status"`,
              ['Cursando','Concluído','Trancado'], f.status)}
        </div>`;
    }

    function renderFormacoes() {
        const cont = document.getElementById('formacoes-list');
        if (!cont) return;
        cont.innerHTML = state.formacoes.map((f, i) => formacaoItemHTML(f, i)).join('');
    }

    function experienciaItemHTML(exp, idx) {
        return `<div class="cv-item" data-item-id="${exp.id}">
          <div class="cv-item__header">
            <span class="cv-item__label">Experiência ${idx + 1}</span>
            ${deleteBtn('experiencias', idx)}
          </div>
          ${field('Cargo', inp(`data-il="experiencias" data-il-idx="${idx}" data-il-field="cargo" value="${e(exp.cargo)}"`))}
          ${field('Empresa', inp(`data-il="experiencias" data-il-idx="${idx}" data-il-field="empresa" value="${e(exp.empresa)}"`))}
          ${sel(`data-il-sel="experiencias" data-il-idx="${idx}" data-il-field="tipo"`,
              ['CLT','PJ','Estágio','Freelance','Voluntário'], exp.tipo)}
          ${grid2(
              field('Início', inp(`data-il="experiencias" data-il-idx="${idx}" data-il-field="dataInicio" type="month" value="${e(exp.dataInicio)}"`)),
              field('Fim', inp(`data-il="experiencias" data-il-idx="${idx}" data-il-field="dataFim" type="month" value="${e(exp.dataFim)}"${exp.atual ? ' disabled' : ''}`)
          ))}
          ${checkRow('Trabalho aqui atualmente', exp.atual, `data-ilb="experiencias" data-ilb-idx="${idx}" data-ilb-field="atual"`)}
          ${field('Descrição das atividades', textarea(`data-il="experiencias" data-il-idx="${idx}" data-il-field="descricao" rows="3"`, exp.descricao))}
        </div>`;
    }

    function renderExperiencias() {
        const cont = document.getElementById('experiencias-list');
        if (!cont) return;
        cont.innerHTML = state.experiencias.map((exp, i) => experienciaItemHTML(exp, i)).join('');
    }

    function licencaItemHTML(l, idx) {
        return `<div class="cv-item" data-item-id="${l.id}">
          <div class="cv-item__header">
            <span class="cv-item__label">Certificado ${idx + 1}</span>
            ${deleteBtn('licencas', idx)}
          </div>
          ${field('Nome da Certificação', inp(`data-il="licencas" data-il-idx="${idx}" data-il-field="nome" value="${e(l.nome)}"`))}
          ${field('Instituição Emissora', inp(`data-il="licencas" data-il-idx="${idx}" data-il-field="instituicao" value="${e(l.instituicao)}"`))}
          ${grid2(
              field('Data de Emissão', inp(`data-il="licencas" data-il-idx="${idx}" data-il-field="dataEmissao" type="month" value="${e(l.dataEmissao)}"`)),
              field('Data de Validade', inp(`data-il="licencas" data-il-idx="${idx}" data-il-field="dataValidade" type="month" value="${e(l.dataValidade || '')}"`)
          ))}
          ${field('ID da Credencial', inp(`data-il="licencas" data-il-idx="${idx}" data-il-field="credencial" value="${e(l.credencial || '')}" placeholder="Código de verificação"`))}
        </div>`;
    }

    function renderLicencas() {
        const cont = document.getElementById('licencas-list');
        if (!cont) return;
        cont.innerHTML = state.licencas.map((l, i) => licencaItemHTML(l, i)).join('');
    }

    function projetoItemHTML(p, idx) {
        return `<div class="cv-item" data-item-id="${p.id}">
          <div class="cv-item__header">
            <span class="cv-item__label">Projeto ${idx + 1}</span>
            ${deleteBtn('projetos', idx)}
          </div>
          ${field('Título do Projeto', inp(`data-il="projetos" data-il-idx="${idx}" data-il-field="titulo" value="${e(p.titulo)}"`))}
          ${field('Descrição', textarea(`data-il="projetos" data-il-idx="${idx}" data-il-field="descricao" rows="3"`, p.descricao))}
          ${field('Tecnologias Utilizadas', inp(`data-il="projetos" data-il-idx="${idx}" data-il-field="tecnologias" value="${e(p.tecnologias)}" placeholder="React, Node.js, PostgreSQL"`))}
          ${field('Link do Projeto', inp(`data-il="projetos" data-il-idx="${idx}" data-il-field="link" value="${e(p.link || '')}" placeholder="https://github.com/usuario/projeto"`))}
          ${grid2(
              field('Início', inp(`data-il="projetos" data-il-idx="${idx}" data-il-field="dataInicio" type="month" value="${e(p.dataInicio)}"`)),
              field('Fim', inp(`data-il="projetos" data-il-idx="${idx}" data-il-field="dataFim" type="month" value="${e(p.dataFim || '')}"`)
          ))}
        </div>`;
    }

    function renderProjetos() {
        const cont = document.getElementById('projetos-list');
        if (!cont) return;
        cont.innerHTML = state.projetos.map((p, i) => projetoItemHTML(p, i)).join('');
    }

    function cursoItemHTML(c, idx) {
        return `<div class="cv-item" data-item-id="${c.id}">
          <div class="cv-item__header">
            <span class="cv-item__label">Curso ${idx + 1}</span>
            ${deleteBtn('outrosCursos', idx)}
          </div>
          ${field('Nome do Curso', inp(`data-il="outrosCursos" data-il-idx="${idx}" data-il-field="nome" value="${e(c.nome)}"`))}
          ${field('Instituição', inp(`data-il="outrosCursos" data-il-idx="${idx}" data-il-field="instituicao" value="${e(c.instituicao)}"`))}
          ${field('Carga Horária', inp(`data-il="outrosCursos" data-il-idx="${idx}" data-il-field="cargaHoraria" value="${e(c.cargaHoraria)}" placeholder="Ex: 40 horas"`))}
          ${grid2(
              field('Início', inp(`data-il="outrosCursos" data-il-idx="${idx}" data-il-field="dataInicio" type="month" value="${e(c.dataInicio)}"`)),
              field('Fim', inp(`data-il="outrosCursos" data-il-idx="${idx}" data-il-field="dataFim" type="month" value="${e(c.dataFim)}"`)
          ))}
        </div>`;
    }

    function renderOutrosCursos() {
        const cont = document.getElementById('outros-cursos-list');
        if (!cont) return;
        cont.innerHTML = state.outrosCursos.map((c, i) => cursoItemHTML(c, i)).join('');
    }

    function renderHabilidades() {
        const hardCont = document.getElementById('hard-skills-tags');
        const softCont = document.getElementById('soft-skills-tags');
        if (hardCont) {
            hardCont.innerHTML = state.hardSkills.map(sk =>
                `<span class="cv-skill-tag cv-skill-tag--hard">${e(sk)} <button type="button" data-remove-hard="${e(sk)}" class="cv-skill-remove">×</button></span>`
            ).join('');
        }
        if (softCont) {
            softCont.innerHTML = state.softSkills.map(sk =>
                `<span class="cv-skill-tag cv-skill-tag--soft">${e(sk)} <button type="button" data-remove-soft="${e(sk)}" class="cv-skill-remove">×</button></span>`
            ).join('');
        }
    }

    function idiomaItemHTML(id, idx) {
        const levels = ['Básico','Intermediário','Avançado','Fluente','Nativo'];
        return `<div class="cv-item" data-item-id="${id.id}">
          <div class="cv-item__header">
            ${field('Idioma', inp(`data-il="idiomas" data-il-idx="${idx}" data-il-field="idioma" value="${e(id.idioma)}"`))}
            ${deleteBtn('idiomas', idx)}
          </div>
          <div class="cv-grid-3">
            <div class="cv-field"><label class="cv-field__label">Fala</label>
              ${sel(`data-il-sel="idiomas" data-il-idx="${idx}" data-il-field="nivelFala"`, levels, id.nivelFala)}
            </div>
            <div class="cv-field"><label class="cv-field__label">Leitura</label>
              ${sel(`data-il-sel="idiomas" data-il-idx="${idx}" data-il-field="nivelLeitura"`, levels, id.nivelLeitura)}
            </div>
            <div class="cv-field"><label class="cv-field__label">Escrita</label>
              ${sel(`data-il-sel="idiomas" data-il-idx="${idx}" data-il-field="nivelEscrita"`, levels, id.nivelEscrita)}
            </div>
          </div>
        </div>`;
    }

    function renderIdiomas() {
        const cont = document.getElementById('idiomas-list');
        if (!cont) return;
        cont.innerHTML = state.idiomas.map((id, i) => idiomaItemHTML(id, i)).join('');
    }

    function renderDiversidade() {
        const cont = document.getElementById('diversidade-list');
        if (!cont) return;
        cont.innerHTML = state.diversidade.map((d, i) => `
          <div class="cv-item cv-item--compact">
            ${checkRow(d.tipo, d.selecionado, `data-div-idx="${i}" data-div-field="selecionado"`)}
            ${d.selecionado ? `<div class="cv-div-sub">${checkRow('Aparecer no currículo', d.aparecer, `data-div-idx="${i}" data-div-field="aparecer"`)}</div>` : ''}
          </div>`
        ).join('');
    }

    function renderHorarios() {
        const cont = document.getElementById('horarios-list');
        if (!cont) return;
        cont.innerHTML = state.horariosSemana.map((h, dIdx) => `
          <div class="cv-item">
            <h3 class="cv-item__label">${e(h.dia)}</h3>
            <div class="cv-horario-grid">
              ${h.slots.map((sl, sIdx) => `
                <div class="cv-horario-row">
                  <input type="time" class="cv-input cv-input--time"
                         value="${e(sl[0])}" placeholder="Início"
                         data-horario-dia="${dIdx}" data-horario-slot="${sIdx}" data-horario-end="start">
                  <span class="cv-horario-sep">até</span>
                  <input type="time" class="cv-input cv-input--time"
                         value="${e(sl[1])}" placeholder="Fim"
                         data-horario-dia="${dIdx}" data-horario-slot="${sIdx}" data-horario-end="end">
                </div>`).join('')}
          </div></div>`
        ).join('');
    }

    // ── preview renderer ──────────────────────────────────────────────────
    function pvSec(title, bodyHtml) {
        return `<div class="pv-section">
                  <h3 class="pv-section__title">${e(title)}</h3>
                  <div class="pv-section__body">${bodyHtml}</div>
                </div>`;
    }

    function pvSkillTag(sk, cls) {
        return `<span class="pv-skill ${cls}">${e(sk)}</span>`;
    }

    function renderPreview() {
        const pv = document.getElementById('curriculo-preview');
        if (!pv) return;
        const s = state;
        let html = '';

        // Header
        html += `<div class="pv-header">
          <div class="pv-header__name">${e(s.nomeSocial || USER_NAME || 'Seu Nome')}</div>
          <div class="pv-header__course">Análise e Desenvolvimento de Sistemas</div>`;
        if (s.visibility.dadosPessoais) {
            html += `<div class="pv-header__contact">`;
            if (s.genero) html += `<span>${e(s.genero)}</span>`;
            if (s.telefone) html += `<span>${e(s.telefone)}${s.telefoneWhatsApp ? ' (WhatsApp)' : ''}</span>`;
            if (s.telefoneSecundario) html += `<span>${e(s.telefoneSecundario)}${s.telefoneSecundarioWhatsApp ? ' (WhatsApp)' : ''}</span>`;
            if (s.email) html += `<span>${e(s.email)}</span>`;
            if (s.cidade && s.estado) html += `<span>${e(s.cidade)} — ${e(s.estado)}</span>`;
            if (s.pais && s.nacionalidade) html += `<span>${e(s.pais)} — ${e(s.nacionalidade)}</span>`;
            if (s.linkedin && s.linkedinVisivel) html += `<span class="pv-link">${e(s.linkedin)}</span>`;
            if (s.github && s.githubVisivel) html += `<span class="pv-link">${e(s.github)}</span>`;
            if (s.site && s.siteVisivel) html += `<span class="pv-link">${e(s.site)}</span>`;
            if (s.portfolio && s.portfolioVisivel) html += `<span class="pv-link">${e(s.portfolio)}</span>`;
            html += `</div>`;
        }
        html += `</div>`;

        // Sobre
        if (s.visibility.sobre && s.sobre) {
            html += pvSec('Sobre', `<p class="pv-text">${e(s.sobre)}</p>`);
        }

        // Formação
        if (s.visibility.formacaoAcademica && s.formacoes.length > 0) {
            const items = s.formacoes.map(f => `
              <div class="pv-item">
                <p class="pv-item__title">${e(f.curso || 'Curso')}</p>
                <p class="pv-item__sub">${e(f.instituicao || 'Instituição')}</p>
                <p class="pv-item__meta">${e(f.nivel)} · ${e(f.status)}${f.dataInicio ? ` · ${fmtMonth(f.dataInicio)} – ${f.dataFim ? fmtMonth(f.dataFim) : 'Presente'}` : ''}</p>
              </div>`).join('');
            html += pvSec('Formação Acadêmica', items);
        }

        // Experiência
        if (s.visibility.experienciaProfissional && s.experiencias.length > 0) {
            const items = s.experiencias.map(exp => `
              <div class="pv-item">
                <p class="pv-item__title">${e(exp.cargo || 'Cargo')}</p>
                <p class="pv-item__sub">${e(exp.empresa || 'Empresa')} · ${e(exp.tipo)}</p>
                <p class="pv-item__meta">${fmtMonth(exp.dataInicio)} – ${exp.atual ? 'Presente' : fmtMonth(exp.dataFim)}</p>
                ${exp.descricao ? `<p class="pv-item__desc">${e(exp.descricao)}</p>` : ''}
              </div>`).join('');
            html += pvSec('Experiência Profissional', items);
        }

        // Licenças
        if (s.visibility.licencas && s.licencas.length > 0) {
            const items = s.licencas.map(l => `
              <div class="pv-item">
                <p class="pv-item__title">${e(l.nome || 'Certificação')}</p>
                <p class="pv-item__meta">${e(l.instituicao || 'Instituição')}${l.dataEmissao ? ` · Emitido ${fmtMonth(l.dataEmissao)}` : ''}${l.dataValidade ? ` · Válido até ${fmtMonth(l.dataValidade)}` : ''}</p>
                ${l.credencial ? `<p class="pv-item__meta">Credencial: ${e(l.credencial)}</p>` : ''}
              </div>`).join('');
            html += pvSec('Licenças e Certificados', items);
        }

        // Projetos
        if (s.visibility.projetos && s.projetos.length > 0) {
            const items = s.projetos.map(p => `
              <div class="pv-item">
                <p class="pv-item__title">${e(p.titulo || 'Projeto')}</p>
                ${p.descricao ? `<p class="pv-item__desc">${e(p.descricao)}</p>` : ''}
                ${p.tecnologias ? `<p class="pv-item__meta">Tecnologias: ${e(p.tecnologias)}</p>` : ''}
                ${p.link ? `<p class="pv-link pv-item__meta">${e(p.link)}</p>` : ''}
              </div>`).join('');
            html += pvSec('Projetos', items);
        }

        // Outros Cursos
        if (s.visibility.outrosCursos && s.outrosCursos.length > 0) {
            const items = s.outrosCursos.map(c => `
              <div class="pv-item">
                <p class="pv-item__title">${e(c.nome || 'Curso')}</p>
                <p class="pv-item__meta">${e(c.instituicao || 'Instituição')}${c.cargaHoraria ? ` · ${e(c.cargaHoraria)}` : ''}</p>
              </div>`).join('');
            html += pvSec('Outros Cursos', items);
        }

        // Habilidades
        if (s.visibility.habilidades && (s.hardSkills.length > 0 || s.softSkills.length > 0)) {
            let body = '';
            if (s.hardSkills.length > 0) {
                body += `<p class="pv-skills__label">Hard Skills</p>
                         <div class="pv-skills">${s.hardSkills.map(sk => pvSkillTag(sk, 'pv-skill--hard')).join('')}</div>`;
            }
            if (s.softSkills.length > 0) {
                body += `<p class="pv-skills__label">Soft Skills</p>
                         <div class="pv-skills">${s.softSkills.map(sk => pvSkillTag(sk, 'pv-skill--soft')).join('')}</div>`;
            }
            html += pvSec('Habilidades', body);
        }

        // Idiomas
        if (s.visibility.idiomas && s.idiomas.length > 0) {
            const items = s.idiomas.map(id => `
              <div class="pv-item">
                <p class="pv-item__title">${e(id.idioma || 'Idioma')}</p>
                <p class="pv-item__meta">Fala: ${e(id.nivelFala)} · Leitura: ${e(id.nivelLeitura)} · Escrita: ${e(id.nivelEscrita)}</p>
              </div>`).join('');
            html += pvSec('Idiomas', items);
        }

        // Diversidade
        const divItems = s.diversidade.filter(d => d.selecionado && d.aparecer);
        if (s.visibility.diversidade && divItems.length > 0) {
            html += pvSec('Diversidade', divItems.map(d => `<p class="pv-text">· ${e(d.tipo)}</p>`).join(''));
        }

        // Horários
        const horariosAtivos = s.horariosSemana.filter(h => h.slots.some(sl => sl[0] || sl[1]));
        if (s.visibility.horariosContato && horariosAtivos.length > 0) {
            const body = horariosAtivos.map(h => {
                const slotStr = h.slots.filter(sl => sl[0] && sl[1]).map(sl => `${sl[0]}–${sl[1]}`).join(', ');
                return slotStr ? `<p class="pv-text"><strong>${e(h.dia)}:</strong> ${slotStr}</p>` : '';
            }).join('') +
            (s.disponibilidadeMudanca ? `<p class="pv-text">Disponibilidade de mudança: ${e(s.disponibilidadeMudanca)}</p>` : '') +
            (s.disponibilidadeViagem ? `<p class="pv-text">Disponibilidade de viagem: ${e(s.disponibilidadeViagem)}</p>` : '');
            html += pvSec('Horários de Contato', body);
        }

        // Tipo de trabalho
        if (s.visibility.tipoTrabalho && s.tiposTrabalho.length > 0) {
            html += pvSec('Tipo de Trabalho Buscado', `<p class="pv-text">${e(s.tiposTrabalho.join(', '))}</p>`);
        }

        // Disponibilidade
        if (s.visibility.disponibilidade) {
            html += pvSec('Disponibilidade e Modalidade', `
              <p class="pv-text">Início: ${e(s.disponibilidade)}</p>
              ${s.modeloTrabalho.length ? `<p class="pv-text">Modelo: ${e(s.modeloTrabalho.join(', '))}</p>` : ''}
              <p class="pv-text">Jornada: ${e(s.jornada)}</p>`);
        }

        // Pretensão salarial
        if (s.visibility.pretensaoSalarial) {
            let body = '';
            if (s.bolsaDeslocamento && s.bolsaDeslocamentoVisivel) body += `<p class="pv-text">· Bolsa para cobrir deslocamento e alimentação</p>`;
            if (s.considerarAPartir && s.considerarAPartirVisivel) body += `<p class="pv-text">· A considerar a partir de ${e(s.considerarAPartir)}</p>`;
            if ((s.faixaMinima || s.faixaMaxima) && s.faixaSalarialVisivel) body += `<p class="pv-text">· Faixa salarial: ${e(s.faixaMinima || 'N/A')} a ${e(s.faixaMaxima || 'N/A')}</p>`;
            if (s.experienciaPratica && s.experienciaPraticaVisivel) body += `<p class="pv-text">· Busco adquirir experiência prática na área</p>`;
            if (body) html += pvSec('Pretensão Salarial', body);
        }

        // Permissão de contato
        if (s.permissaoContato) {
            html += `<div class="pv-permission">✓ Autorizado contato de empresas parceiras SENAC</div>`;
        }

        pv.innerHTML = html;
    }

    // ── render all dynamic sections ───────────────────────────────────────
    function renderAllDynamic() {
        renderFormacoes();
        renderExperiencias();
        renderLicencas();
        renderProjetos();
        renderOutrosCursos();
        renderHabilidades();
        renderIdiomas();
        renderDiversidade();
        renderHorarios();
        renderPreview();
    }

    // ── event delegation ──────────────────────────────────────────────────
    function bindEvents() {
        const editor = document.getElementById('curriculo-editor');
        if (!editor) return;

        editor.addEventListener('input', function (ev) {
            const el = ev.target;

            // Simple state field
            const st = el.dataset.st;
            if (st) { state[st] = el.value; renderPreview(); return; }

            // Item list field (text/textarea)
            if (el.dataset.il) {
                state[el.dataset.il][+el.dataset.ilIdx][el.dataset.ilField] = el.value;
                // Disable dataFim if atual checked
                if (el.dataset.ilField === 'atual') return;
                renderPreview();
                return;
            }

            // Sobre textarea
            if (el.id === 'sobre-textarea') { state.sobre = el.value; renderPreview(); return; }

            // Hard/soft skill input — live counter only
            const chars = document.getElementById('sobre-count');
            if (el.id === 'sobre-textarea' && chars) chars.textContent = `${el.value.length}/2600`;

            // Horários
            if (el.dataset.horarioDia !== undefined) {
                setSlot(+el.dataset.horarioDia, +el.dataset.horarioSlot, el.dataset.horarioEnd, el.value);
                return;
            }
        });

        editor.addEventListener('change', function (ev) {
            const el = ev.target;

            // Visibility checkbox
            if (el.classList.contains('cv-vis-check')) {
                state.visibility[el.dataset.visKey] = el.checked;
                renderPreview();
                return;
            }

            // Simple boolean field
            const stb = el.dataset.stb;
            if (stb) {
                state[stb] = el.checked;
                // If toggling atual, re-render experiencias to enable/disable dataFim
                if (stb === 'permissaoContato') { renderPreview(); return; }
                renderPreview();
                return;
            }

            // Simple select
            const st = el.dataset.st;
            if (st) { state[st] = el.value; renderPreview(); return; }

            // List item select
            if (el.dataset.ilSel) {
                state[el.dataset.ilSel][+el.dataset.ilIdx][el.dataset.ilField] = el.value;
                renderPreview();
                return;
            }

            // List item checkbox
            if (el.dataset.ilb) {
                state[el.dataset.ilb][+el.dataset.ilbIdx][el.dataset.ilbField] = el.checked;
                // Re-render to enable/disable dataFim
                if (el.dataset.ilb === 'experiencias') renderExperiencias();
                renderPreview();
                return;
            }

            // Diversidade
            if (el.dataset.divIdx !== undefined) {
                state.diversidade[+el.dataset.divIdx][el.dataset.divField] = el.checked;
                renderDiversidade();
                renderPreview();
                return;
            }

            // Horários select
            if (el.id === 'disponibilidade-mudanca') { state.disponibilidadeMudanca = el.value; renderPreview(); return; }
            if (el.id === 'disponibilidade-viagem') { state.disponibilidadeViagem = el.value; renderPreview(); return; }
            if (el.id === 'disponibilidade-inicio') { state.disponibilidade = el.value; renderPreview(); return; }
            if (el.id === 'jornada') { state.jornada = el.value; renderPreview(); return; }

            // Modelo trabalho checkbox (data-modelo-trabalho="1")
            if ('modeloTrabalho' in el.dataset) {
                if (el.checked) { if (!state.modeloTrabalho.includes(el.value)) state.modeloTrabalho.push(el.value); }
                else { state.modeloTrabalho = state.modeloTrabalho.filter(m => m !== el.value); }
                renderPreview(); return;
            }

            // Tipo trabalho checkbox (data-tipo-trabalho="1")
            if ('tipoTrabalho' in el.dataset) {
                if (el.checked) { if (!state.tiposTrabalho.includes(el.value)) state.tiposTrabalho.push(el.value); }
                else { state.tiposTrabalho = state.tiposTrabalho.filter(t => t !== el.value); }
                renderPreview(); return;
            }

            // Pretensão salarial booleans
            if (el.id === 'bolsa-deslocamento') { state.bolsaDeslocamento = el.checked; renderPreview(); return; }
            if (el.id === 'bolsa-deslocamento-visivel') { state.bolsaDeslocamentoVisivel = el.checked; renderPreview(); return; }
            if (el.id === 'considerar-visivel') { state.considerarAPartirVisivel = el.checked; renderPreview(); return; }
            if (el.id === 'faixa-visivel') { state.faixaSalarialVisivel = el.checked; renderPreview(); return; }
            if (el.id === 'exp-pratica') { state.experienciaPratica = el.checked; renderPreview(); return; }
            if (el.id === 'exp-pratica-visivel') { state.experienciaPraticaVisivel = el.checked; renderPreview(); return; }

            // Horários time inputs (change completes them)
            if (el.dataset.horarioDia !== undefined) {
                setSlot(+el.dataset.horarioDia, +el.dataset.horarioSlot, el.dataset.horarioEnd, el.value);
                return;
            }
        });

        editor.addEventListener('input', function (ev) {
            const el = ev.target;
            if (el.id === 'sobre-textarea') {
                state.sobre = el.value;
                const cnt = document.getElementById('sobre-count');
                if (cnt) cnt.textContent = `${el.value.length}/2600`;
                renderPreview();
            }
            if (el.id === 'considerar-partir') { state.considerarAPartir = el.value; renderPreview(); }
            if (el.id === 'faixa-minima') { state.faixaMinima = el.value; renderPreview(); }
            if (el.id === 'faixa-maxima') { state.faixaMaxima = el.value; renderPreview(); }
        });

        // Click delegation (add / remove items, skill remove)
        editor.addEventListener('click', function (ev) {
            const btn = ev.target.closest('button[data-delete-list]');
            if (btn) {
                const list = btn.dataset.deleteList;
                const idx = +btn.dataset.deleteIdx;
                state[list].splice(idx, 1);
                renderAllDynamic();
                return;
            }

            // Remove skill
            const rh = ev.target.closest('[data-remove-hard]');
            if (rh) { state.hardSkills = state.hardSkills.filter(s => s !== rh.dataset.removeHard); renderHabilidades(); renderPreview(); return; }
            const rs = ev.target.closest('[data-remove-soft]');
            if (rs) { state.softSkills = state.softSkills.filter(s => s !== rs.dataset.removeSoft); renderHabilidades(); renderPreview(); return; }

            // Add buttons
            const addId = ev.target.closest('.cv-add-btn')?.id;
            if (!addId) return;
            if (addId === 'add-formacao') { state.formacoes.push({ id: uid(), nivel: 'Graduação', instituicao: '', curso: '', dataInicio: '', dataFim: '', status: 'Cursando' }); renderFormacoes(); renderPreview(); }
            if (addId === 'add-experiencia') { state.experiencias.push({ id: uid(), cargo: '', empresa: '', tipo: 'CLT', dataInicio: '', dataFim: '', atual: false, descricao: '' }); renderExperiencias(); renderPreview(); }
            if (addId === 'add-licenca') { state.licencas.push({ id: uid(), nome: '', instituicao: '', dataEmissao: '', dataValidade: '', credencial: '' }); renderLicencas(); renderPreview(); }
            if (addId === 'add-projeto') { state.projetos.push({ id: uid(), titulo: '', descricao: '', tecnologias: '', link: '', dataInicio: '', dataFim: '' }); renderProjetos(); renderPreview(); }
            if (addId === 'add-curso') { state.outrosCursos.push({ id: uid(), nome: '', instituicao: '', cargaHoraria: '', dataInicio: '', dataFim: '' }); renderOutrosCursos(); renderPreview(); }
            if (addId === 'add-idioma') { state.idiomas.push({ id: uid(), idioma: '', nivelFala: 'Básico', nivelLeitura: 'Básico', nivelEscrita: 'Básico' }); renderIdiomas(); renderPreview(); }
        });

        // Hard/soft skill add buttons
        document.getElementById('add-hard-skill')?.addEventListener('click', function () {
            const inp = document.getElementById('nova-hard-skill');
            const v = inp?.value.trim();
            if (v && !state.hardSkills.includes(v)) { state.hardSkills.push(v); renderHabilidades(); renderPreview(); inp.value = ''; }
        });
        document.getElementById('add-soft-skill')?.addEventListener('click', function () {
            const inp = document.getElementById('nova-soft-skill');
            const v = inp?.value.trim();
            if (v && !state.softSkills.includes(v)) { state.softSkills.push(v); renderHabilidades(); renderPreview(); inp.value = ''; }
        });
        document.getElementById('nova-hard-skill')?.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter') { ev.preventDefault(); document.getElementById('add-hard-skill')?.click(); }
        });
        document.getElementById('nova-soft-skill')?.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter') { ev.preventDefault(); document.getElementById('add-soft-skill')?.click(); }
        });
    }

    // ── save ──────────────────────────────────────────────────────────────
    async function save() {
        const btn = document.getElementById('btn-save');
        const status = document.getElementById('save-status');
        if (btn) { btn.disabled = true; btn.textContent = 'Salvando…'; }
        if (status) { status.textContent = ''; status.className = 'cv-save-status'; }

        try {
            const res = await fetch(SAVE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
                body: JSON.stringify(state),
            });
            const json = await res.json();
            if (json.ok) {
                if (status) { status.textContent = 'Salvo com sucesso!'; status.className = 'cv-save-status cv-save-status--ok'; }
            } else {
                if (status) { status.textContent = json.error || 'Erro ao salvar.'; status.className = 'cv-save-status cv-save-status--err'; }
            }
        } catch {
            if (status) { status.textContent = 'Erro de rede. Tente novamente.'; status.className = 'cv-save-status cv-save-status--err'; }
        } finally {
            if (btn) { btn.disabled = false; btn.textContent = 'Salvar Currículo'; }
            setTimeout(() => { if (status) status.textContent = ''; }, 3500);
        }
    }

    document.getElementById('btn-save')?.addEventListener('click', save);

    // ── init ──────────────────────────────────────────────────────────────
    renderAllDynamic();

    // Sync sobre textarea initial count
    const sobre = document.getElementById('sobre-textarea');
    const sobreCount = document.getElementById('sobre-count');
    if (sobre && sobreCount) sobreCount.textContent = `${sobre.value.length}/2600`;

    bindEvents();
});
