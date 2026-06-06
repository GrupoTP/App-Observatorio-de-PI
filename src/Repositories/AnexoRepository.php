<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

final class AnexoRepository
{
    /** @param array{id: string, projeto: string, usuario: string, nome: string, bytes: string, desc: string, miniatura: ?string} $data */
    public function insert(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO anexo (id_anexo, id_projeto, id_usuario, nome, data_envio, bytes, descricao, miniatura, ativo)
             VALUES (:id, :projeto, :usuario, :nome, NOW(), :bytes, :desc, :miniatura, 1)'
        );

        $stmt->execute([
            'id' => $data['id'],
            'projeto' => $data['projeto'],
            'usuario' => $data['usuario'],
            'nome' => $data['nome'],
            'bytes' => $data['bytes'],
            'desc' => $data['desc'],
            'miniatura' => $data['miniatura'],
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT a.*, p.cod_turma, p.id_usuario_submissor, p.ativo AS projeto_ativo
             FROM anexo a
             INNER JOIN projeto p ON p.id_projeto = a.id_projeto
             WHERE a.id_anexo = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function forProject(string $projectId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id_anexo, id_projeto, id_usuario, nome, data_envio, descricao, miniatura, ativo
             FROM anexo WHERE id_projeto = :id AND ativo = 1 ORDER BY data_envio DESC'
        );
        $stmt->execute(['id' => $projectId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function professorAllocatedToTurma(string $professorId, string $turmaCode): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM alocacao
             WHERE id_usuario = :prof AND cod_turma = :turma AND ativo = 1
             LIMIT 1'
        );
        $stmt->execute(['prof' => $professorId, 'turma' => $turmaCode]);

        return (bool) $stmt->fetchColumn();
    }

    public function alunoCreditedOnProject(string $alunoId, string $projectId): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM projeto_aluno_credito
             WHERE id_projeto = :proj AND id_usuario = :aluno
             LIMIT 1'
        );
        $stmt->execute(['proj' => $projectId, 'aluno' => $alunoId]);

        return (bool) $stmt->fetchColumn();
    }
}
