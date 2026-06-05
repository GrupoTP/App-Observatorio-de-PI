<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->
<div class="page-container">
    <?php
    $title = 'Gerenciar Alunos';
    $subtitle = 'Lista de alunos cadastrados';
    $backUrl = '/admin/dashboard';
    require dirname(__DIR__) . '/partials/page-header.php';
    ?>

    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="search" name="q" class="form-control" value="<?= e($search) ?>" placeholder="Buscar aluno">
            <button class="btn btn-senac-secondary">Buscar</button>
        </div>
    </form>

    <div class="table-responsive card">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Nome</th><th>E-mail</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($alunos as $a): ?>
                    <tr>
                        <td><?= e(user_display_name($a)) ?></td>
                        <td><?= e($a['email_institucional']) ?></td>
                        <td class="text-end">
                            <?php if (\App\Auth\SessionAuth::isAdmin()): ?>
                                <a href="/admin/alunos/<?= e($a['id_usuario']) ?>" class="btn btn-sm btn-senac-outline">Detalhes</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
