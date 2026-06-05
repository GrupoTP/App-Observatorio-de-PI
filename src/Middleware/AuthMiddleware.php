<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Auth\SessionAuth;

final class AuthMiddleware
{
    public static function handle(): void
    {
        if (!SessionAuth::isAuthenticated()) {
            redirect('/login');
        }
    }
}
