<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\FeedbackRepository;
use App\Repositories\ProjetoRepository;
use App\Repositories\UsuarioRepository;
use App\Services\ProjetoService;

final class DashboardController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId();
        $usuarios = new UsuarioRepository();
        $projetos = new ProjetoRepository();
        $service = new ProjetoService();

        $user = $usuarios->findById($userId ?? '');
        $all = $projetos->forAluno($userId ?? '');
        $feedbackRepo = new FeedbackRepository();
        $recent = [];

        foreach (array_slice($all, 0, 3) as $project) {
            $feedback = $feedbackRepo->findByProject($project['id_projeto']);
            $project['grade'] = $feedbackRepo->averageGradeForProject($project['id_projeto'], $feedback);
            $project['submitted_at'] = $feedback['data'] ?? $project['prazo_especial'] ?? null;
            $recent[] = $project;
        }

        $this->render('aluno/dashboard', [
            'headerTitle' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'user' => $user,
            'userName' => user_display_name($user),
            'sentCount' => count($all),
            'evaluatedCount' => $projetos->countEvaluatedByAluno($userId ?? ''),
            'upcomingCount' => $service->upcomingDeadlinesCount($userId ?? ''),
            'recentProjects' => $recent,
        ]);
    }
}
