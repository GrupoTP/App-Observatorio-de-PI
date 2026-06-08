<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page">
    <div class="app-page-stack">

        <?php /* ─── Header ─── */ ?>
        <div class="app-pi-header__text">
            <a href="/admin/alunos" class="app-page-heading__back" aria-label="Voltar">
                <?= lucide_tag('arrow-left', 'app-page-heading__icon') ?>
            </a>
            <div>
                <h1 class="app-page-heading__title">Cadastrar Novo Usuário</h1>
                <p class="app-page-heading__subtitle">Preencha os dados obrigatórios para criar um novo usuário</p>
            </div>
        </div>

        <form method="post" action="/admin/alunos/novo" class="app-page-stack" enctype="multipart/form-data" id="cadastrar-form">
            <?= csrf_field() ?>

            <?php /* ─── Card 1: Required data ─── */ ?>
            <div class="app-form-card">
                <div class="app-pi-form-section-title">
                    <?= lucide_tag('user-plus', 'app-pi-detalhes__section-icon') ?>
                    <h2>Dados Obrigatórios</h2>
                </div>

                <?php /* Cargo toggle */ ?>
                <div class="app-field">
                    <label class="app-field__label">
                        Cargo <span class="app-field__required">*</span>
                    </label>
                    <div class="app-cadastrar__cargo-grid" id="cargo-grid">
                        <?php
                        $cargos = [
                            ['value' => 'aluno',      'label' => 'Aluno'],
                            ['value' => 'professor',   'label' => 'Professor'],
                            ['value' => 'coordenador', 'label' => 'Coordenador'],
                            ['value' => 'parceiro',    'label' => 'Parceiro'],
                        ];
                        foreach ($cargos as $c):
                        ?>
                        <label class="app-cadastrar__cargo-card" data-role="<?= e($c['value']) ?>">
                            <input type="radio" name="role" value="<?= e($c['value']) ?>"
                                   <?= $c['value'] === 'aluno' ? 'checked' : '' ?>
                                   onchange="onRoleChange(this)">
                            <span><?= e($c['label']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="cpf">
                            CPF <span class="app-field__required">*</span>
                        </label>
                        <input id="cpf" name="cpf" type="text"
                               class="app-field__input"
                               placeholder="000.000.000-00"
                               maxlength="14"
                               oninput="formatCPF(this)"
                               required>
                    </div>

                    <div class="app-field" id="matricula-field" style="">
                        <label class="app-field__label" for="matricula">
                            Matrícula <span class="app-field__required">*</span>
                        </label>
                        <input id="matricula" name="matricula" type="text"
                               class="app-field__input"
                               placeholder="Ex: 2026001234"
                               required>
                    </div>

                    <?php /* Empresa field (shown for parceiro) */ ?>
                    <div class="app-field" id="empresa-field" style="display:none">
                        <label class="app-field__label" for="empresa">
                            Empresa <span class="app-field__required">*</span>
                        </label>
                        <input id="empresa" name="empresa" type="text"
                               class="app-field__input"
                               placeholder="Ex: Empresa Parceira S.A.">
                    </div>
                </div>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="nome">
                            Nome <span class="app-field__required">*</span>
                        </label>
                        <input id="nome" name="nome" type="text"
                               class="app-field__input"
                               placeholder="Ex: João"
                               required>
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="sobrenome">
                            Sobrenome <span class="app-field__required">*</span>
                        </label>
                        <input id="sobrenome" name="sobrenome" type="text"
                               class="app-field__input"
                               placeholder="Ex: Silva Santos"
                               required>
                    </div>
                </div>

                <div class="app-field">
                    <label class="app-field__label" for="nome_social">Nome Social</label>
                    <input id="nome_social" name="nome_social" type="text"
                           class="app-field__input"
                           placeholder="Nome pelo qual prefere ser chamado(a) — opcional">
                    <p class="app-field__hint">Se preenchido, será utilizado em todo o sistema</p>
                </div>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="email">
                            E-mail Institucional <span class="app-field__required">*</span>
                        </label>
                        <input id="email" name="email" type="email"
                               class="app-field__input"
                               placeholder="usuario@senac.edu.br"
                               required>
                        <p class="app-field__hint">Será o e-mail de login. Só o Administrador pode alterá-lo.</p>
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="email_pessoal">E-mail Pessoal</label>
                        <input id="email_pessoal" name="email_pessoal" type="email"
                               class="app-field__input"
                               placeholder="Ex: joao@gmail.com">
                    </div>
                </div>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="senha">
                            Senha Inicial <span class="app-field__required">*</span>
                        </label>
                        <input id="senha" name="senha" type="password"
                               class="app-field__input"
                               value="senac123"
                               required>
                    </div>

                    <?php /* Turma: shown for aluno / professor */ ?>
                    <div class="app-field" id="turma-field">
                        <label class="app-field__label" for="cod_turma">Turma (aluno/professor)</label>
                        <select id="cod_turma" name="cod_turma" class="app-field__input">
                            <option value="">—</option>
                            <?php foreach ($turmas as $t): ?>
                            <option value="<?= e($t['cod_turma']) ?>">
                                <?= e($t['nome_curso']) ?> — <?= e($t['nome_turma']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php /* Status toggle */ ?>
                <div class="app-field">
                    <label class="app-field__label">Status do Perfil</label>
                    <div class="app-cadastrar__status-grid">
                        <label class="app-cadastrar__status-card app-cadastrar__status-card--active-opt" id="status-ativo-card">
                            <input type="radio" name="ativo" value="1" checked onchange="onStatusChange(this)">
                            <span>Ativo</span>
                        </label>
                        <label class="app-cadastrar__status-card" id="status-inativo-card">
                            <input type="radio" name="ativo" value="0" onchange="onStatusChange(this)">
                            <span>Inativo</span>
                        </label>
                    </div>
                </div>
            </div>

            <?php /* ─── Card 2: Optional data ─── */ ?>
            <div class="app-form-card">
                <div class="app-pi-form-section-title">
                    <h2>Dados Complementares <span style="font-weight:400;font-size:.85rem;color:var(--senac-gray-medium)">(Opcional)</span></h2>
                </div>
                <p class="app-pi-detalhes__label" style="margin-bottom:1rem">
                    Estes campos podem ser preenchidos agora ou posteriormente nas Configurações do Perfil.
                </p>

                <div class="app-form-grid app-form-grid--2">
                    <div class="app-field">
                        <label class="app-field__label" for="identidade_rg">Identidade (RG)</label>
                        <input id="identidade_rg" name="identidade_rg" type="text"
                               class="app-field__input" placeholder="00.000.000-0">
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="data_nascimento">Data de Nascimento</label>
                        <input id="data_nascimento" name="data_nascimento" type="date" class="app-field__input">
                    </div>

                    <div class="app-field">
                        <label class="app-field__label" for="telefone1">Telefone Principal</label>
                        <input id="telefone1" name="telefone1" type="text"
                               class="app-field__input" placeholder="(00) 00000-0000">
                        <label class="app-field__checkbox-label mt-1">
                            <input type="checkbox" name="telefone1_whatsapp" value="1">
                            Este telefone é WhatsApp
                        </label>
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="telefone2">Telefone Secundário</label>
                        <input id="telefone2" name="telefone2" type="text"
                               class="app-field__input" placeholder="(00) 00000-0000">
                        <label class="app-field__checkbox-label mt-1">
                            <input type="checkbox" name="telefone2_whatsapp" value="1">
                            Este telefone é WhatsApp
                        </label>
                    </div>

                    <div class="app-field">
                        <label class="app-field__label" for="cep">CEP</label>
                        <input id="cep" name="cep" type="text"
                               class="app-field__input" placeholder="00000-000">
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="cidade">Cidade</label>
                        <input id="cidade" name="cidade" type="text"
                               class="app-field__input" placeholder="Ex: Recife">
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="estado">Estado</label>
                        <input id="estado" name="estado" type="text"
                               class="app-field__input" placeholder="Ex: PE" maxlength="2">
                    </div>
                    <div class="app-field">
                        <label class="app-field__label" for="pais">País</label>
                        <input id="pais" name="pais" type="text"
                               class="app-field__input" placeholder="Brasil" value="Brasil">
                    </div>
                </div>

                <div class="app-field">
                    <label class="app-field__label" for="endereco">Endereço Completo</label>
                    <input id="endereco" name="endereco" type="text"
                           class="app-field__input" placeholder="Rua, número, complemento">
                </div>
                <div class="app-field">
                    <label class="app-field__label" for="bairro">Bairro</label>
                    <input id="bairro" name="bairro" type="text"
                           class="app-field__input" placeholder="Nome do bairro">
                </div>
            </div>

            <?php /* ─── LGPD info box ─── */ ?>
            <div class="app-info-box app-info-box--shield">
                <?= lucide_tag('shield', 'app-info-box__icon') ?>
                <div>
                    <p class="app-info-box__title"><strong>Proteção de Dados (LGPD — Lei nº 13.709/2018)</strong></p>
                    <ul class="app-info-box__list">
                        <li>Os dados cadastrados serão protegidos e utilizados exclusivamente para fins acadêmicos</li>
                        <li>O usuário receberá credenciais de acesso pelo e-mail cadastrado</li>
                        <li>A senha inicial deverá ser alterada no primeiro acesso</li>
                        <li>O e-mail primário só pode ser alterado pelo Administrador</li>
                    </ul>
                </div>
            </div>

            <?php /* ─── Actions ─── */ ?>
            <div class="app-form-actions">
                <a href="/admin/alunos" class="app-action-btn app-action-btn--outline">Cancelar</a>
                <button type="submit" class="app-action-btn app-action-btn--blue">
                    <?= lucide_tag('save', 'app-action-btn__icon') ?>
                    Cadastrar Usuário
                </button>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    var ROLES_WITH_TURMA = ['aluno', 'professor'];
    var ROLES_WITH_MATRICULA = ['aluno'];
    var ROLES_WITH_EMPRESA = ['parceiro'];

    function updateCargoCards() {
        var selected = document.querySelector('[name=role]:checked');
        if (!selected) return;
        document.querySelectorAll('.app-cadastrar__cargo-card').forEach(function (card) {
            var radio = card.querySelector('input[type=radio]');
            card.classList.toggle('app-cadastrar__cargo-card--active', radio.checked);
        });
    }

    function updateStatusCards() {
        var selected = document.querySelector('[name=ativo]:checked');
        if (!selected) return;
        document.getElementById('status-ativo-card').classList.toggle('app-cadastrar__status-card--selected-active', selected.value === '1');
        document.getElementById('status-inativo-card').classList.toggle('app-cadastrar__status-card--selected-inactive', selected.value === '0');
    }

    window.onRoleChange = function (radio) {
        var role = radio.value;
        var turmaField    = document.getElementById('turma-field');
        var matriculaField = document.getElementById('matricula-field');
        var empresaField  = document.getElementById('empresa-field');
        var matriculaInput = document.getElementById('matricula');
        var empresaInput   = document.getElementById('empresa');

        var showTurma     = ROLES_WITH_TURMA.includes(role);
        var showMatricula = ROLES_WITH_MATRICULA.includes(role);
        var showEmpresa   = ROLES_WITH_EMPRESA.includes(role);

        turmaField.style.display    = showTurma    ? '' : 'none';
        matriculaField.style.display = showMatricula ? '' : 'none';
        empresaField.style.display  = showEmpresa  ? '' : 'none';

        matriculaInput.required = showMatricula;
        empresaInput.required   = showEmpresa;

        updateCargoCards();
    };

    window.onStatusChange = function () { updateStatusCards(); };

    window.formatCPF = function (input) {
        var v = input.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = v;
    };

    updateCargoCards();
    updateStatusCards();
})();
</script>
