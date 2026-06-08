<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Auth;

final class SessionAuth
{
    public const ROLE_ALUNO = 'aluno';
    public const ROLE_PROFESSOR = 'professor';
    public const ROLE_COORDENADOR = 'coordenador';
    public const ROLE_PARCEIRO = 'parceiro';

    public static function userId(): ?string
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function activeRole(): ?string
    {
        return $_SESSION['active_role'] ?? null;
    }

    public static function isAuthenticated(): bool
    {
        return self::userId() !== null && self::activeRole() !== null;
    }

    public static function isAdmin(): bool
    {
        return self::activeRole() === self::ROLE_COORDENADOR;
    }

    public static function isProfessor(): bool
    {
        return self::activeRole() === self::ROLE_PROFESSOR;
    }

    public static function isAluno(): bool
    {
        return self::activeRole() === self::ROLE_ALUNO;
    }

    public static function isParceiro(): bool
    {
        return self::activeRole() === self::ROLE_PARCEIRO;
    }

    public static function login(string $userId, string $role): void
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['active_role'] = $role;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['active_role'], $_SESSION['pending_roles'], $_SESSION['pending_user_id']);
    }

    /** @param list<string> $roles */
    public static function setPendingProfile(string $userId, array $roles): void
    {
        $_SESSION['pending_user_id'] = $userId;
        $_SESSION['pending_roles'] = $roles;
    }

    public static function clearPending(): void
    {
        unset($_SESSION['pending_user_id'], $_SESSION['pending_roles']);
    }

    public static function pendingUserId(): ?string
    {
        return $_SESSION['pending_user_id'] ?? null;
    }

    /** @return list<string> */
    public static function pendingRoles(): array
    {
        return $_SESSION['pending_roles'] ?? [];
    }
}
