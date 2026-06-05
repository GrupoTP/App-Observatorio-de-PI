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
