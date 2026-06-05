<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AnexoRepository;
use App\Support\Uuid;
use RuntimeException;

final class UploadService
{
    private const MAX_BYTES = 10 * 1024 * 1024;
    private const ALLOWED = ['application/pdf', 'application/zip', 'image/png', 'image/jpeg'];

    public function __construct(
        private readonly AnexoRepository $anexos = new AnexoRepository(),
    ) {
    }

    public function storeProjectFile(?array $file, string $projectId, string $userId, string $description = ''): ?string
    {
        if ($file === null) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Falha no upload do arquivo.');
        }

        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new RuntimeException('Arquivo excede o tamanho máximo de 10 MB.');
        }

        $mime = mime_content_type($file['tmp_name']) ?: ($file['type'] ?? '');
        if (!in_array($mime, self::ALLOWED, true)) {
            throw new RuntimeException('Tipo de arquivo não permitido.');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'bin';
        $id = Uuid::v4();
        $filename = $id . '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext);
        $destDir = dirname(__DIR__, 2) . '/public/assets/uploads';

        if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
            throw new RuntimeException('Não foi possível criar diretório de uploads.');
        }

        $destPath = $destDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new RuntimeException('Não foi possível salvar o arquivo.');
        }

        $this->anexos->insert([
            'id' => $id,
            'projeto' => $projectId,
            'usuario' => $userId,
            'nome' => $file['name'],
            'bytes' => (int) $file['size'],
            'desc' => $description ?: $file['name'],
        ]);

        return $filename;
    }
}
