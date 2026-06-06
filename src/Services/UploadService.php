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
    /** @var list<string> */
    private const ALLOWED = [
        'application/pdf',
        'application/zip',
        'application/x-zip-compressed',
        'image/png',
        'image/jpeg',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public function __construct(
        private readonly AnexoRepository $anexos = new AnexoRepository(),
        private readonly AnexoService $anexoStorage = new AnexoService(),
    ) {
    }

    /**
     * @param list<array{name: string, type: string, tmp_name: string, error: int, size: int}> $files
     * @param list<string> $descriptions
     */
    public function storeProjectFiles(array $files, string $projectId, string $userId, array $descriptions = []): void
    {
        if ($files === []) {
            throw new RuntimeException('É obrigatório anexar ao menos um arquivo.');
        }

        foreach ($files as $index => $file) {
            $description = $descriptions[$index] ?? '';
            $this->storeProjectFile($file, $projectId, $userId, $description);
        }
    }

    /**
     * @param array{name: string, type: string, tmp_name: string, error: int, size: int} $file
     */
    public function storeProjectFile(array $file, string $projectId, string $userId, string $description = ''): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Falha no upload do arquivo "' . ($file['name'] ?? '') . '".');
        }

        $maxBytes = (int) config('upload.max_bytes', 1073741824);
        if (($file['size'] ?? 0) > $maxBytes) {
            throw new RuntimeException('Arquivo "' . $file['name'] . '" excede o tamanho máximo de 1 GB.');
        }

        $mime = mime_content_type($file['tmp_name']) ?: ($file['type'] ?? '');
        if (!in_array($mime, self::ALLOWED, true)) {
            throw new RuntimeException('Tipo de arquivo não permitido: ' . $file['name']);
        }

        $id = Uuid::v4();
        $extension = $this->safeExtension($file['name'], $mime);
        $relativePath = $projectId . '/' . $id . '.' . $extension;
        $absolutePath = $this->anexoStorage->storageRoot() . '/' . $relativePath;

        $projectDir = dirname($absolutePath);
        if (!is_dir($projectDir) && !@mkdir($projectDir, 0750, true) && !is_dir($projectDir)) {
            throw new RuntimeException('Não foi possível preparar o diretório de anexos.');
        }

        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            throw new RuntimeException('Não foi possível salvar o arquivo "' . $file['name'] . '".');
        }

        chmod($absolutePath, 0640);
        $thumbnailPath = $this->storeThumbnail($absolutePath, $mime, $projectId, $id);

        $this->anexos->insert([
            'id' => $id,
            'projeto' => $projectId,
            'usuario' => $userId,
            'nome' => $file['name'],
            'bytes' => $relativePath,
            'desc' => $description !== '' ? $description : $file['name'],
            'miniatura' => $thumbnailPath,
        ]);

        return $id;
    }

    private function safeExtension(string $originalName, string $mime): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        return match (true) {
            $extension !== '' && preg_match('/^[a-z0-9]{1,8}$/', $extension) === 1 => $extension,
            $mime === 'application/pdf' => 'pdf',
            $mime === 'image/png' => 'png',
            $mime === 'image/jpeg' => 'jpg',
            $mime === 'application/zip', $mime === 'application/x-zip-compressed' => 'zip',
            $mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            default => 'bin',
        };
    }

    private function storeThumbnail(string $sourcePath, string $mime, string $projectId, string $anexoId): ?string
    {
        if (!extension_loaded('gd') || !in_array($mime, ['image/png', 'image/jpeg'], true)) {
            return null;
        }

        $source = match ($mime) {
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            default => false,
        };

        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);

            return null;
        }

        $maxWidth = 240;
        $targetWidth = min($width, $maxWidth);
        $targetHeight = (int) round($height * ($targetWidth / $width));
        $thumb = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($thumb === false) {
            imagedestroy($source);

            return null;
        }

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        $relativePath = $projectId . '/' . $anexoId . '_thumb.jpg';
        $absolutePath = $this->anexoStorage->storageRoot() . '/' . $relativePath;

        if (!imagejpeg($thumb, $absolutePath, 82)) {
            imagedestroy($thumb);

            return null;
        }

        imagedestroy($thumb);
        chmod($absolutePath, 0640);

        return $relativePath;
    }
}
