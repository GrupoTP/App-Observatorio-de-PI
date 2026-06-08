<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Services;

use App\Auth\SessionAuth;
use App\Repositories\UsuarioRepository;
use App\Support\Password;

final class AuthService
{
    public function __construct(
        private readonly UsuarioRepository $usuarios = new UsuarioRepository(),
    ) {
    }

    /** @return array{ok: bool, error?: string, needs_profile?: bool, roles?: list<string>} */
    public function attemptLogin(string $email, string $password): array
    {
        $user = $this->usuarios->findByEmail($email);

        if ($user === null) {
            return ['ok' => false, 'error' => 'Credenciais inválidas. Verifique seu e-mail e senha.'];
        }

        if (!Password::verify($password, $user['senha_hash'], $user['senha_salt'])) {
            return ['ok' => false, 'error' => 'Credenciais inválidas. Verifique seu e-mail e senha.'];
        }

        $roles = $this->usuarios->rolesForUser($user['id_usuario']);

        if ($roles === []) {
            return ['ok' => false, 'error' => 'Usuário sem perfil ativo no sistema.'];
        }

        return ['ok' => true, 'user' => $user, 'roles' => $roles];
    }

    public function completeLogin(string $userId, string $role): void
    {
        SessionAuth::login($userId, $role);
        SessionAuth::clearPending();
    }

    public function roleLabel(string $role): string
    {
        return match ($role) {
            'aluno' => 'Aluno',
            'professor' => 'Professor',
            'coordenador' => 'Administrador',
            default => ucfirst($role),
        };
    }

    public function mapRoleForSession(string $selectedProfile): string
    {
        return match ($selectedProfile) {
            'Administrador', 'Coordenador' => SessionAuth::ROLE_COORDENADOR,
            'Professor' => SessionAuth::ROLE_PROFESSOR,
            default => SessionAuth::ROLE_ALUNO,
        };
    }

    /** @param list<string> $dbRoles */
    public function profileOptions(array $dbRoles): array
    {
        $options = [];
        foreach ($dbRoles as $role) {
            $options[] = match ($role) {
                'coordenador' => 'Administrador',
                'professor'   => 'Professor',
                'aluno'       => 'Aluno',
                'parceiro'    => 'Parceiro',
                default       => $role,
            };
        }

        return array_unique($options);
    }

    public function dbRoleFromProfile(string $profile): string
    {
        return match ($profile) {
            'Administrador', 'Coordenador' => 'coordenador',
            'Professor'                    => 'professor',
            'Parceiro'                     => 'parceiro',
            default                        => 'aluno',
        };
    }
}
