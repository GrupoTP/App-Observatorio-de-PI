<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var list<array<string, mixed>> $students */
$students = $students ?? [];
?>
<ul class="app-project-detail__students">
    <?php foreach ($students as $student): ?>
        <li class="app-project-detail__student">
            <span class="app-project-detail__student-avatar" aria-hidden="true">
                <?= lucide_tag('user', 'app-project-detail__student-avatar-icon') ?>
            </span>
            <div class="app-project-detail__student-body">
                <p class="app-project-detail__student-name mb-0"><?= e(user_display_name($student)) ?></p>
                <p class="app-project-detail__student-email mb-0"><?= e($student['email_institucional'] ?? '') ?></p>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
