<?php
// router.php
use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

if (php_sapi_name() === 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    if (is_file($file)) {
        // Sert le fichier statique directement
        return false; // ATTENTION: ne rien mettre après ce return
    }
}

// Toutes les autres requêtes passent par Symfony
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};