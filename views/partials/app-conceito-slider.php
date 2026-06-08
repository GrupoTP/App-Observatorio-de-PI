<?php
/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

/**
 * Concept slider partial.
 *
 * Required variables injected by the caller:
 * @var string      $fieldName   HTML name attribute for the hidden input (e.g. "criterio_Funcionalidades")
 * @var string|null $selectedCode  Pre-selected concept code (AE|O|B|ANS|I), or null/'' for none
 *
 * Concepts ordered from best to worst so the slider feels intuitive (left = best).
 * The hidden input receives the concept code that the backend expects.
 */

$selectedCode = strtoupper(trim($selectedCode ?? ''));

// Ordered lowest → highest (index 0 = I, index 4 = AE) so slider max = best grade
$concepts = [
    ['code' => 'I',   'label' => 'Insuficiente',             'modifier' => 'i'],
    ['code' => 'ANS', 'label' => 'Ainda Não Suficiente',     'modifier' => 'ans'],
    ['code' => 'B',   'label' => 'Bom',                     'modifier' => 'b'],
    ['code' => 'O',   'label' => 'Ótimo',                   'modifier' => 'o'],
    ['code' => 'AE',  'label' => 'Atendido com Excelência', 'modifier' => 'ae'],
];

$selectedIndex = 2; // Default: B (middle)
foreach ($concepts as $i => $c) {
    if ($c['code'] === $selectedCode) {
        $selectedIndex = $i;
        break;
    }
}

$selectedConcept = $concepts[$selectedIndex];
$sliderId = 'slider_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $fieldName) . '_' . uniqid();
?>
<div class="conceito-slider" data-slider-id="<?= e($sliderId) ?>">
    <input type="range"
           id="<?= e($sliderId) ?>"
           class="conceito-slider__range"
           min="0"
           max="4"
           step="1"
           value="<?= (int) $selectedIndex ?>"
           data-field="<?= e($fieldName) ?>"
           aria-label="Conceito para <?= e($fieldName) ?>">

    <div class="conceito-slider__labels" aria-hidden="true">
        <?php foreach ($concepts as $c): ?>
        <span class="conceito-slider__label"><?= e($c['code']) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="conceito-slider__display conceito-slider__display--<?= e($selectedConcept['modifier']) ?>"
         id="<?= e($sliderId) ?>_display">
        <span class="conceito-slider__display-code"><?= e($selectedConcept['code']) ?></span>
        <span class="conceito-slider__display-label"><?= e($selectedConcept['label']) ?></span>
    </div>

    <input type="hidden"
           name="<?= e($fieldName) ?>"
           id="<?= e($sliderId) ?>_value"
           value="<?= e($selectedConcept['code']) ?>"
           required>
</div>
