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

        // Group by month label (e.g. "Junho de 2026")
        $byMonth = [];
        $monthNames = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];
        foreach ($turmas as $t) {
            $ts = strtotime($t['prazo_projetos'] ?? '') ?: 0;
            $monthLabel = $ts > 0
                ? $monthNames[(int) date('n', $ts)] . ' de ' . date('Y', $ts)
                : 'Sem prazo definido';
            $byMonth[$monthLabel][] = $t;
        }

        $this->render('aluno/prazos', [
            'headerTitle' => 'Prazos',
            'pageTitle' => 'Prazos e Entregas',
            'turmas' => $turmas,
            'byMonth' => $byMonth,
        ]);
    }
}
