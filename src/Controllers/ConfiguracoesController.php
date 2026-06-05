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
use App\Support\Password;

final class ConfiguracoesController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $userId = SessionAuth::userId() ?? '';
        $repo = new UsuarioRepository();
        $user = $repo->findById($userId);
        $aluno = SessionAuth::isAluno() ? $repo->getAlunoRow($userId) : null;

        $this->render('aluno/configuracoes', [
            'headerTitle' => 'Configurações',
            'pageTitle' => 'Configurações',
            'user' => $user,
            'aluno' => $aluno,
        ]);
    }

    public function update(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';
        $repo = new UsuarioRepository();

        $repo->updateProfile($userId, [
            'nome_civil_nome' => $request->input('nome_civil_nome', '') ?? '',
            'nome_civil_sobrenome' => $request->input('nome_civil_sobrenome', '') ?? '',
            'nome_social_nome' => $request->input('nome_social_nome'),
            'nome_social_sobrenome' => $request->input('nome_social_sobrenome'),
            'email_pessoal' => $request->input('email_pessoal', '') ?? '',
        ]);

        if (SessionAuth::isAluno()) {
            $repo->updateAlunoSettings(
                $userId,
                $request->input('portfolio_publico') === '1',
                $request->input('notificacoes')
            );
        }

        $newPassword = $request->input('nova_senha');
        if ($newPassword !== null && $newPassword !== '') {
            $hashed = Password::hash($newPassword);
            $repo->updatePassword($userId, $hashed['hash'], $hashed['salt']);
        }

        Flash::success('Configurações salvas.');
        redirect('/configuracoes');
    }
}
