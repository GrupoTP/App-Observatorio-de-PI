<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\TurmaRepository;

final class PrazosController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId() ?? '';
        $turmas = (new TurmaRepository())->prazosForAluno($userId);

        $this->render('aluno/prazos', [
            'headerTitle' => 'Prazos',
            'pageTitle' => 'Prazos e Entregas',
            'turmas' => $turmas,
        ]);
    }
}
