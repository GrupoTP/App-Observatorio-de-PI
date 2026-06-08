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
        if ($this->existsInTable($pdo, 'parceiro', $userId)) {
            $roles[] = 'parceiro';
        }

        return $roles;
    }

    /** @return list<array<string, mixed>> */
    public function listAlunos(?string $search = null, ?string $cursoId = null): array
    {
        // Subqueries pick the first active matricula per student to avoid duplicate rows.
        $sql = "SELECT u.*, a.portfolio_publico,
                       (SELECT c2.id_curso
                        FROM matricula m2
                        INNER JOIN turma t2 ON t2.cod_turma = m2.cod_turma AND t2.ativo = 1
                        INNER JOIN curso c2 ON c2.id_curso = t2.id_curso
                        WHERE m2.id_usuario = u.id_usuario AND (m2.ativo IS NULL OR m2.ativo = 1)
                        ORDER BY m2.cod_matricula LIMIT 1) AS id_curso,
                       (SELECT c2.nome_curso
                        FROM matricula m2
                        INNER JOIN turma t2 ON t2.cod_turma = m2.cod_turma AND t2.ativo = 1
                        INNER JOIN curso c2 ON c2.id_curso = t2.id_curso
                        WHERE m2.id_usuario = u.id_usuario AND (m2.ativo IS NULL OR m2.ativo = 1)
                        ORDER BY m2.cod_matricula LIMIT 1) AS nome_curso,
                       (SELECT t2.modulo
                        FROM matricula m2
                        INNER JOIN turma t2 ON t2.cod_turma = m2.cod_turma AND t2.ativo = 1
                        WHERE m2.id_usuario = u.id_usuario AND (m2.ativo IS NULL OR m2.ativo = 1)
                        ORDER BY m2.cod_matricula LIMIT 1) AS modulo
                FROM usuario u
                INNER JOIN aluno a ON a.id_usuario = u.id_usuario
                WHERE u.ativo = 1";
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " AND (u.nome_civil_nome LIKE :q OR u.nome_civil_sobrenome LIKE :q
                      OR u.email_institucional LIKE :q
                      OR EXISTS (
                          SELECT 1 FROM matricula m2
                          INNER JOIN turma t2 ON t2.cod_turma = m2.cod_turma
                          INNER JOIN curso c2 ON c2.id_curso = t2.id_curso
                          WHERE m2.id_usuario = u.id_usuario AND c2.nome_curso LIKE :q
                      ))";
            $params['q'] = '%' . $search . '%';
        }

        if ($cursoId !== null && $cursoId !== '') {
            $sql .= " AND EXISTS (
                          SELECT 1 FROM matricula m2
                          INNER JOIN turma t2 ON t2.cod_turma = m2.cod_turma AND t2.ativo = 1
                          INNER JOIN curso c2 ON c2.id_curso = t2.id_curso
                          WHERE m2.id_usuario = u.id_usuario AND c2.id_curso = :curso_id
                      )";
            $params['curso_id'] = $cursoId;
        }

        $sql .= ' ORDER BY u.nome_civil_nome, u.nome_civil_sobrenome';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function listCursos(): array
    {
        $stmt = Database::connection()->query(
            'SELECT DISTINCT c.id_curso, c.nome_curso
             FROM curso c
             INNER JOIN turma t ON t.id_curso = c.id_curso AND t.ativo = 1
             INNER JOIN matricula m ON m.cod_turma = t.cod_turma
             WHERE c.ativo = 1
             ORDER BY c.nome_curso'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUsuario(array $data, string $role): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO usuario (
                    id_usuario, email_institucional, nome_civil_nome, nome_civil_sobrenome,
                    senha_hash, senha_salt, ativo,
                    email_pessoal, email_pessoal_cod_validacao, email_pessoal_cod_exp,
                    nome_social_nome,
                    cpf, data_nascimento, identidade_rg,
                    telefone1, telefone1_whatsapp, telefone2, telefone2_whatsapp,
                    cep, endereco, bairro, cidade, estado, pais
                ) VALUES (
                    :id, :email, :nome, :sobrenome,
                    :hash, :salt, :ativo,
                    :email_pessoal, \'\', \'2099-12-31 23:59:59\',
                    :nome_social,
                    :cpf, :data_nascimento, :identidade_rg,
                    :telefone1, :tel1_wa, :telefone2, :tel2_wa,
                    :cep, :endereco, :bairro, :cidade, :estado, :pais
                )'
            );
            $stmt->execute([
                'id'             => $data['id_usuario'],
                'email'          => $data['email_institucional'],
                'nome'           => $data['nome_civil_nome'],
                'sobrenome'      => $data['nome_civil_sobrenome'],
                'hash'           => $data['senha_hash'],
                'salt'           => $data['senha_salt'],
                'ativo'          => $data['ativo'] ?? 1,
                'email_pessoal'  => $data['email_pessoal'] ?? $data['email_institucional'],
                'nome_social'    => $data['nome_social_nome'] ?? null,
                'cpf'            => $data['cpf'] ?? null,
                'data_nascimento' => $data['data_nascimento'] ?? null,
                'identidade_rg'  => $data['identidade_rg'] ?? null,
                'telefone1'      => $data['telefone1'] ?? null,
                'tel1_wa'        => $data['telefone1_whatsapp'] ?? 0,
                'telefone2'      => $data['telefone2'] ?? null,
                'tel2_wa'        => $data['telefone2_whatsapp'] ?? 0,
                'cep'            => $data['cep'] ?? null,
                'endereco'       => $data['endereco'] ?? null,
                'bairro'         => $data['bairro'] ?? null,
                'cidade'         => $data['cidade'] ?? null,
                'estado'         => $data['estado'] ?? null,
                'pais'           => $data['pais'] ?? 'Brasil',
            ]);

            match ($role) {
                'aluno' => $pdo->prepare('INSERT INTO aluno (id_usuario) VALUES (:id)')->execute(['id' => $data['id_usuario']]),
                'professor' => $pdo->prepare('INSERT INTO professor (id_usuario) VALUES (:id)')->execute(['id' => $data['id_usuario']]),
                'coordenador' => $pdo->prepare('INSERT INTO coordenador (id_usuario) VALUES (:id)')->execute(['id' => $data['id_usuario']]),
                'parceiro' => $pdo->prepare('INSERT INTO parceiro (id_usuario, empresa) VALUES (:id, :empresa)')->execute([
                    'id' => $data['id_usuario'],
                    'empresa' => $data['empresa'] ?? '',
                ]),
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
        $allowed = ['aluno', 'professor', 'coordenador', 'parceiro'];
        if (!in_array($table, $allowed, true)) {
            return false;
        }

        $stmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE id_usuario = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);

        return (bool) $stmt->fetchColumn();
    }
}
