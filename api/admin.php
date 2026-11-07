<?php
declare(strict_types=1);
require __DIR__ . '/common.php';
ymr_session();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = (string) ($_GET['action'] ?? '');

if ($method === 'GET' && $action === 'status') {
    $auth = is_file(ymr_auth_file()) ? json_decode((string) file_get_contents(ymr_auth_file()), true) : null;
    ymr_json(['configured' => is_array($auth) && !empty($auth['password_hash']), 'loggedIn' => ymr_is_admin()]);
}

if ($method !== 'POST' && !($method === 'GET' && $action === 'list')) ymr_json(['error' => 'Método não permitido.'], 405);
if ($method === 'POST') $action = (string) ($_POST['action'] ?? $action);

if ($action === 'setup') {
    if (is_file(ymr_auth_file())) ymr_json(['error' => 'A configuração inicial já foi concluída.'], 409);
    $token = (string) ($_POST['setup_token'] ?? '');
    $hashFile = YMR_STORAGE . '/setup-key.sha256';
    $expected = is_file($hashFile) ? trim((string) file_get_contents($hashFile)) : '';
    if ($expected === '' || !hash_equals($expected, hash('sha256', $token))) ymr_json(['error' => 'Código de configuração inválido.'], 403);
    $username = ymr_safe_text($_POST['username'] ?? '', 64);
    $password = (string) ($_POST['password'] ?? '');
    if ($username === '' || strlen($password) < 12) ymr_json(['error' => 'Use um usuário e uma senha com pelo menos 12 caracteres.'], 422);
    $auth = ['username' => $username, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'created_at' => gmdate('c')];
    if (file_put_contents(ymr_auth_file(), json_encode($auth, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) ymr_json(['error' => 'Não foi possível gravar a configuração no servidor.'], 500);
    @chmod(ymr_auth_file(), 0600);
    session_regenerate_id(true);
    $_SESSION['ymr_admin'] = true;
    $_SESSION['ymr_csrf'] = bin2hex(random_bytes(32));
    ymr_json(['ok' => true, 'csrf' => $_SESSION['ymr_csrf']]);
}

if ($action === 'login') {
    $now = time();
    $failures = array_values(array_filter($_SESSION['ymr_login_failures'] ?? [], static fn($stamp) => is_int($stamp) && ($now - $stamp) < 900));
    if (count($failures) >= 8) ymr_json(['error' => 'Muitas tentativas nesta sessão. Aguarde 15 minutos e tente novamente.'], 429);
    $auth = is_file(ymr_auth_file()) ? json_decode((string) file_get_contents(ymr_auth_file()), true) : null;
    $username = ymr_safe_text($_POST['username'] ?? '', 64);
    $password = (string) ($_POST['password'] ?? '');
    if (!is_array($auth) || !hash_equals((string) ($auth['username'] ?? ''), $username) || !password_verify($password, (string) ($auth['password_hash'] ?? ''))) {
        $failures[] = $now;
        $_SESSION['ymr_login_failures'] = $failures;
        ymr_json(['error' => 'Usuário ou senha incorretos.'], 401);
    }
    unset($_SESSION['ymr_login_failures']);
    session_regenerate_id(true);
    $_SESSION['ymr_admin'] = true;
    $_SESSION['ymr_csrf'] = bin2hex(random_bytes(32));
    ymr_json(['ok' => true, 'csrf' => $_SESSION['ymr_csrf']]);
}

if ($action === 'logout') {
    ymr_require_admin();
    $_SESSION = [];
    session_destroy();
    ymr_json(['ok' => true]);
}

ymr_require_admin();
$products = ymr_read_catalog();

if ($method === 'GET' && $action === 'list') {
    ymr_json(['products' => $products]);
}

if ($action === 'save') {
    $id = ymr_safe_text($_POST['id'] ?? '', 64);
    $name = ymr_safe_text($_POST['name'] ?? '', 140);
    $section = ymr_safe_text($_POST['section'] ?? '', 32);
    $allowedSections = ['pet', 'autorais', 'brindes', 'variados'];
    if ($name === '' || !in_array($section, $allowedSections, true)) ymr_json(['error' => 'Preencha o nome do produto e escolha uma seção.'], 422);

    $priceRaw = trim((string) ($_POST['price'] ?? ''));
    $price = null;
    if ($priceRaw !== '') {
        $price = filter_var(str_replace(',', '.', $priceRaw), FILTER_VALIDATE_FLOAT);
        if ($price === false || $price < 0) ymr_json(['error' => 'Informe um preço válido.'], 422);
    }

    $options = array_values(array_filter(array_map(static fn($v) => trim($v), preg_split('/[\r\n,]+/', (string) ($_POST['options'] ?? '')) ?: [])));
    $options = array_slice(array_map(static fn($v) => mb_substr($v, 0, 64, 'UTF-8'), $options), 0, 24);
    $colors = array_values(array_filter(array_map(static fn($v) => trim($v), preg_split('/[\r\n,]+/', (string) ($_POST['colors'] ?? '')) ?: [])));
    $colors = array_slice(array_map(static fn($v) => mb_substr($v, 0, 64, 'UTF-8'), $colors), 0, 12);
    $availability = [];
    foreach ($options as $option) $availability[$option] = isset($_POST['available'][$option]);

    $existingIndex = null;
    foreach ($products as $i => $p) if (is_array($p) && $id !== '' && ($p['id'] ?? '') === $id) { $existingIndex = $i; break; }
    $existing = $existingIndex !== null ? $products[$existingIndex] : [];
    $imageUrl = (string) ($existing['image'] ?? '');

    if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $file = $_FILES['image'];
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) > 8 * 1024 * 1024) ymr_json(['error' => 'A imagem deve ter até 8 MB.'], 422);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime]) || @getimagesize($file['tmp_name']) === false) ymr_json(['error' => 'Envie uma imagem JPG, PNG ou WebP.'], 422);
        $uploadDir = dirname(__DIR__) . '/assets/uploads';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) ymr_json(['error' => 'Não foi possível criar a pasta de imagens.'], 500);
        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) ymr_json(['error' => 'Não foi possível salvar a imagem.'], 500);
        $imageUrl = 'assets/uploads/' . $filename;
    }

    $product = [
        'id' => $id !== '' ? $id : bin2hex(random_bytes(12)),
        'name' => $name,
        'section' => $section,
        'collection' => ymr_safe_text($_POST['collection'] ?? '', 100),
        'category' => ymr_safe_text($_POST['category'] ?? '', 100),
        'description' => ymr_safe_text($_POST['description'] ?? '', 800),
        'price' => $price,
        'image' => $imageUrl,
        'options' => $options,
        'colors' => $colors,
        'availability' => $availability,
        'active' => true,
        'kind' => $section === 'brindes' ? 'orcamento' : ($section === 'pet' ? 'personalizado' : 'produto'),
    ];
    if ($existingIndex !== null) $products[$existingIndex] = $product; else $products[] = $product;
    if (!ymr_write_catalog($products)) ymr_json(['error' => 'Não foi possível salvar o catálogo no servidor. Verifique as permissões da pasta storage.'], 500);
    ymr_json(['ok' => true, 'product' => $product]);
}

if ($action === 'archive') {
    $id = ymr_safe_text($_POST['id'] ?? '', 64);
    $found = false;
    foreach ($products as &$p) {
        if (is_array($p) && ($p['id'] ?? '') === $id) { $p['active'] = false; $found = true; break; }
    }
    unset($p);
    if (!$found) ymr_json(['error' => 'Produto não encontrado.'], 404);
    if (!ymr_write_catalog($products)) ymr_json(['error' => 'Não foi possível atualizar o catálogo.'], 500);
    ymr_json(['ok' => true]);
}

if ($action === 'toggle') {
    $id = ymr_safe_text($_POST['id'] ?? '', 64);
    $active = (string) ($_POST['active'] ?? '') === '1';
    $found = false;
    foreach ($products as &$p) {
        if (is_array($p) && ($p['id'] ?? '') === $id) { $p['active'] = $active; $found = true; break; }
    }
    unset($p);
    if (!$found) ymr_json(['error' => 'Produto não encontrado.'], 404);
    if (!ymr_write_catalog($products)) ymr_json(['error' => 'Não foi possível atualizar o catálogo.'], 500);
    ymr_json(['ok' => true]);
}

ymr_json(['error' => 'Ação desconhecida.'], 400);
