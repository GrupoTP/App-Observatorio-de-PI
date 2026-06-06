<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\FeedbackRepository;

final class FeedbacksController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId() ?? '';
        $repo = new FeedbackRepository();
        $feedbacks = $repo->forAlunoProjects($userId);

        foreach ($feedbacks as &$fb) {
            $fb['rubrica'] = $repo->rubricaForFeedback((int) $fb['id_feedback']);
            $scores = array_map(static fn ($r) => (float) $r['conceito'], $fb['rubrica']);
            $fb['conceito_final'] = $scores !== []
                ? nota_para_conceito(round(array_sum($scores) / count($scores), 2))
                : null;
        }
        unset($fb);

        $this->render('aluno/feedbacks', [
            'headerTitle' => 'Feedbacks',
            'pageTitle' => 'Feedbacks dos Professores',
            'feedbacks' => $feedbacks,
        ]);
    }
}
