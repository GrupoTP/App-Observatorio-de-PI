<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\AnexoRepository;
use App\Repositories\FeedbackRepository;
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
        $feedbackRepo = new FeedbackRepository();
        $projects = [];

        foreach ($repo->forAluno($userId, $status === 'todos' ? null : $status, $search) as $project) {
            $feedback = $feedbackRepo->findByProject($project['id_projeto']);
            $project['grade'] = $feedbackRepo->averageGradeForProject($project['id_projeto'], $feedback);
            $project['submitted_at'] = $feedback['data'] ?? $project['prazo_especial'] ?? null;
            $project['turma_label'] = turma_display_label($project);
            $projects[] = $project;
        }

        $this->render('aluno/projetos', [
            'headerTitle' => 'Meus Projetos',
            'pageTitle' => 'Meus Projetos',
            'projects' => $projects,
            'search' => $search ?? '',
            'status' => $status ?? 'todos',
            'hasFilters' => ($search ?? '') !== '' || ($status ?? 'todos') !== 'todos',
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $userId = SessionAuth::userId() ?? '';
        $id = $params['id'] ?? '';
        $service = new ProjetoService();
        $repo = new ProjetoRepository();

        if (!$service->canAlunoAccess($id, $userId)) {
            Flash::error('Projeto não encontrado.');
            redirect('/projetos');
        }

        $project = $repo->findById($id);
        if ($project === null) {
            Flash::error('Projeto não encontrado.');
            redirect('/projetos');
        }

        $feedbackRepo = new FeedbackRepository();
        $feedback = $feedbackRepo->findByProject($id);
        $grade = $feedbackRepo->averageGradeForProject($id, $feedback);
        $conceito = $grade !== null ? nota_para_conceito($grade) : null;

        $attachments = (new AnexoRepository())->forProject($id);
        $submittedAt = null;
        if ($attachments !== []) {
            $dates = array_column($attachments, 'data_envio');
            sort($dates);
            $submittedAt = $dates[0] ?? null;
        }

        if ($feedback !== null) {
            $feedback['rubrica'] = $feedbackRepo->rubricaForFeedback((int) $feedback['id_feedback']);
        }

        $this->render('aluno/projeto-detalhes', [
            'headerTitle' => 'Detalhes do Projeto',
            'pageTitle' => $project['titulo'] ?? 'Detalhes do Projeto',
            'project' => $project,
            'students' => $repo->teamMembers($id),
            'attachments' => $attachments,
            'feedback' => $feedback,
            'conceito' => $conceito,
            'submittedAt' => $submittedAt,
            'isOwner' => $service->isOwner($id, $userId),
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
        $attachments = (new AnexoRepository())->forProject($id);
        $this->render('aluno/projeto-form', [
            'headerTitle' => 'Editar Projeto',
            'pageTitle' => 'Editar Projeto',
            'project' => $project,
            'attachments' => $attachments,
            'action' => '/projetos/' . $id . '/editar',
        ]);
    }

    public function update(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';
        $id = $params['id'] ?? '';

        try {
            (new ProjetoService())->updateForAluno(
                $id,
                $userId,
                [
                    'titulo' => $request->input('titulo', '') ?? '',
                    'descricao' => $request->input('descricao', '') ?? '',
                    'link_repo_git' => $request->input('link_repo_git', '') ?? '',
                    'tecnologias' => $request->input('tecnologias', '') ?? '',
                    'publico' => $request->input('publico'),
                    'nome_grupo' => $request->input('nome_grupo'),
                ],
                $request->files('anexo_arquivo'),
                $request->inputList('anexo_descricao'),
                $request->inputMap('anexo_existente_nome'),
                $request->inputMap('anexo_existente_descricao'),
                $request->inputList('anexo_remover'),
            );
            Flash::success('Projeto atualizado com sucesso.');
            redirect('/projetos');

            return;
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
        }

        redirect('/projetos/' . $id . '/editar');
    }

    public function destroy(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';
        $id = $params['id'] ?? '';
        $service = new ProjetoService();

        if ($service->isOwner($id, $userId)) {
            (new ProjetoRepository())->softDelete($id);
            Flash::success('Projeto removido da sua lista.');
        } else {
            Flash::error('Sem permissão.');
        }

        redirect('/projetos');
    }
}
