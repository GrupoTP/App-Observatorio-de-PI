<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="auth-page d-flex flex-column min-vh-100">
    <div class="auth-page__main flex-grow-1 d-flex align-items-center justify-content-center p-4">
        <div class="auth-card-shell w-100">
            <div class="auth-card">
                <?php require dirname(__DIR__) . '/partials/auth-card-header.php'; ?>

                <?php require dirname(__DIR__) . '/partials/auth-flash.php'; ?>

                <form method="post" action="/login" class="auth-form" novalidate>
                    <?= csrf_field() ?>

                    <div class="auth-field">
                        <label for="email" class="auth-label">
                            E-mail <span class="text-senac-error">*</span>
                        </label>
                        <input type="email" class="auth-input" id="email" name="email" required
                               placeholder="seu.email@senac.edu.br" value="<?= old('email') ?>" autocomplete="email">
                    </div>

                    <div class="auth-field">
                        <label for="password" class="auth-label auth-label--sm">
                            Senha <span class="text-senac-error">*</span>
                        </label>
                        <div class="auth-password-wrap">
                            <input type="password" class="auth-input auth-input--password" id="password" name="password"
                                   required placeholder="Digite sua senha" autocomplete="current-password">
                            <button type="button" class="auth-password-toggle" onclick="togglePassword('password', this)"
                                    aria-label="Mostrar senha">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn-primary">
                        Entrar
                    </button>

                    <div class="text-center">
                        <a href="#" class="auth-forgot-link">Esqueci minha senha</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require dirname(__DIR__) . '/partials/auth-footer.php'; ?>
</div>
