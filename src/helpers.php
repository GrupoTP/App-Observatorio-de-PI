<?php

declare(strict_types=1);

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);

    $templatePath = dirname(__DIR__) . '/views/' . $template . '.php';

    if (!is_file($templatePath)) {
        throw new RuntimeException(sprintf('View "%s" not found.', $template));
    }

    ob_start();
    require $templatePath;
    $content = ob_get_clean();

    require dirname(__DIR__) . '/views/layouts/main.php';
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
