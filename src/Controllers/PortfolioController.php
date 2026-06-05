<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\ProjetoRepository;
use App\Repositories\UsuarioRepository;

final class PortfolioController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId() ?? '';
        $repo = new ProjetoRepository();
        $user = (new UsuarioRepository())->findById($userId);

        $this->render('aluno/portfolio', [
            'headerTitle' => 'Meu Portfólio',
            'pageTitle' => 'Meu Portfólio Profissional',
            'projects' => $repo->portfolioPublic($userId),
            'userName' => user_display_name($user),
        ]);
    }
}
