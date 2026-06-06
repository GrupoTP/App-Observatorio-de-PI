<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\SessionAuth;
use App\Http\Request;
use App\Repositories\AnexoRepository;
use App\Services\AnexoService;
use RuntimeException;

final class AnexoController extends Controller
{
    public function download(Request $request, array $params): void
    {
        $this->serveFile($params['id'] ?? '', false);
    }

    public function thumbnail(Request $request, array $params): void
    {
        $this->serveFile($params['id'] ?? '', true);
    }

    private function serveFile(string $anexoId, bool $thumbnail): void
    {
        $userId = SessionAuth::userId();
        $role = SessionAuth::activeRole();

        if ($userId === null || $role === null) {
            http_response_code(401);
            exit;
        }

        $service = new AnexoService();
        if (!$service->canAccess($anexoId, $userId, $role)) {
            http_response_code(404);
            exit;
        }

        $anexo = (new AnexoRepository())->findById($anexoId);
        if ($anexo === null) {
            http_response_code(404);
            exit;
        }

        $relativePath = $thumbnail ? ($anexo['miniatura'] ?? null) : ($anexo['bytes'] ?? null);
        if (!is_string($relativePath) || $relativePath === '') {
            http_response_code(404);
            exit;
        }

        try {
            $absolutePath = $service->resolveStoragePath($relativePath);
        } catch (RuntimeException) {
            http_response_code(404);
            exit;
        }

        if ($thumbnail) {
            header('Content-Type: image/jpeg');
            header('Content-Disposition: inline; filename="' . rawurlencode($anexoId) . '_thumb.jpg"');
        } else {
            $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Content-Disposition: attachment; filename="' . rawurlencode((string) $anexo['nome']) . '"');
        }

        header('Content-Length: ' . (string) filesize($absolutePath));
        header('X-Content-Type-Options: nosniff');
        readfile($absolutePath);
        exit;
    }
}
