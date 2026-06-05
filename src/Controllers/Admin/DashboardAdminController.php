<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Http\Request;
use App\Repositories\ProjetoRepository;
use App\Repositories\UsuarioRepository;

final class DashboardAdminController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $projetos = new ProjetoRepository();
        $usuarios = new UsuarioRepository();
        $all = $projetos->allForAdmin();

        $this->render('admin/dashboard', [
            'headerTitle' => \App\Auth\SessionAuth::isAdmin() ? 'Painel Admin' : 'Painel Professor',
            'pageTitle' => 'Dashboard Administrativo',
            'totalProjects' => count($all),
            'pending' => count(array_filter($all, static fn ($p) => $p['situacao_projeto'] === 'enviado')),
            'evaluated' => count(array_filter($all, static fn ($p) => $p['situacao_projeto'] === 'avaliado')),
            'totalStudents' => count($usuarios->listAlunos()),
            'recent' => array_slice($all, 0, 5),
        ]);
    }
}
