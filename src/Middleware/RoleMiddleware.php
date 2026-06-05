<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Auth\SessionAuth;

final class RoleMiddleware
{
    /** @param list<string> $allowed */
    public static function handle(array $allowed, bool $strict = false): void
    {
        AuthMiddleware::handle();

        $role = SessionAuth::activeRole();

        if ($role === null || !in_array($role, $allowed, true)) {
            if ($strict) {
                redirect(SessionAuth::isAluno() ? '/dashboard' : '/admin/dashboard');
            }
            redirect('/dashboard');
        }
    }
}
