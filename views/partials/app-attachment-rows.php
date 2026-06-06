<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var bool $attachmentRequired */
/** @var bool $allowMultiple */
$attachmentRequired = $attachmentRequired ?? true;
$allowMultiple = $allowMultiple ?? true;
$rowId = 'anexo-row-' . bin2hex(random_bytes(4));
?>
<div class="app-attachments" data-attachments-manager>
    <div class="app-attachment-list" data-attachment-list>
        <div class="app-attachment-row" data-attachment-row id="<?= e($rowId) ?>">
            <div class="app-file-upload" data-file-upload>
                <input type="file" name="anexo_arquivo[]" class="app-file-upload__input"
                       accept=".pdf,.docx,.zip,.png,.jpg,.jpeg" autocomplete="off"
                       data-file-input<?= $attachmentRequired ? ' required' : '' ?>>
                <label class="app-file-upload__label">
                    <?= lucide_tag('upload', 'app-file-upload__icon') ?>
                    <span class="app-file-upload__placeholder" data-file-placeholder>
                        <span class="app-file-upload__title">Arraste ou clique para anexar</span>
                        <span class="app-file-upload__hint">PDF, DOCX, ZIP, PNG ou JPG (máx. 1 GB por arquivo)</span>
                    </span>
                    <span class="app-file-upload__selected d-none" data-file-selected>
                        <span class="app-file-upload__title" data-file-name></span>
                        <span class="app-file-upload__hint" data-file-size></span>
                    </span>
                </label>
            </div>
            <div class="app-field mt-3 mb-0">
                <label class="app-field__label" for="<?= e($rowId) ?>-desc">Descrição do anexo</label>
                <input type="text" id="<?= e($rowId) ?>-desc" name="anexo_descricao[]" class="app-field__input" autocomplete="off"
                       placeholder="Ex.: Documentação técnica, código-fonte, relatório final">
            </div>
            <?php if ($allowMultiple): ?>
                <button type="button" class="app-attachment-row__remove d-none" data-remove-attachment>
                    Remover anexo
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($allowMultiple): ?>
        <button type="button" class="app-attachment-add" data-add-attachment>
            <?= lucide_tag('circle-plus', 'app-attachment-add__icon') ?>
            Adicionar outro anexo
        </button>
    <?php endif; ?>
</div>
