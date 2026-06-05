<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

final class ProjetoRepository
{
    /** @return list<array<string, mixed>> */
    public function forAluno(string $userId, ?string $status = null, ?string $search = null): array
    {
        $sql = 'SELECT p.*, t.nome_turma, t.modulo, c.nome_curso
                FROM projeto p
                INNER JOIN turma t ON t.cod_turma = p.cod_turma
                INNER JOIN curso c ON c.id_curso = t.id_curso
                WHERE p.ativo = 1 AND (p.id_usuario_submissor = :uid
                    OR EXISTS (SELECT 1 FROM projeto_aluno_credito pac WHERE pac.id_projeto = p.id_projeto AND pac.id_usuario = :uid2))';
        $params = ['uid' => $userId, 'uid2' => $userId];

        if ($status !== null && $status !== '' && $status !== 'todos') {
            $sql .= ' AND p.situacao_projeto = :status';
            $params['status'] = $status;
        }

        if ($search !== null && $search !== '') {
            $sql .= ' AND p.titulo LIKE :q';
            $params['q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.prazo_especial DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT p.*, t.nome_turma, t.modulo, t.prazo_projetos, c.nome_curso
             FROM projeto p
             INNER JOIN turma t ON t.cod_turma = p.cod_turma
             INNER JOIN curso c ON c.id_curso = t.id_curso
             WHERE p.id_projeto = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function allForAdmin(?string $status = null, ?string $search = null): array
    {
        $sql = 'SELECT p.*, t.nome_turma, t.modulo, c.nome_curso,
                       u.nome_civil_nome, u.nome_civil_sobrenome
                FROM projeto p
                INNER JOIN turma t ON t.cod_turma = p.cod_turma
                INNER JOIN curso c ON c.id_curso = t.id_curso
                INNER JOIN usuario u ON u.id_usuario = p.id_usuario_submissor
                WHERE p.ativo = 1';
        $params = [];

        if ($status !== null && $status !== '' && $status !== 'todos') {
            $sql .= ' AND p.situacao_projeto = :status';
            $params['status'] = $status;
        }

        if ($search !== null && $search !== '') {
            $sql .= ' AND (p.titulo LIKE :q OR p.nome_grupo LIKE :q2)';
            $params['q'] = '%' . $search . '%';
            $params['q2'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.prazo_especial DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** PI groups: projects with nome_grupo or coauthors */
    /** @return list<array<string, mixed>> */
    public function listPiGroups(?string $status = null, ?string $search = null, ?string $course = null): array
    {
        $sql = 'SELECT p.*, t.nome_turma, t.modulo, c.nome_curso,
                       u.nome_civil_nome, u.nome_civil_sobrenome
                FROM projeto p
                INNER JOIN turma t ON t.cod_turma = p.cod_turma
                INNER JOIN curso c ON c.id_curso = t.id_curso
                INNER JOIN usuario u ON u.id_usuario = p.id_usuario_submissor
                WHERE p.ativo = 1';
        $params = [];

        if ($status !== null && $status !== '' && $status !== 'todos') {
            $sql .= ' AND p.situacao_projeto = :status';
            $params['status'] = $status;
        }

        if ($search !== null && $search !== '') {
            $sql .= ' AND (p.titulo LIKE :q OR p.nome_grupo LIKE :q2)';
            $params['q'] = '%' . $search . '%';
            $params['q2'] = '%' . $search . '%';
        }

        if ($course !== null && $course !== '' && $course !== 'todos') {
            $sql .= ' AND c.nome_curso = :course';
            $params['course'] = $course;
        }

        $sql .= ' ORDER BY p.prazo_especial DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO projeto (id_projeto, id_usuario_submissor, cod_turma, titulo, nome_grupo, descricao,
                link_github, tecnologias, publico, situacao_projeto, prazo_especial, ativo)
             VALUES (:id, :submissor, :turma, :titulo, :grupo, :desc, :github, :tech, :publico, :sit, :prazo, 1)'
        );
        $stmt->execute($data);
    }

    public function update(string $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE projeto SET titulo = :titulo, nome_grupo = :grupo, descricao = :desc, link_github = :github,
                tecnologias = :tech, publico = :publico, situacao_projeto = :sit
             WHERE id_projeto = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function softDelete(string $id): void
    {
        $stmt = Database::connection()->prepare('UPDATE projeto SET ativo = 0 WHERE id_projeto = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @param list<string> $memberIds */
    public function syncCoauthors(string $projectId, array $memberIds, string $submitterId): void
    {
        $pdo = Database::connection();
        $pdo->prepare('DELETE FROM projeto_aluno_credito WHERE id_projeto = :id')->execute(['id' => $projectId]);

        foreach ($memberIds as $memberId) {
            if ($memberId === $submitterId) {
                continue;
            }
            $pdo->prepare(
                'INSERT INTO projeto_aluno_credito (id_projeto, id_usuario) VALUES (:p, :u)'
            )->execute(['p' => $projectId, 'u' => $memberId]);
        }
    }

    /** @return list<array<string, mixed>> */
    public function coauthors(string $projectId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT u.* FROM projeto_aluno_credito pac
             INNER JOIN usuario u ON u.id_usuario = pac.id_usuario
             WHERE pac.id_projeto = :id'
        );
        $stmt->execute(['id' => $projectId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function portfolioPublic(string $userId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT p.*, c.nome_curso, t.modulo FROM projeto p
             INNER JOIN turma t ON t.cod_turma = p.cod_turma
             INNER JOIN curso c ON c.id_curso = t.id_curso
             INNER JOIN aluno a ON a.id_usuario = :uid
             WHERE p.ativo = 1 AND p.publico = 1 AND p.situacao_projeto = \'avaliado\'
               AND (p.id_usuario_submissor = :uid2 OR EXISTS (
                   SELECT 1 FROM projeto_aluno_credito pac WHERE pac.id_projeto = p.id_projeto AND pac.id_usuario = :uid3
               ))
             ORDER BY p.prazo_especial DESC'
        );
        $stmt->execute(['uid' => $userId, 'uid2' => $userId, 'uid3' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByAluno(string $userId): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM projeto p WHERE p.ativo = 1 AND p.id_usuario_submissor = :uid'
        );
        $stmt->execute(['uid' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function countEvaluatedByAluno(string $userId): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM projeto p WHERE p.ativo = 1 AND p.id_usuario_submissor = :uid AND p.situacao_projeto = \'avaliado\''
        );
        $stmt->execute(['uid' => $userId]);

        return (int) $stmt->fetchColumn();
    }
}
