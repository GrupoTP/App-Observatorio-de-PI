<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

use App\Support\Flash;

function view(string $template, array $data = [], string $layout = 'app'): void
{
    extract($data, EXTR_SKIP);

    $templatePath = dirname(__DIR__) . '/views/' . $template . '.php';

    if (!is_file($templatePath)) {
        throw new RuntimeException(sprintf('View "%s" not found.', $template));
    }

    $flash = Flash::get();

    ob_start();
    require $templatePath;
    $content = ob_get_clean() ?: '';
    $layoutPath = dirname(__DIR__) . '/views/layouts/' . $layout . '.php';

    if (!is_file($layoutPath)) {
        throw new RuntimeException(sprintf('Layout "%s" not found.', $layout));
    }

    require $layoutPath;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function route_param(string $key, ?string $default = null): ?string
{
    $params = $_REQUEST['_route_params'] ?? [];

    return $params[$key] ?? $default;
}

function format_date(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    $ts = strtotime($datetime);

    return $ts !== false ? date('d/m/Y', $ts) : $datetime;
}

function format_datetime(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    $ts = strtotime($datetime);

    return $ts !== false ? date('d/m/Y H:i', $ts) : $datetime;
}

function situacao_label(string $situacao): string
{
    return match ($situacao) {
        'enviado' => 'Enviado',
        'em-correcao' => 'Em correção',
        'avaliado' => 'Avaliado',
        'em-andamento' => 'Em andamento',
        default => ucfirst(str_replace('-', ' ', $situacao)),
    };
}

function situacao_badge_class(string $situacao): string
{
    return match ($situacao) {
        'avaliado' => 'bg-success-subtle text-success-emphasis',
        'enviado' => 'bg-warning-subtle text-dark',
        'em-correcao', 'em-andamento' => 'bg-info-subtle text-info-emphasis',
        default => 'bg-secondary-subtle text-secondary-emphasis',
    };
}

/** @return array{label: string, description: string, modifier: string, icon: string} */
function projeto_status_meta(string $situacao): array
{
    return match ($situacao) {
        'enviado' => [
            'label' => 'Aguardando Avaliação',
            'description' => 'Seu projeto foi enviado e está na fila de avaliação',
            'modifier' => 'enviado',
            'icon' => 'clock',
        ],
        'avaliado' => [
            'label' => 'Avaliado - Conceito Disponível',
            'description' => 'Projeto avaliado com sucesso',
            'modifier' => 'avaliado',
            'icon' => 'circle-check-big',
        ],
        'em-correcao' => [
            'label' => 'Em Correção pelo Professor',
            'description' => 'Professor está avaliando seu projeto neste momento',
            'modifier' => 'em-correcao',
            'icon' => 'circle-alert',
        ],
        default => [
            'label' => situacao_label($situacao),
            'description' => '',
            'modifier' => 'default',
            'icon' => 'folder-open',
        ],
    };
}

/** @return list<array{code: string, label: string, modifier: string}> */
function conceitos_senac(): array
{
    return [
        ['code' => 'AE', 'label' => 'Atendido com Excelência', 'modifier' => 'ae'],
        ['code' => 'O', 'label' => 'Ótimo', 'modifier' => 'o'],
        ['code' => 'B', 'label' => 'Bom', 'modifier' => 'b'],
        ['code' => 'ANS', 'label' => 'Ainda Não Suficiente', 'modifier' => 'ans'],
        ['code' => 'I', 'label' => 'Insuficiente', 'modifier' => 'i'],
    ];
}

function conceito_codigo_valido(string $code): bool
{
    return in_array(strtoupper(trim($code)), ['AE', 'O', 'B', 'ANS', 'I'], true);
}

/**
 * Maps Senac concept code to the minimum internal numeric value for storage.
 * Numeric values are never shown in the UI.
 */
function conceito_codigo_para_nota_interna(string $code): float
{
    return match (strtoupper(trim($code))) {
        'AE' => 9.0,
        'O' => 8.0,
        'B' => 7.0,
        'ANS' => 4.0,
        'I' => 0.0,
        default => throw new InvalidArgumentException('Invalid conceito code.'),
    };
}

/** @return array{code: string, label: string, modifier: string} */
function conceito_por_codigo(string $code): array
{
    foreach (conceitos_senac() as $conceito) {
        if ($conceito['code'] === strtoupper(trim($code))) {
            return $conceito;
        }
    }

    throw new InvalidArgumentException('Unknown conceito code.');
}

/**
 * Converts an internal numeric score to the Senac concept shown in the UI.
 * AE: 9,0–10 | O: 8,0–8,9 | B: 7,0–7,9 | ANS: 4,0–6,9 | I: 0,0–3,9
 *
 * @return array{code: string, label: string, modifier: string}
 */
function nota_para_conceito(float $nota): array
{
    return match (true) {
        $nota >= 9.0 => conceito_por_codigo('AE'),
        $nota >= 8.0 => conceito_por_codigo('O'),
        $nota >= 7.0 => conceito_por_codigo('B'),
        $nota >= 4.0 => conceito_por_codigo('ANS'),
        default => conceito_por_codigo('I'),
    };
}

/** @return array{code: string, label: string, modifier: string} */
function nota_mencao_info(float $nota): array
{
    return nota_para_conceito($nota);
}

function admin_situacao_label(string $situacao): string
{
    return match ($situacao) {
        'avaliado' => 'Avaliado',
        'enviado' => 'Pendente',
        'em-correcao' => 'Em Correção',
        default => situacao_label($situacao),
    };
}

function admin_situacao_badge_class(string $situacao): string
{
    return match ($situacao) {
        'avaliado' => 'admin-status-badge--success',
        'enviado' => 'admin-status-badge--warning',
        'em-correcao' => 'admin-status-badge--info',
        default => 'admin-status-badge--muted',
    };
}

function user_display_name(?array $user): string
{
    if ($user === null) {
        return '';
    }

    $social = trim(($user['nome_social_nome'] ?? '') . ' ' . ($user['nome_social_sobrenome'] ?? ''));
    if ($social !== '') {
        return $social;
    }

    return trim(($user['nome_civil_nome'] ?? '') . ' ' . ($user['nome_civil_sobrenome'] ?? ''));
}

function turma_display_label(?array $turma): string
{
    if ($turma === null) {
        return '—';
    }

    $modulo = trim((string) ($turma['modulo'] ?? ''));
    $curso = mb_strtolower((string) ($turma['nome_curso'] ?? ''));
    $sigla = match (true) {
        str_contains($curso, 'desenvolvimento de sistemas') => 'ADS',
        str_contains($curso, 'jogos') => 'Game Dev',
        default => (string) ($turma['nome_curso'] ?? ''),
    };

    return $modulo !== '' ? $sigla . ' - ' . $modulo : $sigla;
}

function lucide_tag(string $name, string $class = ''): string
{
    $classAttr = trim($class);

    return sprintf(
        '<i data-lucide="%s"%s aria-hidden="true"></i>',
        e($name),
        $classAttr !== '' ? ' class="' . e($classAttr) . '"' : ''
    );
}

function asset_version(string $relativePath): string
{
    $path = dirname(__DIR__) . '/public/' . ltrim($relativePath, '/');

    return is_file($path) ? (string) filemtime($path) : (string) time();
}

function csrf_field(): string
{
    return \App\Support\Csrf::field();
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function flash_old_input(array $input): void
{
    $_SESSION['_old'] = $input;
}

function clear_old_input(): void
{
    unset($_SESSION['_old']);
}

function anexo_download_url(string $anexoId): string
{
    return '/anexos/' . rawurlencode($anexoId) . '/download';
}

function anexo_thumbnail_url(string $anexoId): string
{
    return '/anexos/' . rawurlencode($anexoId) . '/miniatura';
}
