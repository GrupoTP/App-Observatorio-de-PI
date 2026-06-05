<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\UsuarioRepository;
use App\Support\Flash;

final class CurriculoController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId() ?? '';
        $user = (new UsuarioRepository())->findById($userId);
        $data = [];
        if (!empty($user['json_curriculo'])) {
            $decoded = json_decode($user['json_curriculo'], true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $this->render('aluno/curriculo', [
            'headerTitle' => 'Currículo',
            'pageTitle' => 'Gerador de Currículo',
            'curriculo' => $data,
            'user' => $user,
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';

        $data = [
            'resumo' => $request->input('resumo', '') ?? '',
            'experiencia' => $request->input('experiencia', '') ?? '',
            'formacao' => $request->input('formacao', '') ?? '',
            'habilidades' => $request->input('habilidades', '') ?? '',
            'contato' => $request->input('contato', '') ?? '',
        ];

        (new UsuarioRepository())->updateCurriculo($userId, json_encode($data, JSON_UNESCAPED_UNICODE));
        Flash::success('Currículo salvo com sucesso.');

        redirect('/curriculo');
    }
}
