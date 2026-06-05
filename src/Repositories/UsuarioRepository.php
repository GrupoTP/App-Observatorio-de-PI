<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

final class UsuarioRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM usuario WHERE email_institucional = :email AND ativo = 1 LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM usuario WHERE id_usuario = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<string> */
    public function rolesForUser(string $userId): array
    {
        $roles = [];
        $pdo = Database::connection();

        if ($this->existsInTable($pdo, 'aluno', $userId)) {
            $roles[] = 'aluno';
        }
        if ($this->existsInTable($pdo, 'professor', $userId)) {
            $roles[] = 'professor';
        }
        if ($this->existsInTable($pdo, 'coordenador', $userId)) {
            $roles[] = 'coordenador';
        }

        return $roles;
    }

    /** @return list<array<string, mixed>> */
    public function listAlunos(?string $search = null): array
    {
        $sql = 'SELECT u.*, a.portfolio_publico
                FROM usuario u
                INNER JOIN aluno a ON a.id_usuario = u.id_usuario
                WHERE u.ativo = 1';
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= ' AND (u.nome_civil_nome LIKE :q OR u.nome_civil_sobrenome LIKE :q OR u.email_institucional LIKE :q)';
            $params['q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY u.nome_civil_nome, u.nome_civil_sobrenome';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUsuario(array $data, string $role): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO usuario (id_usuario, email_institucional, nome_civil_nome, nome_civil_sobrenome,
                    senha_hash, senha_salt, ativo, email_pessoal, email_pessoal_cod_validacao, email_pessoal_cod_exp)
                 VALUES (:id, :email, :nome, :sobrenome, :hash, :salt, 1, :email_pessoal, \'\', \'2099-12-31 23:59:59\')'
            );
            $stmt->execute([
                'id' => $data['id_usuario'],
                'email' => $data['email_institucional'],
                'nome' => $data['nome_civil_nome'],
                'sobrenome' => $data['nome_civil_sobrenome'],
                'hash' => $data['senha_hash'],
                'salt' => $data['senha_salt'],
                'email_pessoal' => $data['email_pessoal'] ?? $data['email_institucional'],
            ]);

            match ($role) {
                'aluno' => $pdo->prepare('INSERT INTO aluno (id_usuario) VALUES (:id)')->execute(['id' => $data['id_usuario']]),
                'professor' => $pdo->prepare('INSERT INTO professor (id_usuario) VALUES (:id)')->execute(['id' => $data['id_usuario']]),
                'coordenador' => $pdo->prepare('INSERT INTO coordenador (id_usuario) VALUES (:id)')->execute(['id' => $data['id_usuario']]),
                default => null,
            };

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function updateProfile(string $userId, array $fields): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE usuario SET nome_civil_nome = :nome, nome_civil_sobrenome = :sobrenome,
                nome_social_nome = :nome_social_nome, nome_social_sobrenome = :nome_social_sobrenome,
                email_pessoal = :email_pessoal
             WHERE id_usuario = :id'
        );
        $stmt->execute([
            'id' => $userId,
            'nome' => $fields['nome_civil_nome'],
            'sobrenome' => $fields['nome_civil_sobrenome'],
            'nome_social_nome' => $fields['nome_social_nome'] ?: null,
            'nome_social_sobrenome' => $fields['nome_social_sobrenome'] ?: null,
            'email_pessoal' => $fields['email_pessoal'],
        ]);
    }

    public function updatePassword(string $userId, string $hash, string $salt): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE usuario SET senha_hash = :hash, senha_salt = :salt WHERE id_usuario = :id'
        );
        $stmt->execute(['id' => $userId, 'hash' => $hash, 'salt' => $salt]);
    }

    public function updateCurriculo(string $userId, ?string $json): void
    {
        $stmt = Database::connection()->prepare('UPDATE usuario SET json_curriculo = :json WHERE id_usuario = :id');
        $stmt->execute(['id' => $userId, 'json' => $json]);
    }

    public function updateAlunoSettings(string $userId, bool $portfolioPublico, ?string $notificacoes): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE aluno SET portfolio_publico = :publico, notificacoes = :notif WHERE id_usuario = :id'
        );
        $stmt->execute([
            'id' => $userId,
            'publico' => $portfolioPublico ? 1 : 0,
            'notif' => $notificacoes,
        ]);
    }

    public function getAlunoRow(string $userId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM aluno WHERE id_usuario = :id');
        $stmt->execute(['id' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function existsInTable(PDO $pdo, string $table, string $userId): bool
    {
        $allowed = ['aluno', 'professor', 'coordenador'];
        if (!in_array($table, $allowed, true)) {
            return false;
        }

        $stmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE id_usuario = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);

        return (bool) $stmt->fetchColumn();
    }
}
