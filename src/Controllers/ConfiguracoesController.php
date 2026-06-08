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
        $repo   = new UsuarioRepository();
        $user   = $repo->findByIdWithDetails($userId);
        $aluno  = SessionAuth::isAluno() ? $repo->getAlunoRow($userId) : null;
        $roles  = $repo->rolesForUser($userId);

        $this->render('aluno/configuracoes', [
            'headerTitle' => 'Configurações',
            'pageTitle'   => 'Configurações',
            'user'        => $user,
            'aluno'       => $aluno,
            'roles'       => $roles,
        ]);
    }

    public function update(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $userId = SessionAuth::userId() ?? '';
        $repo   = new UsuarioRepository();

        $fotoPerfil = $this->handleProfilePhotoUpload($userId);

        $repo->updateProfile($userId, [
            'nome_civil_nome'       => $request->input('nome_civil_nome', '') ?? '',
            'nome_civil_sobrenome'  => $request->input('nome_civil_sobrenome', '') ?? '',
            'nome_social_nome'      => $request->input('nome_social_nome'),
            'nome_social_sobrenome' => $request->input('nome_social_sobrenome'),
            'email_pessoal'         => $request->input('email_pessoal', '') ?? '',
            'data_nascimento'       => $request->input('data_nascimento'),
            'identidade_rg'         => $request->input('identidade_rg'),
            'telefone1'             => $request->input('telefone1'),
            'telefone1_whatsapp'    => $request->input('telefone1_whatsapp'),
            'telefone2'             => $request->input('telefone2'),
            'telefone2_whatsapp'    => $request->input('telefone2_whatsapp'),
            'cep'                   => $request->input('cep'),
            'endereco'              => $request->input('endereco'),
            'bairro'                => $request->input('bairro'),
            'cidade'                => $request->input('cidade'),
            'estado'                => $request->input('estado'),
            'pais'                  => $request->input('pais', 'Brasil') ?? 'Brasil',
            'foto_perfil'           => $fotoPerfil,
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

    /** Saves uploaded profile photo and returns the public path, or null if no file was uploaded. */
    private function handleProfilePhotoUpload(string $userId): ?string
    {
        if (empty($_FILES['foto_perfil']['tmp_name'])) {
            return null;
        }

        $file = $_FILES['foto_perfil'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime         = mime_content_type($file['tmp_name']);

        if (!in_array($mime, $allowedMimes, true)) {
            return null;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        $ext    = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
        $dir    = dirname(dirname(__DIR__)) . '/public/uploads/profile/';
        $filename = 'profile_' . $userId . '.' . $ext;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return null;
        }

        return '/uploads/profile/' . $filename;
    }
}
