<?php
function esc(mixed $val): string
{
    return Sanitizer::esc($val);
}

function date_br(?string $date): string
{
    if (!$date) return '—';
    $d = DateTime::createFromFormat('Y-m-d', substr($date, 0, 10));
    return $d ? $d->format('d/m/Y') : $date;
}

function datetime_br(?string $dt): string
{
    if (!$dt) return '—';
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $dt);
    return $d ? $d->format('d/m/Y H:i') : $dt;
}

function flash(string $type, string $msg): void
{
    $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
}

function flash_get(): ?array
{
    $f = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $f;
}

function base_url(string $path = ''): string
{
    return APP_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function status_label(string $status): string
{
    return match($status) {
        'pendente'     => 'Pendente',
        'em_andamento' => 'Em Andamento',
        'concluida'    => 'Concluída',
        'cancelada'    => 'Cancelada',
        default        => ucfirst($status),
    };
}

function status_badge(string $status): string
{
    $class = match($status) {
        'pendente'     => 'warning',
        'em_andamento' => 'primary',
        'concluida'    => 'success',
        'cancelada'    => 'secondary',
        default        => 'light',
    };
    return '<span class="badge bg-' . $class . '">' . esc(status_label($status)) . '</span>';
}

function priority_label(string $p): string
{
    return match($p) {
        'baixa'   => 'Baixa',
        'media'   => 'Média',
        'alta'    => 'Alta',
        'critica' => 'Crítica',
        default   => ucfirst($p),
    };
}

function priority_badge(string $p): string
{
    $class = match($p) {
        'baixa'   => 'success',
        'media'   => 'info',
        'alta'    => 'warning',
        'critica' => 'danger',
        default   => 'secondary',
    };
    return '<span class="badge bg-' . $class . '">' . esc(priority_label($p)) . '</span>';
}

function truncate(string $text, int $len = 80): string
{
    return mb_strlen($text) > $len ? mb_substr($text, 0, $len) . '…' : $text;
}

function active_link(string $path): string
{
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return str_contains($current, $path) ? 'active' : '';
}

function render(string $view, array $vars = [], string $layout = 'main'): void
{
    extract($vars);
    ob_start();
    require __DIR__ . '/../views/' . $view . '.php';
    $content = ob_get_clean();
    require __DIR__ . '/../views/layouts/' . $layout . '.php';
}
