<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="app-page app-page--medium">
    <div class="app-page-stack">
        <?php
        $headingTitle    = 'Configurações do Perfil';
        $headingSubtitle = 'Gerencie suas informações pessoais e preferências do sistema';
        $backUrl         = \App\Auth\SessionAuth::isAluno() ? '/dashboard' : '/admin/dashboard';
        require dirname(__DIR__) . '/partials/app-page-heading.php';
        ?>

        <form method="post" action="/configuracoes" class="app-form-stack" autocomplete="off"
              enctype="multipart/form-data">
            <?= csrf_field() ?>

            <?php /* ─── Read-only: main account data ─── */ ?>
            <section class="app-form-card app-config-section">
                <h2 class="app-config-section__title">
                    <?= lucide_tag('shield', 'app-config-section__title-icon') ?>
                    Dados principais
                    <span class="app-config-section__badge">Somente visualização</span>
                </h2>

                <div class="app-config-fields-grid">
                    <div class="app-field">
                        <label class="app-field__label">Nome completo</label>
                        <p class="app-field__readonly">
                            <?= e(trim(($user['nome_civil_nome'] ?? '') . ' ' . ($user['nome_civil_sobrenome'] ?? ''))) ?>
                        </p>
                    </div>

                    <?php if (!empty($user['nome_social_nome'])): ?>
                    <div class="app-field">
                        <label class="app-field__label">Nome social</label>
                        <p class="app-field__readonly">
                            <?= e(trim(($user['nome_social_nome'] ?? '') . ' ' . ($user['nome_social_sobrenome'] ?? ''))) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($user['cpf'])): ?>
                    <div class="app-field">
                        <label class="app-field__label">CPF</label>
                        <p class="app-field__readonly"><?= e($user['cpf']) ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="app-field">
                        <label class="app-field__label">E-mail institucional</label>
                        <p class="app-field__readonly"><?= e($user['email_institucional'] ?? '') ?></p>
                        <p class="app-field__helper mb-0">Apenas o administrador pode alterar este e-mail</p>
                    </div>

                    <?php if (!empty($user['matricula'])): ?>
                    <div class="app-field">
                        <label class="app-field__label">Matrícula</label>
                        <p class="app-field__readonly"><?= e($user['matricula']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($roles)): ?>
                    <div class="app-field">
                        <label class="app-field__label">Perfil(is)</label>
                        <div class="app-config-roles">
                            <?php foreach ($roles as $role): ?>
                            <span class="app-config-role-badge">
                                <?= e(ucfirst($role)) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($user['data_criacao'])): ?>
                    <div class="app-field">
                        <label class="app-field__label">Data de criação</label>
                        <p class="app-field__readonly">
                            <?= e(date('d/m/Y', strtotime($user['data_criacao']))) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($user['ultimo_login'])): ?>
                    <div class="app-field">
                        <label class="app-field__label">Último login</label>
                        <p class="app-field__readonly">
                            <?= e(date('d/m/Y \à\s H:i', strtotime($user['ultimo_login']))) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php /* ─── Editable: personal data ─── */ ?>
            <section class="app-form-card app-config-section">
                <h2 class="app-config-section__title">
                    <?= lucide_tag('user', 'app-config-section__title-icon') ?>
                    Dados pessoais
                </h2>

                <?php /* Profile photo */ ?>
                <div class="app-config-section__divider">
                    <?= lucide_tag('camera', 'app-config-section__divider-icon') ?>
                    <span>Foto de perfil</span>
                </div>

                <div class="app-config-photo">
                    <div class="app-config-photo__avatar">
                        <?php if (!empty($user['foto_perfil'])): ?>
                        <img src="<?= e($user['foto_perfil']) ?>"
                             alt="Foto de perfil"
                             class="app-config-photo__img">
                        <?php else: ?>
                        <?= lucide_tag('camera', 'app-config-photo__placeholder-icon') ?>
                        <?php endif; ?>
                    </div>
                    <div class="app-config-photo__upload">
                        <input type="file"
                               name="foto_perfil"
                               id="foto_perfil"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               class="app-config-photo__input">
                        <p class="app-field__helper mb-0">
                            Formatos aceitos: JPG, PNG, GIF, WebP (máx. 2 MB)
                        </p>
                    </div>
                </div>

                <?php /* Identity & birth date */ ?>
                <div class="app-config-section__divider mt-4">
                    <?= lucide_tag('credit-card', 'app-config-section__divider-icon') ?>
                    <span>Identificação</span>
                </div>

                <div class="app-config-fields-grid">
                    <div class="app-field">
                        <label for="identidade_rg" class="app-field__label">Identidade (RG)</label>
                        <input type="text"
                               id="identidade_rg"
                               name="identidade_rg"
                               class="app-field__input"
                               placeholder="00.000.000-0"
                               value="<?= e($user['identidade_rg'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="data_nascimento" class="app-field__label">Data de nascimento</label>
                        <input type="date"
                               id="data_nascimento"
                               name="data_nascimento"
                               class="app-field__input"
                               value="<?= e($user['data_nascimento'] ?? '') ?>">
                    </div>
                </div>

                <?php /* Full name (civil) */ ?>
                <div class="app-config-section__divider mt-4">
                    <?= lucide_tag('user', 'app-config-section__divider-icon') ?>
                    <span>Nome</span>
                </div>

                <div class="app-config-fields-grid">
                    <div class="app-field">
                        <label for="nome_civil_nome" class="app-field__label">
                            Nome <span class="text-senac-error">*</span>
                        </label>
                        <input type="text"
                               id="nome_civil_nome"
                               name="nome_civil_nome"
                               class="app-field__input"
                               required
                               autocomplete="given-name"
                               value="<?= e($user['nome_civil_nome'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="nome_civil_sobrenome" class="app-field__label">
                            Sobrenome <span class="text-senac-error">*</span>
                        </label>
                        <input type="text"
                               id="nome_civil_sobrenome"
                               name="nome_civil_sobrenome"
                               class="app-field__input"
                               required
                               autocomplete="family-name"
                               value="<?= e($user['nome_civil_sobrenome'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="nome_social_nome" class="app-field__label">Nome social</label>
                        <input type="text"
                               id="nome_social_nome"
                               name="nome_social_nome"
                               class="app-field__input"
                               autocomplete="off"
                               placeholder="Como prefere ser chamado(a)"
                               value="<?= e($user['nome_social_nome'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="nome_social_sobrenome" class="app-field__label">Sobrenome social</label>
                        <input type="text"
                               id="nome_social_sobrenome"
                               name="nome_social_sobrenome"
                               class="app-field__input"
                               autocomplete="off"
                               value="<?= e($user['nome_social_sobrenome'] ?? '') ?>">
                    </div>
                </div>

                <?php /* E-mails */ ?>
                <div class="app-config-section__divider mt-4">
                    <?= lucide_tag('mail', 'app-config-section__divider-icon') ?>
                    <span>E-mails</span>
                </div>

                <div class="app-field">
                    <label for="email_pessoal" class="app-field__label">E-mail secundário (pessoal)</label>
                    <input type="email"
                           id="email_pessoal"
                           name="email_pessoal"
                           class="app-field__input"
                           autocomplete="email"
                           placeholder="seu.email@gmail.com"
                           value="<?= e($user['email_pessoal'] ?? '') ?>">
                    <p class="app-field__helper mb-0">Você pode alterar seu e-mail pessoal a qualquer momento</p>
                </div>

                <?php /* Phone numbers */ ?>
                <div class="app-config-section__divider mt-4">
                    <?= lucide_tag('phone', 'app-config-section__divider-icon') ?>
                    <span>Telefones</span>
                </div>

                <div class="app-config-fields-grid">
                    <div>
                        <div class="app-field">
                            <label for="telefone1" class="app-field__label">Telefone principal</label>
                            <input type="tel"
                                   id="telefone1"
                                   name="telefone1"
                                   class="app-field__input"
                                   placeholder="(00) 00000-0000"
                                   value="<?= e($user['telefone1'] ?? '') ?>">
                        </div>
                        <div class="app-field app-field--checkbox mt-2">
                            <input type="checkbox"
                                   name="telefone1_whatsapp"
                                   value="1"
                                   id="telefone1_whatsapp"
                                   class="app-field__checkbox"
                                <?= !empty($user['telefone1_whatsapp']) ? 'checked' : '' ?>>
                            <label for="telefone1_whatsapp" class="app-field__checkbox-label">
                                Este telefone é WhatsApp
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="app-field">
                            <label for="telefone2" class="app-field__label">Telefone secundário</label>
                            <input type="tel"
                                   id="telefone2"
                                   name="telefone2"
                                   class="app-field__input"
                                   placeholder="(00) 00000-0000"
                                   value="<?= e($user['telefone2'] ?? '') ?>">
                        </div>
                        <div class="app-field app-field--checkbox mt-2">
                            <input type="checkbox"
                                   name="telefone2_whatsapp"
                                   value="1"
                                   id="telefone2_whatsapp"
                                   class="app-field__checkbox"
                                <?= !empty($user['telefone2_whatsapp']) ? 'checked' : '' ?>>
                            <label for="telefone2_whatsapp" class="app-field__checkbox-label">
                                Este telefone é WhatsApp
                            </label>
                        </div>
                    </div>
                </div>

                <?php /* Address */ ?>
                <div class="app-config-section__divider mt-4">
                    <?= lucide_tag('map-pin', 'app-config-section__divider-icon') ?>
                    <span>Endereço</span>
                </div>

                <div class="app-config-fields-grid">
                    <div class="app-field">
                        <label for="cep" class="app-field__label">CEP</label>
                        <input type="text"
                               id="cep"
                               name="cep"
                               class="app-field__input"
                               placeholder="00000-000"
                               maxlength="10"
                               value="<?= e($user['cep'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="cidade" class="app-field__label">Cidade</label>
                        <input type="text"
                               id="cidade"
                               name="cidade"
                               class="app-field__input"
                               placeholder="Recife"
                               value="<?= e($user['cidade'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="estado" class="app-field__label">Estado (UF)</label>
                        <input type="text"
                               id="estado"
                               name="estado"
                               class="app-field__input"
                               placeholder="PE"
                               maxlength="2"
                               value="<?= e($user['estado'] ?? '') ?>">
                    </div>

                    <div class="app-field">
                        <label for="pais" class="app-field__label">País</label>
                        <input type="text"
                               id="pais"
                               name="pais"
                               class="app-field__input"
                               placeholder="Brasil"
                               value="<?= e($user['pais'] ?? 'Brasil') ?>">
                    </div>
                </div>

                <div class="app-field mt-3">
                    <label for="endereco" class="app-field__label">Endereço completo</label>
                    <input type="text"
                           id="endereco"
                           name="endereco"
                           class="app-field__input"
                           placeholder="Rua, número, complemento"
                           value="<?= e($user['endereco'] ?? '') ?>">
                </div>

                <div class="app-field mt-3">
                    <label for="bairro" class="app-field__label">Bairro</label>
                    <input type="text"
                           id="bairro"
                           name="bairro"
                           class="app-field__input"
                           placeholder="Nome do bairro"
                           value="<?= e($user['bairro'] ?? '') ?>">
                </div>
            </section>

            <?php if ($aluno !== null): ?>
            <?php /* ─── Student-only: privacy & notifications ─── */ ?>
            <section class="app-form-card app-config-section">
                <h2 class="app-config-section__title">
                    <?= lucide_tag('eye', 'app-config-section__title-icon') ?>
                    Privacidade e notificações
                </h2>

                <div class="app-field app-field--checkbox">
                    <input type="checkbox"
                           name="portfolio_publico"
                           value="1"
                           id="portfolio_publico"
                           class="app-field__checkbox"
                        <?= !empty($aluno['portfolio_publico']) ? 'checked' : '' ?>>
                    <div>
                        <label for="portfolio_publico" class="app-field__checkbox-label">
                            Portfólio público — autorizar empresas parceiras
                        </label>
                        <p class="app-field__helper mb-0">
                            Seus projetos aprovados poderão ser visualizados por empresas cadastradas
                        </p>
                    </div>
                </div>

                <div class="app-field mt-3">
                    <label for="notificacoes" class="app-field__label">Notificações</label>
                    <select name="notificacoes" id="notificacoes" class="app-field__input">
                        <option value="">Nenhuma</option>
                        <option value="email"
                            <?= ($aluno['notificacoes'] ?? '') === 'email' ? 'selected' : '' ?>>
                            E-mail
                        </option>
                    </select>
                </div>
            </section>
            <?php endif; ?>

            <?php /* ─── Security: change password ─── */ ?>
            <section class="app-form-card app-config-section">
                <h2 class="app-config-section__title">
                    <?= lucide_tag('lock', 'app-config-section__title-icon') ?>
                    Segurança
                </h2>

                <div class="app-config-fields-grid">
                    <div class="app-field">
                        <label for="nova_senha" class="app-field__label">Nova senha</label>
                        <input type="password"
                               id="nova_senha"
                               name="nova_senha"
                               class="app-field__input"
                               autocomplete="new-password"
                               placeholder="Deixe em branco para manter a senha atual">
                        <p class="app-field__helper mb-0">Mínimo de 8 caracteres</p>
                    </div>
                </div>
            </section>

            <?php /* ─── LGPD notice ─── */ ?>
            <div class="app-config-lgpd">
                <?= lucide_tag('shield', 'app-config-lgpd__icon') ?>
                <div>
                    <p class="app-config-lgpd__title mb-1">Proteção de Dados (LGPD)</p>
                    <p class="app-config-lgpd__text mb-0">
                        Seus dados pessoais são protegidos conforme a Lei nº 13.709/2018 e utilizados
                        exclusivamente para fins acadêmicos. Você pode solicitar a exclusão ou exportação
                        dos seus dados a qualquer momento.
                    </p>
                </div>
            </div>

            <div class="app-form-actions">
                <a href="<?= \App\Auth\SessionAuth::isAluno() ? '/dashboard' : '/admin/dashboard' ?>"
                   class="app-action-btn app-action-btn--secondary">
                    Cancelar
                </a>
                <button type="submit" class="app-action-btn app-action-btn--primary">
                    <?= lucide_tag('save', 'app-action-btn__icon') ?>
                    Salvar configurações
                </button>
            </div>
        </form>
    </div>
</div>
