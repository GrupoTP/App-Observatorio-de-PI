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

    /** Returns the usuario row plus the first matricula code for the user (if any). */
    public function findByIdWithDetails(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT u.*, m.cod_matricula AS matricula
             FROM usuario u
             LEFT JOIN matricula m ON m.id_usuario = u.id_usuario AND m.ativo = 1
             WHERE u.id_usuario = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function updateProfile(string $userId, array $fields): void
    {
        $fotoPerfil = $fields['foto_perfil'] ?? null;

        $stmt = Database::connection()->prepare(
            'UPDATE usuario SET
                nome_civil_nome = :nome,
                nome_civil_sobrenome = :sobrenome,
                nome_social_nome = :nome_social_nome,
                nome_social_sobrenome = :nome_social_sobrenome,
                email_pessoal = :email_pessoal,
                data_nascimento = :data_nascimento,
                identidade_rg = :identidade_rg,
                telefone1 = :telefone1,
                telefone1_whatsapp = :telefone1_whatsapp,
                telefone2 = :telefone2,
                telefone2_whatsapp = :telefone2_whatsapp,
                cep = :cep,
                endereco = :endereco,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado,
                pais = :pais,
                foto_perfil = COALESCE(:foto_perfil, foto_perfil)
             WHERE id_usuario = :id'
        );
        $stmt->execute([
            'id'                  => $userId,
            'nome'                => $fields['nome_civil_nome'],
            'sobrenome'           => $fields['nome_civil_sobrenome'],
            'nome_social_nome'    => $fields['nome_social_nome'] ?: null,
            'nome_social_sobrenome' => $fields['nome_social_sobrenome'] ?: null,
            'email_pessoal'       => $fields['email_pessoal'],
            'data_nascimento'     => $fields['data_nascimento'] ?: null,
            'identidade_rg'       => $fields['identidade_rg'] ?: null,
            'telefone1'           => $fields['telefone1'] ?: null,
            'telefone1_whatsapp'  => empty($fields['telefone1_whatsapp']) ? 0 : 1,
            'telefone2'           => $fields['telefone2'] ?: null,
            'telefone2_whatsapp'  => empty($fields['telefone2_whatsapp']) ? 0 : 1,
            'cep'                 => $fields['cep'] ?: null,
            'endereco'            => $fields['endereco'] ?: null,
            'bairro'              => $fields['bairro'] ?: null,
            'cidade'              => $fields['cidade'] ?: null,
            'estado'              => $fields['estado'] ?: null,
            'pais'                => $fields['pais'] ?: 'Brasil',
            'foto_perfil'         => $fotoPerfil ?: null,
        ]);
    }

    public function updateLastLogin(string $userId): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE usuario SET ultimo_login = NOW() WHERE id_usuario = :id'
        );
        $stmt->execute(['id' => $userId]);
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
