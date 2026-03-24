<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

echo 'APP_ENV=' . ($_ENV['APP_ENV'] ?? 'not set') . PHP_EOL;
echo 'APP_DEBUG=' . ($_ENV['APP_DEBUG'] ?? 'not set') . PHP_EOL;
exit;

if (php_sapi_name() === 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    if (is_file($file)) {
        // Sert le fichier statique directement
        return false; // ATTENTION: ne rien mettre après ce return
    }
}


return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
