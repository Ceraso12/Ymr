<?php
declare(strict_types=1);

const YMR_STORAGE = __DIR__ . '/../storage';

function ymr_json(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, private');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ymr_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Strict',
        'path' => '/',
    ]);
    session_start();
}

function ymr_auth_file(): string { return YMR_STORAGE . '/admin-auth.json'; }
function ymr_catalog_file(): string { return YMR_STORAGE . '/catalog.json'; }

function ymr_read_catalog(): array {
    $file = is_file(ymr_catalog_file()) ? ymr_catalog_file() : YMR_STORAGE . '/seed-products.json';
    if (!is_file($file)) return [];
    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function ymr_write_catalog(array $products): bool {
    $json = json_encode(array_values($products), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) return false;
    $temp = YMR_STORAGE . '/catalog-' . bin2hex(random_bytes(6)) . '.tmp';
    if (file_put_contents($temp, $json, LOCK_EX) === false) return false;
    @chmod($temp, 0600);
    if (!rename($temp, ymr_catalog_file())) { @unlink($temp); return false; }
    @chmod(ymr_catalog_file(), 0600);
    return true;
}

function ymr_is_admin(): bool {
    ymr_session();
    return !empty($_SESSION['ymr_admin']) && !empty($_SESSION['ymr_csrf']);
}

function ymr_require_admin(): void {
    if (!ymr_is_admin()) ymr_json(['error' => 'Faça login para continuar.'], 401);
    $sent = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($sent === '' || !hash_equals((string) $_SESSION['ymr_csrf'], $sent)) {
        ymr_json(['error' => 'Sessão expirada. Atualize a página e entre novamente.'], 403);
    }
}

function ymr_safe_text($value, int $max = 500): string {
    if (!is_string($value) && !is_numeric($value)) return '';
    $value = trim((string) $value);
    return mb_substr($value, 0, $max, 'UTF-8');
}
