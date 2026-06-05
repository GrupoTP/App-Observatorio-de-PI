<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="login-page d-flex align-items-center justify-content-center p-3">
    <div class="card login-card shadow-lg w-100">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <span class="logo-box fs-5 mb-3">SENAC</span>
                <h1 class="h4 fw-bold text-senac-blue mt-3">Observatório de Projetos Integradores</h1>
                <p class="text-muted small">Faculdade Senac Recife</p>
            </div>

            <form method="post" action="/login" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail institucional</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" required
                           placeholder="seu.email@senac.edu.br" value="<?= old('email') ?>">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Senha</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)" aria-label="Mostrar senha">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-senac-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Entrar
                </button>
            </form>

            <div class="alert alert-light border small mb-0">
                <strong>Demo:</strong> aluno@aluno, professor@professor, admin@admin — senha: <code>senac123</code>
            </div>
        </div>
    </div>
</div>
