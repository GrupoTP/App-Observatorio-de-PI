<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\UsuarioRepository;
use App\Support\Csrf;
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
            'pageTitle' => 'Currículo',
            'curriculo' => $data,
            'user' => $user,
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');

        if (str_contains($contentType, 'application/json')) {
            $this->handleJsonSave();
            return;
        }

        // Legacy form POST — keep for any edge cases.
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

    private function handleJsonSave(): never
    {
        header('Content-Type: application/json; charset=utf-8');

        $csrfToken = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!Csrf::validate($csrfToken)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'CSRF inválido. Atualize a página e tente novamente.']);
            exit;
        }

        $userId = SessionAuth::userId();
        if ($userId === null) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Não autenticado.']);
            exit;
        }

        $body = (string) file_get_contents('php://input');
        $data = json_decode($body, true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'JSON inválido.']);
            exit;
        }

        (new UsuarioRepository())->updateCurriculo($userId, json_encode($data, JSON_UNESCAPED_UNICODE));
        echo json_encode(['ok' => true]);
        exit;
    }
}
