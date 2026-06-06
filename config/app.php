<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Observatório de Projetos Integradores',
        'env' => getenv('APP_ENV') ?: 'local',
    ],
    'database' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'opi',
        'user' => getenv('DB_USER') ?: 'opi_user',
        'password' => getenv('DB_PASSWORD') ?: 'opi_secret',
        'charset' => 'utf8mb4',
    ],
    'upload' => [
        'max_bytes' => (int) (getenv('UPLOAD_MAX_BYTES') ?: 1073741824),
        'storage_path' => getenv('UPLOAD_STORAGE_PATH') ?: dirname(__DIR__) . '/storage/attachments',
    ],
];
