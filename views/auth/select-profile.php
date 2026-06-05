<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<?php
$profileDescriptions = [
    'Administrador' => 'Acesso total ao sistema',
    'Coordenador' => 'Validação e gestão acadêmica',
    'Professor' => 'Avaliação de projetos',
    'Aluno' => 'Submissão e acompanhamento',
];
?>
<div class="auth-page d-flex flex-column min-vh-100">
    <div class="auth-page__main flex-grow-1 d-flex align-items-center justify-content-center p-4">
        <div class="auth-card-shell w-100">
            <div class="auth-card">
                <?php require dirname(__DIR__) . '/partials/auth-card-header.php'; ?>

                <?php require dirname(__DIR__) . '/partials/auth-flash.php'; ?>

                <form method="post" action="/login" class="auth-form">
                    <?= csrf_field() ?>
                    <?php if (!empty($email)): ?>
                        <input type="hidden" name="email" value="<?= e($email) ?>">
                        <input type="hidden" name="password" value="<?= e($password) ?>">
                    <?php endif; ?>

                    <div class="auth-field">
                        <label class="auth-label auth-label--sm">
                            Selecione seu perfil <span class="text-senac-error">*</span>
                        </label>
                        <div class="d-grid gap-3">
                            <?php foreach ($profiles as $profile): ?>
                                <button type="submit" name="profile" value="<?= e($profile) ?>"
                                        class="auth-profile-option text-start">
                                    <div class="fw-semibold"><?= e($profile) ?></div>
                                    <?php if (isset($profileDescriptions[$profile])): ?>
                                        <div class="auth-profile-option__hint">
                                            <?= e($profileDescriptions[$profile]) ?>
                                        </div>
                                    <?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="/login" class="auth-forgot-link">Voltar ao login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require dirname(__DIR__) . '/partials/auth-footer.php'; ?>
</div>
