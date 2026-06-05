<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Services;

use App\Auth\SessionAuth;
use App\Repositories\ProjetoRepository;
use App\Repositories\TurmaRepository;
use App\Support\Uuid;

final class ProjetoService
{
    public function __construct(
        private readonly ProjetoRepository $projetos = new ProjetoRepository(),
        private readonly TurmaRepository $turmas = new TurmaRepository(),
        private readonly UploadService $uploads = new UploadService(),
    ) {
    }

    public function canAlunoAccess(string $projectId, string $userId): bool
    {
        $project = $this->projetos->findById($projectId);
        if ($project === null) {
            return false;
        }

        if ($project['id_usuario_submissor'] === $userId) {
            return true;
        }

        foreach ($this->projetos->coauthors($projectId) as $co) {
            if ($co['id_usuario'] === $userId) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $input */
    public function createForAluno(string $userId, array $input, ?array $file): string
    {
        $turma = $this->turmas->activeTurmaForAluno($userId);
        if ($turma === null) {
            throw new \RuntimeException('Nenhuma turma ativa encontrada para matrícula.');
        }

        $id = Uuid::v4();
        $this->projetos->insert([
            'id' => $id,
            'submissor' => $userId,
            'turma' => $turma['cod_turma'],
            'titulo' => $input['titulo'],
            'grupo' => $input['nome_grupo'] ?? null,
            'desc' => $input['descricao'],
            'github' => $input['link_github'] ?? '',
            'tech' => $input['tecnologias'] ?? '',
            'publico' => !empty($input['publico']) ? 1 : 0,
            'sit' => 'enviado',
            'prazo' => $turma['prazo_projetos'],
        ]);

        if ($file !== null) {
            $this->uploads->storeProjectFile($file, $id, $userId);
        }

        return $id;
    }

    /** @param array<string, mixed> $input */
    public function updateForAluno(string $projectId, string $userId, array $input, ?array $file): void
    {
        if (!$this->canAlunoAccess($projectId, $userId) || !$this->isOwner($projectId, $userId)) {
            throw new \RuntimeException('Sem permissão para editar este projeto.');
        }

        $this->projetos->update($projectId, [
            'titulo' => $input['titulo'],
            'grupo' => $input['nome_grupo'] ?? null,
            'desc' => $input['descricao'],
            'github' => $input['link_github'] ?? '',
            'tech' => $input['tecnologias'] ?? '',
            'publico' => !empty($input['publico']) ? 1 : 0,
            'sit' => $input['situacao_projeto'] ?? 'enviado',
        ]);

        if ($file !== null) {
            $this->uploads->storeProjectFile($file, $projectId, $userId);
        }
    }

    public function isOwner(string $projectId, string $userId): bool
    {
        $project = $this->projetos->findById($projectId);

        return $project !== null && $project['id_usuario_submissor'] === $userId;
    }

    /** @param list<string> $memberIds */
    public function savePiGroup(array $input, ?string $projectId, string $submitterId, array $memberIds, ?string $professorId = null): string
    {
        $turmaCode = $input['cod_turma'] ?? '';
        $turma = $this->turmas->findByCode($turmaCode);
        if ($turma === null) {
            throw new \RuntimeException('Turma inválida.');
        }

        if ($projectId === null) {
            $projectId = Uuid::v4();
            $this->projetos->insert([
                'id' => $projectId,
                'submissor' => $submitterId,
                'turma' => $turmaCode,
                'titulo' => $input['titulo'],
                'grupo' => $input['nome_grupo'] ?? null,
                'desc' => $input['descricao'] ?? '',
                'github' => $input['link_github'] ?? '',
                'tech' => $input['tecnologias'] ?? '',
                'publico' => 0,
                'sit' => $input['situacao_projeto'] ?? 'em-andamento',
                'prazo' => $input['prazo_especial'] ?? $turma['prazo_projetos'],
            ]);
        } else {
            $this->projetos->update($projectId, [
                'titulo' => $input['titulo'],
                'grupo' => $input['nome_grupo'] ?? null,
                'desc' => $input['descricao'] ?? '',
                'github' => $input['link_github'] ?? '',
                'tech' => $input['tecnologias'] ?? '',
                'publico' => 0,
                'sit' => $input['situacao_projeto'] ?? 'em-andamento',
            ]);
        }

        $this->projetos->syncCoauthors($projectId, $memberIds, $submitterId);

        return $projectId;
    }

    public function upcomingDeadlinesCount(string $userId): int
    {
        $projects = $this->projetos->forAluno($userId);
        $count = 0;
        $now = time();
        foreach ($projects as $p) {
            if ($p['situacao_projeto'] === 'avaliado') {
                continue;
            }
            $deadline = strtotime($p['prazo_especial'] ?? '');
            if ($deadline === false) {
                continue;
            }
            $days = (int) ceil(($deadline - $now) / 86400);
            if ($days > 0 && $days <= 7) {
                $count++;
            }
        }

        return $count;
    }
}
