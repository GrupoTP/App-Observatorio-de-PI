<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

final class FeedbackRepository
{
    /** @return list<array<string, mixed>> */
    public function forAlunoProjects(string $userId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT f.*, p.titulo AS projeto_titulo, u.nome_civil_nome, u.nome_civil_sobrenome
             FROM feedback f
             INNER JOIN projeto p ON p.id_projeto = f.id_projeto
             INNER JOIN usuario u ON u.id_usuario = f.id_usuario
             WHERE f.ativo = 1 AND p.ativo = 1 AND (p.id_usuario_submissor = :uid OR EXISTS (
                 SELECT 1 FROM projeto_aluno_credito pac WHERE pac.id_projeto = p.id_projeto AND pac.id_usuario = :uid2
             ))
             ORDER BY f.data DESC'
        );
        $stmt->execute(['uid' => $userId, 'uid2' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByProject(string $projectId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT f.*, u.nome_civil_nome, u.nome_civil_sobrenome
             FROM feedback f
             INNER JOIN usuario u ON u.id_usuario = f.id_usuario
             WHERE f.id_projeto = :id AND f.ativo = 1
             ORDER BY f.data DESC LIMIT 1'
        );
        $stmt->execute(['id' => $projectId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @param array<string, mixed>|null $feedback */
    public function averageGradeForProject(string $projectId, ?array $feedback = null): ?float
    {
        $feedback ??= $this->findByProject($projectId);
        if ($feedback === null) {
            return null;
        }

        $scores = array_map(
            static fn (array $row): float => (float) $row['conceito'],
            $this->rubricaForFeedback((int) $feedback['id_feedback'])
        );

        if ($scores === []) {
            return null;
        }

        return round(array_sum($scores) / count($scores), 1);
    }

    /** @return list<array<string, mixed>> */
    public function rubricaForFeedback(int $feedbackId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM feedback_rubrica WHERE id_feedback = :id ORDER BY criterio'
        );
        $stmt->execute(['id' => $feedbackId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $projectId, string $professorId, string $descricao, array $rubricaScores): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO feedback (id_projeto, id_usuario, descricao, data, ativo) VALUES (:p, :u, :d, NOW(), 1)'
            );
            $stmt->execute(['p' => $projectId, 'u' => $professorId, 'd' => $descricao]);
            $feedbackId = (int) $pdo->lastInsertId();

            $rubricaStmt = $pdo->prepare(
                'INSERT INTO feedback_rubrica (id_feedback, criterio, conceito) VALUES (:f, :c, :v)'
            );
            foreach ($rubricaScores as $criterio => $conceito) {
                $rubricaStmt->execute(['f' => $feedbackId, 'c' => $criterio, 'v' => (string) $conceito]);
            }

            $pdo->prepare('UPDATE projeto SET situacao_projeto = \'avaliado\' WHERE id_projeto = :id')
                ->execute(['id' => $projectId]);

            $pdo->commit();

            return $feedbackId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
