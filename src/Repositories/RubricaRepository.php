<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use App\Support\Uuid;
use PDO;

final class RubricaRepository
{
    /** @return list<array<string, mixed>> */
    public function allActive(?string $codTurma = null): array
    {
        $sql = 'SELECT * FROM rubrica_criterio WHERE ativo = 1';
        $params = [];

        if ($codTurma !== null) {
            $sql .= ' AND (cod_turma IS NULL OR cod_turma = :turma)';
            $params['turma'] = $codTurma;
        }

        $sql .= ' ORDER BY ordem, nome';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @param list<array{nome: string, peso: float, ordem: int}> $criteria */
    public function replaceForTurma(string $codTurma, array $criteria): void
    {
        $pdo = Database::connection();
        $pdo->prepare('UPDATE rubrica_criterio SET ativo = 0 WHERE cod_turma = :t')->execute(['t' => $codTurma]);

        $stmt = $pdo->prepare(
            'INSERT INTO rubrica_criterio (id_criterio, cod_turma, nome, peso, ordem, ativo) VALUES (:id, :t, :n, :p, :o, 1)'
        );

        foreach ($criteria as $i => $c) {
            $stmt->execute([
                'id' => Uuid::v4(),
                't' => $codTurma,
                'n' => $c['nome'],
                'p' => $c['peso'],
                'o' => $c['ordem'] ?? ($i + 1),
            ]);
        }
    }
}
