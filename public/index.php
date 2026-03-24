<?php

use App\Kernel;

// echo 'APP_ENV=' . ($_ENV['APP_ENV'] ?? 'not set') . PHP_EOL;
// echo 'APP_DEBUG=' . ($_ENV['APP_DEBUG'] ?? 'not set') . PHP_EOL;
// exit;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';


return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
