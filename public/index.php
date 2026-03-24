<?php

use App\Kernel;

// echo 'APP_ENV=' . ($_ENV['APP_ENV'] ?? 'not set') . PHP_EOL;
// echo 'APP_DEBUG=' . ($_ENV['APP_DEBUG'] ?? 'not set') . PHP_EOL;
// exit;

putenv('APP_ENV=prod');     // Force l'environnement
putenv('APP_DEBUG=0');
$_ENV['APP_ENV'] = 'prod';
$_ENV['APP_DEBUG'] = 0;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

if (php_sapi_name() === 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    // NE TRAITE QUE LES FICHIERS PHP STATIQUES
    if (is_file($file) && preg_match('/\.php$/', $file)) {
        return false;
    }
}


return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
