<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Http;

final class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return rtrim($path, '/') ?: '/';
    }

    public function query(string $key, ?string $default = null): ?string
    {
        return isset($_GET[$key]) ? (string) $_GET[$key] : $default;
    }

    public function input(string $key, ?string $default = null): ?string
    {
        return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
    }

    /** @return array<string, mixed> */
    public function allPost(): array
    {
        return $_POST;
    }

    public function file(string $key): ?array
    {
        if (!isset($_FILES[$key]) || !is_array($_FILES[$key])) {
            return null;
        }

        if (($_FILES[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $_FILES[$key];
    }

    public function validateCsrf(): bool
    {
        return \App\Support\Csrf::validate($this->input('_csrf'));
    }
}
