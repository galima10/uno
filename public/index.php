<?php

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

// Ne pas toucher aux fichiers statiques
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // attention au 2e param
    $file = __DIR__ . $url;
    if (is_file($file)) {
        return false;
    }
}

// Symfony s'occupe du reste
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
