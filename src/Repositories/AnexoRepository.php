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
    public function insert(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO anexo (id_anexo, id_projeto, id_usuario, nome, data_envio, bytes, descricao, ativo)
             VALUES (:id, :projeto, :usuario, :nome, NOW(), :bytes, :desc, 1)'
        );
        $stmt->execute($data);
    }

    /** @return list<array<string, mixed>> */
    public function forProject(string $projectId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM anexo WHERE id_projeto = :id AND ativo = 1 ORDER BY data_envio DESC'
        );
        $stmt->execute(['id' => $projectId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
