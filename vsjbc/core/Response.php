<?php
class Response
{
    public static function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
        exit;
    }

    public static function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404 — Página não encontrada</h1>';
        exit;
    }

    public static function forbidden(): void
    {
        http_response_code(403);
        echo '<h1>403 — Acesso negado</h1>';
        exit;
    }
}
