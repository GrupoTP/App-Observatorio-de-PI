<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Services;

use App\Auth\SessionAuth;
use App\Repositories\AnexoRepository;
use RuntimeException;

final class AnexoService
{
    public function __construct(
        private readonly AnexoRepository $anexos = new AnexoRepository(),
    ) {
    }

    public function canAccess(string $anexoId, string $userId, string $activeRole): bool
    {
        $anexo = $this->anexos->findById($anexoId);
        if ($anexo === null || (int) ($anexo['ativo'] ?? 0) !== 1 || (int) ($anexo['projeto_ativo'] ?? 0) !== 1) {
            return false;
        }

        return match ($activeRole) {
            SessionAuth::ROLE_COORDENADOR => true,
            SessionAuth::ROLE_PROFESSOR => $this->anexos->professorAllocatedToTurma(
                $userId,
                (string) $anexo['cod_turma'],
            ),
            SessionAuth::ROLE_ALUNO => $this->canAlunoAccess($anexo, $userId),
            default => false,
        };
    }

    /** @param array<string, mixed> $anexo */
    private function canAlunoAccess(array $anexo, string $userId): bool
    {
        if ($anexo['id_usuario_submissor'] === $userId) {
            return true;
        }

        return $this->anexos->alunoCreditedOnProject($userId, (string) $anexo['id_projeto']);
    }

    public function resolveStoragePath(string $relativePath): string
    {
        $root = rtrim((string) config('upload.storage_path'), '/');
        $normalized = str_replace('\\', '/', $relativePath);

        if ($normalized === '' || str_contains($normalized, '..') || str_starts_with($normalized, '/')) {
            throw new RuntimeException('Invalid attachment path.');
        }

        $absolute = $root . '/' . $normalized;
        $realRoot = realpath($root);
        $realFile = realpath($absolute);

        if ($realRoot === false || $realFile === false || !str_starts_with($realFile, $realRoot)) {
            throw new RuntimeException('Attachment file not found.');
        }

        return $realFile;
    }

    public function storageRoot(): string
    {
        $root = rtrim((string) config('upload.storage_path'), '/');
        if (!is_dir($root) && !@mkdir($root, 0750, true) && !is_dir($root)) {
            throw new RuntimeException('Não foi possível acessar o diretório de anexos. Verifique as permissões do servidor.');
        }

        if (!is_writable($root)) {
            throw new RuntimeException('O diretório de anexos não tem permissão de escrita.');
        }

        return $root;
    }
}
