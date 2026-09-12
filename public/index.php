<?php

declare(strict_types=1);

use App\Kernel;

// Suppressing the inspection, since this is a path from container
/** @noinspection PhpIncludeInspection */
require_once '/app/vendor/autoload_runtime.php';

return static function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
