<?php
declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/ai.php';

// Exibir erros apenas em desenvolvimento
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// Sessão segura
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ── Autoloader simples ───────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $dirs = [
        __DIR__ . '/core/',
        __DIR__ . '/models/',
        __DIR__ . '/controllers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/core/Helpers.php';

// ── Roteador ─────────────────────────────────────────────────────────────────
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Remover prefixo do subdiretório (vsjbc)
$base = trim(parse_url(APP_URL, PHP_URL_PATH), '/');
if ($base && str_starts_with($uri, $base)) {
    $uri = trim(substr($uri, strlen($base)), '/');
}

$method = strtoupper($_SERVER['REQUEST_METHOD']);

// Tabela de rotas: [método, padrão_regex] => [Controlador, ação]
$routes = [
    // Autenticação
    ['GET',  '',            'AuthController',      'showLogin'],
    ['GET',  'login',       'AuthController',      'showLogin'],
    ['POST', 'login',       'AuthController',      'processLogin'],
    ['GET',  'logout',      'AuthController',      'logout'],

    // Dashboard
    ['GET',  'dashboard',   'DashboardController', 'index'],

    // Demandas
    ['GET',  'demandas',              'DemandController', 'index'],
    ['GET',  'demandas/nova',         'DemandController', 'create'],
    ['POST', 'demandas/nova',         'DemandController', 'store'],
    ['GET',  'demandas/(\d+)',        'DemandController', 'show'],
    ['GET',  'demandas/(\d+)/editar', 'DemandController', 'edit'],
    ['POST', 'demandas/(\d+)/editar', 'DemandController', 'update'],
    ['POST', 'demandas/(\d+)/excluir','DemandController', 'delete'],
    ['POST', 'demandas/(\d+)/status', 'DemandController', 'changeStatus'],
    ['POST', 'demandas/(\d+)/comentar','DemandController','addComment'],

    // Relatórios
    ['GET',  'relatorios',   'ReportController',   'index'],
    ['GET',  'relatorios/exportar', 'ReportController', 'exportCsv'],

    // GLPI
    ['GET',  'glpi',         'GlpiController',     'index'],
    ['POST', 'glpi/importar','GlpiController',     'import'],

    // Oráculo – base de conhecimento (admin)
    ['GET',  'oraculo',            'OracleController', 'knowledge'],
    ['GET',  'oraculo/novo',       'OracleController', 'createKnowledge'],
    ['POST', 'oraculo/novo',       'OracleController', 'storeKnowledge'],
    ['GET',  'oraculo/(\d+)/editar','OracleController','editKnowledge'],
    ['POST', 'oraculo/(\d+)/editar','OracleController','updateKnowledge'],
    ['POST', 'oraculo/(\d+)/excluir','OracleController','deleteKnowledge'],

    // API endpoints (AJAX)
    ['POST', 'api/oracle-chat',    'OracleController', 'chat'],
    ['POST', 'api/status-demanda', 'DemandController', 'ajaxStatus'],
    ['GET',  'api/dashboard-stats','DashboardController','apiStats'],
];

$matched = false;
foreach ($routes as [$routeMethod, $pattern, $controller, $action]) {
    $regex = '#^' . $pattern . '$#';
    if ($method === $routeMethod && preg_match($regex, $uri, $matches)) {
        array_shift($matches); // remove full match
        $ctrl = new $controller();
        call_user_func_array([$ctrl, $action], $matches);
        $matched = true;
        break;
    }
}

if (!$matched) {
    Response::notFound();
}
