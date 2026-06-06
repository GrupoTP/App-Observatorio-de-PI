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
<div class="app-existing-attachments" data-existing-attachments>
    <p class="app-existing-attachments__hint mb-0">
        Alterações nos anexos só serão aplicadas ao clicar em Salvar alterações.
    </p>
    <ul class="app-existing-attachments__list">
        <?php foreach ($attachments as $anexo): ?>
            <?php $anexoId = (string) $anexo['id_anexo']; ?>
            <li class="app-existing-attachment" data-existing-attachment data-anexo-id="<?= e($anexoId) ?>">
                <div class="app-existing-attachment__preview">
                    <?php if (!empty($anexo['miniatura'])): ?>
                        <img src="<?= e(anexo_thumbnail_url($anexoId)) ?>"
                             alt="" class="app-existing-attachment__thumb" loading="lazy">
                    <?php else: ?>
                        <span class="app-existing-attachment__icon" aria-hidden="true">
                            <?= lucide_tag('file-text', 'app-existing-attachment__icon-svg') ?>
                        </span>
                    <?php endif; ?>
                    <a href="<?= e(anexo_download_url($anexoId)) ?>"
                       class="app-existing-attachment__download"
                       target="_blank" rel="noopener">
                        <?= lucide_tag('download', 'app-existing-attachment__download-icon') ?>
                        Baixar arquivo
                    </a>
                </div>
                <div class="app-existing-attachment__fields">
                    <div class="app-field mb-3">
                        <label class="app-field__label" for="anexo-nome-<?= e($anexoId) ?>">Nome do anexo</label>
                        <input type="text"
                               id="anexo-nome-<?= e($anexoId) ?>"
                               name="anexo_existente_nome[<?= e($anexoId) ?>]"
                               class="app-field__input"
                               required
                               autocomplete="off"
                               value="<?= e($anexo['nome'] ?? '') ?>"
                               data-existing-anexo-nome>
                    </div>
                    <div class="app-field mb-3">
                        <label class="app-field__label" for="anexo-desc-<?= e($anexoId) ?>">Descrição</label>
                        <input type="text"
                               id="anexo-desc-<?= e($anexoId) ?>"
                               name="anexo_existente_descricao[<?= e($anexoId) ?>]"
                               class="app-field__input"
                               autocomplete="off"
                               placeholder="Ex.: Documentação técnica, relatório final"
                               value="<?= e($anexo['descricao'] ?? '') ?>"
                               data-existing-anexo-descricao>
                    </div>
                    <p class="app-existing-attachment__meta mb-0">
                        Enviado em <?= e(format_date($anexo['data_envio'] ?? null)) ?>
                    </p>
                    <input type="checkbox"
                           name="anexo_remover[]"
                           value="<?= e($anexoId) ?>"
                           class="visually-hidden"
                           data-anexo-remove-input>
                    <button type="button"
                            class="app-existing-attachment__remove"
                            data-mark-anexo-remove>
                        Remover anexo
                    </button>
                    <button type="button"
                            class="app-existing-attachment__undo d-none"
                            data-unmark-anexo-remove>
                        Desfazer remoção
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
