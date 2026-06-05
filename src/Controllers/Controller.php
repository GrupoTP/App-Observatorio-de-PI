<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Support\Csrf;
use App\Support\Flash;

abstract class Controller
{
    protected function requireCsrf(Request $request): void
    {
        if (!$request->validateCsrf()) {
            Flash::error('Sessão expirada. Tente novamente.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    /** @param array<string, mixed> $data */
    protected function render(string $template, array $data = [], string $layout = 'app'): void
    {
        view($template, $data, $layout);
    }
}
