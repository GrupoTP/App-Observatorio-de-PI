<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\UsuarioRepository;
use App\Services\AuthService;
use App\Support\Csrf;
use App\Support\Flash;

final class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly UsuarioRepository $usuarios = new UsuarioRepository(),
    ) {
    }

    public function show(Request $request, array $params = []): void
    {
        Csrf::regenerate();

        if (SessionAuth::pendingUserId() !== null) {
            $this->render('auth/select-profile', [
                'pageTitle' => 'Selecionar perfil',
                'profiles' => $this->auth->profileOptions(SessionAuth::pendingRoles()),
            ], 'auth');

            return;
        }

        $this->render('auth/login', ['pageTitle' => 'Login'], 'auth');
    }

    public function login(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);

        $email = $request->input('email', '') ?? '';
        $password = $request->input('password', '') ?? '';
        $profile = $request->input('profile');

        if (SessionAuth::pendingUserId() !== null && $profile !== null) {
            $this->finalizeProfile($profile);

            return;
        }

        if ($email === '' || $password === '') {
            Flash::error('Preencha e-mail e senha.');
            redirect('/login');
        }

        $result = $this->auth->attemptLogin($email, $password);

        if (!$result['ok']) {
            Flash::error($result['error'] ?? 'Erro ao entrar.');
            redirect('/login');
        }

        /** @var array<string, mixed> $user */
        $user = $result['user'];
        $roles = $result['roles'];
        $profileOptions = $this->auth->profileOptions($roles);

        if (count($profileOptions) > 1 && ($profile === null || $profile === '')) {
            SessionAuth::setPendingProfile($user['id_usuario'], $roles);
            $this->render('auth/select-profile', [
                'pageTitle' => 'Selecionar perfil',
                'profiles' => $profileOptions,
                'email' => $email,
                'password' => $password,
            ], 'auth');

            return;
        }

        $selected = $profile ?? $profileOptions[0];
        $dbRole = $this->auth->dbRoleFromProfile($selected);
        $this->auth->completeLogin($user['id_usuario'], $dbRole);
        $this->redirectByRole($dbRole);
    }

    public function logout(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        SessionAuth::logout();
        redirect('/login');
    }

    private function finalizeProfile(string $profileLabel): void
    {
        $userId = SessionAuth::pendingUserId();
        if ($userId === null) {
            redirect('/login');
        }

        $dbRole = $this->auth->dbRoleFromProfile($profileLabel);
        $this->auth->completeLogin($userId, $dbRole);
        $this->redirectByRole($dbRole);
    }

    private function redirectByRole(string $role): void
    {
        redirect($role === 'aluno' ? '/dashboard' : '/admin/dashboard');
    }
}
