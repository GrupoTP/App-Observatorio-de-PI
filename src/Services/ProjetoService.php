<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Services;

use App\Auth\SessionAuth;
use App\Repositories\AnexoRepository;
use App\Repositories\ProjetoRepository;
use App\Repositories\TurmaRepository;
use App\Support\Uuid;

final class ProjetoService
{
    public function __construct(
        private readonly ProjetoRepository $projetos = new ProjetoRepository(),
        private readonly TurmaRepository $turmas = new TurmaRepository(),
        private readonly UploadService $uploads = new UploadService(),
        private readonly AnexoRepository $anexos = new AnexoRepository(),
        private readonly AnexoService $anexoStorage = new AnexoService(),
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

    /**
     * @param array<string, mixed> $input
     * @param list<array{name: string, type: string, tmp_name: string, error: int, size: int}> $files
     * @param list<string> $attachmentDescriptions
     */
    public function createForAluno(string $userId, array $input, array $files = [], array $attachmentDescriptions = []): string
    {
        $validated = $this->validateSubmissaoInput($input, $files, requireAttachments: true, requireTurma: true);

        $codTurma = $validated['cod_turma'];
        if (!$this->turmas->isAlunoEnrolledInTurma($userId, $codTurma)) {
            throw new \RuntimeException('Turma inválida para sua matrícula.');
        }

        $turma = $this->turmas->findByCode($codTurma);
        if ($turma === null) {
            throw new \RuntimeException('Turma não encontrada.');
        }

        $id = Uuid::v4();
        $this->projetos->insert([
            'id' => $id,
            'submissor' => $userId,
            'turma' => $turma['cod_turma'],
            'titulo' => $validated['titulo'],
            'grupo' => $validated['nome_grupo'],
            'desc' => $validated['descricao'],
            'repo_git' => $validated['link_repo_git'],
            'tech' => $validated['tecnologias'],
            'publico' => $validated['publico'],
            'sit' => 'enviado',
            'prazo' => $turma['prazo_projetos'],
        ]);

        $this->uploads->storeProjectFiles($files, $id, $userId, $attachmentDescriptions);

        return $id;
    }

    /**
     * @param array<string, mixed> $input
     * @param list<array{name: string, type: string, tmp_name: string, error: int, size: int}> $files
     * @param list<string> $attachmentDescriptions
     * @param array<string, string> $existingAttachmentNames
     * @param array<string, string> $existingAttachmentDescriptions
     * @param list<string> $removeAttachmentIds
     */
    public function updateForAluno(
        string $projectId,
        string $userId,
        array $input,
        array $files = [],
        array $attachmentDescriptions = [],
        array $existingAttachmentNames = [],
        array $existingAttachmentDescriptions = [],
        array $removeAttachmentIds = [],
    ): void {
        if (!$this->canAlunoAccess($projectId, $userId) || !$this->isOwner($projectId, $userId)) {
            throw new \RuntimeException('Sem permissão para editar este projeto.');
        }

        $validated = $this->validateSubmissaoInput($input, $files, requireAttachments: false, requireTurma: false);
        $this->assertProjectWillHaveAttachments(
            $projectId,
            $removeAttachmentIds,
            $files,
            $existingAttachmentNames,
        );

        $this->projetos->update($projectId, [
            'titulo' => $validated['titulo'],
            'grupo' => $validated['nome_grupo'],
            'desc' => $validated['descricao'],
            'repo_git' => $validated['link_repo_git'],
            'tech' => $validated['tecnologias'],
            'publico' => $validated['publico'],
            'sit' => $input['situacao_projeto'] ?? 'enviado',
        ]);

        if ($files !== []) {
            $this->uploads->storeProjectFiles($files, $projectId, $userId, $attachmentDescriptions);
        }

        $this->applyExistingAttachmentChanges(
            $projectId,
            $existingAttachmentNames,
            $existingAttachmentDescriptions,
            $removeAttachmentIds,
        );
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
                'repo_git' => $input['link_repo_git'] ?? '',
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
                'repo_git' => $input['link_repo_git'] ?? '',
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

    /**
     * @param list<string> $removeAttachmentIds
     * @param list<array{name: string, type: string, tmp_name: string, error: int, size: int}> $newFiles
     * @param array<string, string> $existingAttachmentNames
     */
    private function assertProjectWillHaveAttachments(
        string $projectId,
        array $removeAttachmentIds,
        array $newFiles,
        array $existingAttachmentNames,
    ): void {
        $current = $this->anexos->forProject($projectId);
        $currentIds = array_map(static fn (array $anexo): string => (string) $anexo['id_anexo'], $current);
        $removeIds = array_values(array_unique(array_filter($removeAttachmentIds)));

        foreach ($removeIds as $anexoId) {
            if (!in_array($anexoId, $currentIds, true)) {
                throw new \RuntimeException('Anexo inválido para remoção.');
            }
        }

        foreach ($current as $anexo) {
            $anexoId = (string) $anexo['id_anexo'];
            if (in_array($anexoId, $removeIds, true)) {
                continue;
            }

            $nome = trim($existingAttachmentNames[$anexoId] ?? (string) $anexo['nome']);
            if ($nome === '') {
                throw new \RuntimeException('Informe o nome de todos os anexos mantidos.');
            }
        }

        $keptCount = count($currentIds) - count($removeIds);
        if ($keptCount + count($newFiles) < 1) {
            throw new \RuntimeException('O projeto deve ter ao menos um anexo.');
        }
    }

    /**
     * @param array<string, string> $existingAttachmentNames
     * @param array<string, string> $existingAttachmentDescriptions
     * @param list<string> $removeAttachmentIds
     */
    private function applyExistingAttachmentChanges(
        string $projectId,
        array $existingAttachmentNames,
        array $existingAttachmentDescriptions,
        array $removeAttachmentIds,
    ): void {
        $current = $this->anexos->forProject($projectId);
        $removeIds = array_values(array_unique(array_filter($removeAttachmentIds)));

        foreach ($current as $anexo) {
            $anexoId = (string) $anexo['id_anexo'];
            if (in_array($anexoId, $removeIds, true)) {
                continue;
            }

            if (!array_key_exists($anexoId, $existingAttachmentNames)) {
                continue;
            }

            $nome = trim($existingAttachmentNames[$anexoId]);
            $descricao = trim($existingAttachmentDescriptions[$anexoId] ?? '');
            if ($descricao === '') {
                $descricao = $nome;
            }

            $this->anexos->updateMetadata($anexoId, $nome, $descricao);
        }

        foreach ($removeIds as $anexoId) {
            if (!$this->anexos->isActiveForProject($anexoId, $projectId)) {
                continue;
            }

            $anexo = $this->anexos->findById($anexoId);
            if ($anexo === null) {
                continue;
            }

            $this->anexos->softDelete($anexoId);
            $this->anexoStorage->deleteStoredFiles($anexo);
        }
    }

    /**
     * @param array<string, mixed> $input
     * @param list<array{name: string, type: string, tmp_name: string, error: int, size: int}> $files
     * @return array{titulo: string, descricao: string, cod_turma: string, link_repo_git: string, tecnologias: string, nome_grupo: ?string, publico: int}
     */
    private function validateSubmissaoInput(
        array $input,
        array $files,
        bool $requireAttachments,
        bool $requireTurma,
    ): array {
        $titulo = trim((string) ($input['titulo'] ?? ''));
        if ($titulo === '') {
            throw new \RuntimeException('Informe o título do projeto.');
        }

        $descricao = trim((string) ($input['descricao'] ?? ''));
        if ($descricao === '') {
            throw new \RuntimeException('Informe a descrição do projeto.');
        }

        if (mb_strlen($descricao) > 500) {
            throw new \RuntimeException('A descrição deve ter no máximo 500 caracteres.');
        }

        $codTurma = trim((string) ($input['cod_turma'] ?? ''));
        if ($requireTurma && $codTurma === '') {
            throw new \RuntimeException('Selecione a turma do projeto.');
        }

        $linkRepoGit = trim((string) ($input['link_repo_git'] ?? ''));
        if ($linkRepoGit === '') {
            throw new \RuntimeException('Informe o link do repositório Git.');
        }

        if (filter_var($linkRepoGit, FILTER_VALIDATE_URL) === false) {
            throw new \RuntimeException('Informe uma URL válida do repositório Git.');
        }

        $tecnologias = trim((string) ($input['tecnologias'] ?? ''));
        if ($tecnologias === '') {
            throw new \RuntimeException('Informe as tecnologias utilizadas.');
        }

        if ($requireAttachments && $files === []) {
            throw new \RuntimeException('É obrigatório anexar ao menos um arquivo.');
        }

        $nomeGrupo = trim((string) ($input['nome_grupo'] ?? ''));
        $nomeGrupo = $nomeGrupo !== '' ? $nomeGrupo : null;

        return [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'cod_turma' => $codTurma,
            'link_repo_git' => $linkRepoGit,
            'tecnologias' => $tecnologias,
            'nome_grupo' => $nomeGrupo,
            'publico' => !empty($input['publico']) ? 1 : 0,
        ];
    }
}
