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
        $files = $this->files($key);

        return $files[0] ?? null;
    }

    /**
     * @return list<array{name: string, type: string, tmp_name: string, error: int, size: int}>
     */
    public function files(string $key): array
    {
        if (!isset($_FILES[$key]) || !is_array($_FILES[$key])) {
            return [];
        }

        $upload = $_FILES[$key];

        if (!is_array($upload['name'] ?? null)) {
            if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                return [];
            }

            return [$upload];
        }

        $normalized = [];
        foreach ($upload['name'] as $index => $name) {
            if (($upload['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $normalized[] = [
                'name' => (string) $name,
                'type' => (string) ($upload['type'][$index] ?? ''),
                'tmp_name' => (string) ($upload['tmp_name'][$index] ?? ''),
                'error' => (int) ($upload['error'][$index] ?? UPLOAD_ERR_NO_FILE),
                'size' => (int) ($upload['size'][$index] ?? 0),
            ];
        }

        return $normalized;
    }

    /** @return list<string> */
    public function inputList(string $key): array
    {
        if (!isset($_POST[$key]) || !is_array($_POST[$key])) {
            return [];
        }

        return array_map(static fn ($value): string => trim((string) $value), $_POST[$key]);
    }

    /** @return array<string, string> */
    public function inputMap(string $key): array
    {
        if (!isset($_POST[$key]) || !is_array($_POST[$key])) {
            return [];
        }

        $values = [];
        foreach ($_POST[$key] as $mapKey => $value) {
            $values[(string) $mapKey] = trim((string) $value);
        }

        return $values;
    }

    public function validateCsrf(): bool
    {
        return \App\Support\Csrf::validate($this->input('_csrf'));
    }
}
