<?php
#TODO: remove when migrating to worker mode

declare(strict_types=1);

if (opcache_reset()) {
    http_response_code(200);
} else {
    http_response_code(500);
}
