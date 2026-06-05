<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 mb-3">Aplicação iniciada com sucesso</h1>
                <p class="text-muted mb-4">
                    Estrutura base PHP + Bootstrap + MySQL pronta para evoluir.
                </p>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="text-muted">Status do banco:</span>
                    <?php if ($dbStatus === 'connected'): ?>
                        <span class="badge text-bg-success">Conectado</span>
                    <?php else: ?>
                        <span class="badge text-bg-danger">Desconectado</span>
                    <?php endif; ?>
                </div>

                <?php if ($dbError !== null): ?>
                    <div class="alert alert-warning" role="alert">
                        <?= e($dbError) ?>
                    </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h2 class="h6 fw-semibold">Stack</h2>
                            <ul class="mb-0 small">
                                <li>PHP 8.3 + Apache</li>
                                <li>Bootstrap 5</li>
                                <li>MySQL 8</li>
                                <li>PDO</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h2 class="h6 fw-semibold">Próximos passos</h2>
                            <ul class="mb-0 small">
                                <li>Adicionar rotas em <code>public/index.php</code></li>
                                <li>Criar models em <code>src/</code></li>
                                <li>Evolutionar o schema em <code>sql/init.sql</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
