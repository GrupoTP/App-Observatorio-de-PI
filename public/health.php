<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

require_once dirname(__DIR__) . '/bootstrap.php';

header('Content-Type: application/json');

try {
    \App\Database::connection()->query('SELECT 1');
    $db = 'ok';
} catch (Throwable $e) {
    $db = $e->getMessage();
}

echo json_encode([
    'db_host' => config('database.host'),
    'db' => $db,
    'getenv_DB_HOST' => getenv('DB_HOST'),
], JSON_PRETTY_PRINT);
