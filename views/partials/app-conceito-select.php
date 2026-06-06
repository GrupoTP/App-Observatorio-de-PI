<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/** @var string $fieldName */
/** @var string|null $fieldId */
$fieldId = $fieldId ?? $fieldName;
$inputClass = $inputClass ?? 'form-select';
?>
<select id="<?= e($fieldId) ?>"
        name="<?= e($fieldName) ?>"
        class="<?= e($inputClass) ?>"
        required>
    <option value="" disabled selected>Selecione o conceito</option>
    <?php foreach (conceitos_senac() as $conceito): ?>
        <option value="<?= e($conceito['code']) ?>">
            <?= e($conceito['code']) ?> — <?= e($conceito['label']) ?>
        </option>
    <?php endforeach; ?>
</select>
