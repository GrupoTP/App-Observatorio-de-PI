<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth\SessionAuth;
use App\Controllers\Controller;
use App\Http\Request;
use App\Repositories\FeedbackRepository;
use App\Repositories\ProjetoRepository;
use App\Repositories\RubricaRepository;
use App\Support\Flash;

final class ProjetosAdminController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $search = $request->query('q');
        $status = $request->query('status', 'todos');

        $this->render('admin/projetos', [
            'headerTitle' => 'Gerenciar Projetos',
            'pageTitle' => 'Gerenciar Projetos',
            'projects' => (new ProjetoRepository())->allForAdmin(
                $status === 'todos' ? null : $status,
                $search
            ),
            'search' => $search ?? '',
            'status' => $status ?? 'todos',
        ]);
    }

    public function evaluate(Request $request, array $params): void
    {
        $id = $params['id'] ?? '';
        $project = (new ProjetoRepository())->findById($id);
        if ($project === null) {
            Flash::error('Projeto não encontrado.');
            redirect('/admin/projetos');
        }

        $turma = $project['cod_turma'];
        $criteria = (new RubricaRepository())->allActive($turma);
        $existing = (new FeedbackRepository())->findByProject($id);

        $this->render('admin/avaliar-projeto', [
            'headerTitle' => 'Avaliar Projeto',
            'pageTitle' => 'Avaliar Projeto',
            'project' => $project,
            'criteria' => $criteria,
            'existing' => $existing,
        ]);
    }

    public function evaluateStore(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $id = $params['id'] ?? '';
        $professorId = SessionAuth::userId() ?? '';

        $scores = [];
        foreach ($_POST as $key => $value) {
            if (!str_starts_with($key, 'criterio_')) {
                continue;
            }

            $criterio = substr($key, 9);
            $scores[$criterio] = (string) conceito_codigo_para_nota_interna((string) $value);
        }

        try {
            (new FeedbackRepository())->create(
                $id,
                $professorId,
                $request->input('descricao', '') ?? '',
                $scores
            );
            Flash::success('Avaliação registrada.');
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
        }

        redirect('/admin/projetos');
    }
}
