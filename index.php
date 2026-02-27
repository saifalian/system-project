<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple autoloader for App namespace
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $path = __DIR__ . '/' . str_replace('\\', '/', lcfirst($class)) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

// Load environment configurations
\App\Config\Config::loadEnv();

$route = $_GET['route'] ?? 'auth/login';

if (strpos($route, 'api/') === 0) {
    require_once __DIR__ . '/routes/api.php';
} else {
    require_once __DIR__ . '/routes/web.php';
}
