<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var list<array<string, mixed>> $attachments */
$attachments = $attachments ?? [];
if ($attachments === []) {
    return;
}
?>
<ul class="app-project-detail__attachments">
    <?php foreach ($attachments as $anexo): ?>
        <?php $anexoId = (string) $anexo['id_anexo']; ?>
        <li class="app-project-detail__attachment">
            <?php if (!empty($anexo['miniatura'])): ?>
                <img src="<?= e(anexo_thumbnail_url($anexoId)) ?>"
                     alt=""
                     class="app-project-detail__attachment-thumb"
                     loading="lazy">
            <?php else: ?>
                <span class="app-project-detail__attachment-icon" aria-hidden="true">
                    <?= lucide_tag('file-text', 'app-project-detail__attachment-icon-svg') ?>
                </span>
            <?php endif; ?>
            <div class="app-project-detail__attachment-body">
                <a href="<?= e(anexo_download_url($anexoId)) ?>"
                   class="app-project-detail__attachment-name">
                    <?= e($anexo['nome'] ?? 'Anexo') ?>
                </a>
                <?php if (!empty($anexo['descricao'])): ?>
                    <p class="app-project-detail__attachment-desc mb-0"><?= e($anexo['descricao']) ?></p>
                <?php endif; ?>
                <p class="app-project-detail__attachment-meta mb-0">
                    Enviado em <?= e(format_date($anexo['data_envio'] ?? null)) ?>
                </p>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
