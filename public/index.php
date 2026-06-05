<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Http\Request;
use App\Router;

$router = new Router();
require dirname(__DIR__) . '/config/routes.php';

$request = new Request();
$router->dispatch($request);
