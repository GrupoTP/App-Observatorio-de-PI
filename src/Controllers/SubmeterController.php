<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Services\ProjetoService;
use App\Support\Flash;

final class SubmeterController extends Controller
{
    public function create(Request $request, array $params = []): void
    {
        $this->render('aluno/submeter', [
            'headerTitle' => 'Submeter Projeto',
            'pageTitle' => 'Submeter Novo Projeto',
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';

        try {
            (new ProjetoService())->createForAluno($userId, [
                'titulo' => $request->input('titulo', '') ?? '',
                'descricao' => $request->input('descricao', '') ?? '',
                'link_github' => $request->input('link_github', '') ?? '',
                'tecnologias' => $request->input('tecnologias', '') ?? '',
                'publico' => $request->input('publico'),
                'nome_grupo' => $request->input('nome_grupo'),
            ], $request->file('arquivo'));
            Flash::success('Projeto submetido com sucesso!');
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
            redirect('/submeter');
        }

        redirect('/projetos');
    }
}
