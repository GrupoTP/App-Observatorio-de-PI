<?php

/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Repositories\FeedbackRepository;
use App\Repositories\ProjetoRepository;
use App\Repositories\RubricaRepository;
use App\Repositories\UsuarioRepository;
use App\Support\Flash;

final class ParceiroController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $search = $request->query('q') ?? '';

        $all    = (new ProjetoRepository())->allForAdmin('avaliado', $search !== '' ? $search : null);
        $public = array_filter($all, fn($p) => (bool) $p['publico']);

        $this->render('parceiro/projetos', [
            'pageTitle'   => 'Projetos Integradores',
            'headerTitle' => 'Projetos Integradores',
            'projects'    => array_values($public),
            'search'      => $search,
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $id      = $params['id'] ?? '';
        $projeto = (new ProjetoRepository())->findById($id);

        if ($projeto === null || !(bool) $projeto['publico']) {
            Flash::error('Projeto não encontrado ou não disponível.');
            redirect('/parceiro');
        }

        $projetoRepo  = new ProjetoRepository();
        $feedbackRepo = new FeedbackRepository();
        $submitter    = (new UsuarioRepository())->findById($projeto['id_usuario_submissor']);
        $members      = $projetoRepo->coauthors($id);
        $feedback     = $feedbackRepo->findByProject($id);

        $rubricaScores  = [];
        $rubricaCriteria = [];
        if ($feedback !== null) {
            $rubricaScores = $feedbackRepo->rubricaForFeedback((int) $feedback['id_feedback']);
            foreach ((new RubricaRepository())->allActive($projeto['cod_turma']) as $c) {
                $rubricaCriteria[$c['id_criterio']] = $c['nome'];
            }
        }

        $this->render('parceiro/projeto-detalhes', [
            'pageTitle'      => $projeto['titulo'],
            'headerTitle'    => $projeto['titulo'],
            'project'        => $projeto,
            'submitter'      => $submitter,
            'members'        => $members,
            'feedback'       => $feedback,
            'rubricaScores'  => $rubricaScores,
            'rubricaCriteria' => $rubricaCriteria,
        ]);
    }
}
