<?php
class Sanitizer
{
    public static function esc(mixed $val): string
    {
        return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function str(string $key, array $source = []): string
    {
        $src = $source ?: $_POST;
        return trim(strip_tags($src[$key] ?? ''));
    }

    public static function int(string $key, array $source = []): int
    {
        $src = $source ?: $_POST;
        return (int)($src[$key] ?? 0);
    }

    public static function date(?string $val): ?string
    {
        if (!$val) return null;
        $d = DateTime::createFromFormat('Y-m-d', $val);
        return ($d && $d->format('Y-m-d') === $val) ? $val : null;
    }

    public static function inList(string $val, array $allowed, string $default = ''): string
    {
        return in_array($val, $allowed, true) ? $val : $default;
    }
}
