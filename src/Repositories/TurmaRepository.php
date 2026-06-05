<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

final class TurmaRepository
{
    public function activeTurmaForAluno(string $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT t.*, c.nome_curso FROM matricula m
             INNER JOIN turma t ON t.cod_turma = m.cod_turma
             INNER JOIN curso c ON c.id_curso = t.id_curso
             WHERE m.id_usuario = :uid AND (m.ativo IS NULL OR m.ativo = 1) AND t.ativo = 1
             ORDER BY m.cod_matricula DESC LIMIT 1'
        );
        $stmt->execute(['uid' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function prazosForAluno(string $userId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT t.*, c.nome_curso,
                    (SELECT p.id_projeto FROM projeto p WHERE p.cod_turma = t.cod_turma AND p.id_usuario_submissor = :uid AND p.ativo = 1 LIMIT 1) AS id_projeto,
                    (SELECT p.situacao_projeto FROM projeto p WHERE p.cod_turma = t.cod_turma AND p.id_usuario_submissor = :uid2 AND p.ativo = 1 LIMIT 1) AS situacao_projeto
             FROM matricula m
             INNER JOIN turma t ON t.cod_turma = m.cod_turma
             INNER JOIN curso c ON c.id_curso = t.id_curso
             WHERE m.id_usuario = :uid3 AND (m.ativo IS NULL OR m.ativo = 1)'
        );
        $stmt->execute(['uid' => $userId, 'uid2' => $userId, 'uid3' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<string> */
    public function distinctCourses(): array
    {
        $stmt = Database::connection()->query('SELECT DISTINCT nome_curso FROM curso WHERE ativo = 1 ORDER BY nome_curso');

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findByCode(string $code): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT t.*, c.nome_curso FROM turma t INNER JOIN curso c ON c.id_curso = t.id_curso WHERE t.cod_turma = :c'
        );
        $stmt->execute(['c' => $code]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
