<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\ProjetoRepository;
use App\Services\ProjetoService;
use App\Support\Flash;

final class ProjetosController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId() ?? '';
        $search = $request->query('q');
        $status = $request->query('status', 'todos');

        $repo = new ProjetoRepository();
        $this->render('aluno/projetos', [
            'headerTitle' => 'Meus Projetos',
            'pageTitle' => 'Meus Projetos',
            'projects' => $repo->forAluno($userId, $status === 'todos' ? null : $status, $search),
            'search' => $search ?? '',
            'status' => $status ?? 'todos',
        ]);
    }

    public function edit(Request $request, array $params): void
    {
        $userId = SessionAuth::userId() ?? '';
        $id = $params['id'] ?? '';
        $service = new ProjetoService();
        $repo = new ProjetoRepository();

        if (!$service->isOwner($id, $userId)) {
            Flash::error('Projeto não encontrado.');
            redirect('/projetos');
        }

        $project = $repo->findById($id);
        $this->render('aluno/projeto-form', [
            'headerTitle' => 'Editar Projeto',
            'pageTitle' => 'Editar Projeto',
            'project' => $project,
            'action' => '/projetos/' . $id . '/editar',
        ]);
    }

    public function update(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';
        $id = $params['id'] ?? '';

        try {
            (new ProjetoService())->updateForAluno($id, $userId, [
                'titulo' => $request->input('titulo', '') ?? '',
                'descricao' => $request->input('descricao', '') ?? '',
                'link_github' => $request->input('link_github', '') ?? '',
                'tecnologias' => $request->input('tecnologias', '') ?? '',
                'publico' => $request->input('publico'),
                'nome_grupo' => $request->input('nome_grupo'),
            ], $request->file('arquivo'));
            Flash::success('Projeto atualizado com sucesso.');
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
        }

        redirect('/projetos');
    }

    public function destroy(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';
        $id = $params['id'] ?? '';
        $service = new ProjetoService();

        if ($service->isOwner($id, $userId)) {
            (new ProjetoRepository())->softDelete($id);
            Flash::success('Projeto excluído.');
        } else {
            Flash::error('Sem permissão.');
        }

        redirect('/projetos');
    }
}
