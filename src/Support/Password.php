<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Support;

final class Password
{
    /** @return array{hash: string, salt: string} */
    public static function hash(string $plain): array
    {
        $salt = bin2hex(random_bytes(16));
        $hash = hash('sha256', $plain . $salt);

        return ['hash' => $hash, 'salt' => $salt];
    }

    public static function verify(string $plain, string $hash, string $salt): bool
    {
        return hash_equals($hash, hash('sha256', $plain . $salt));
    }
}
