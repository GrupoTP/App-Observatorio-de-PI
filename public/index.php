<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Database;
use App\Router;

$router = new Router();

$router->get('/', function () {
    $dbStatus = 'disconnected';
    $dbError = null;

    try {
        Database::connection()->query('SELECT 1');
        $dbStatus = 'connected';
    } catch (Throwable $exception) {
        $dbError = $exception->getMessage();
    }

    view('home', [
        'appName' => config('app.name'),
        'dbStatus' => $dbStatus,
        'dbError' => $dbError,
    ]);
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
