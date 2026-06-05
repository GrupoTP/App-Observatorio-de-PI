<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="login-page d-flex align-items-center justify-content-center p-3">
    <div class="card login-card shadow-lg w-100">
        <div class="card-body p-4 p-md-5">
            <h1 class="h4 fw-bold text-senac-blue mb-2">Selecione seu perfil</h1>
            <p class="text-muted mb-4">Este usuário possui mais de um perfil de acesso.</p>

            <form method="post" action="/login">
                <?= csrf_field() ?>
                <?php if (!empty($email)): ?>
                    <input type="hidden" name="email" value="<?= e($email) ?>">
                    <input type="hidden" name="password" value="<?= e($password) ?>">
                <?php endif; ?>

                <div class="d-grid gap-2 mb-4">
                    <?php foreach ($profiles as $profile): ?>
                        <button type="submit" name="profile" value="<?= e($profile) ?>"
                                class="btn btn-senac-secondary btn-lg text-start">
                            <i class="bi bi-person-badge me-2"></i><?= e($profile) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </form>

            <a href="/login" class="btn btn-link">Voltar ao login</a>
        </div>
    </div>
</div>
