<?php

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
];
