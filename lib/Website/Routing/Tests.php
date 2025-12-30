<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Config;
use Simbiat\Website\Entities\Notifications\Test;
use Simbiat\Website\Enums\SystemUsers;
use Simbiat\Website\Errors;

class Tests extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['mail', 'styling'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/tests/', 'name' => 'Tests']
    ];
    protected string $title = 'Tests';
    protected string $h1 = 'Tests';
    protected string $og_desc = 'Tests';
    
    /**
     * This is the actual page generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function pageGen(array $path): array
    {
        $output_array = [];
        #Forbid if on PROD
        if (!empty($path[0]) && $path[0] !== 'styling') {
            if (Config::$prod || \count($path) === 0) {
                $output_array['http_error'] = 403;
                return $output_array;
            }
        }
        switch ($path[0]) {
            case 'mail':
                try {
                    new Test()->save(SystemUsers::Owner->value, email_override: Config::ADMIN_MAIL)->send(true);
                } catch (\Throwable $throwable) {
                    Errors::error_log($throwable, debug: true);
                }
                exit(0);
            case 'styling':
                return ['service_name' => 'styling_test', 'static_page' => true];
            default:
                return ['http_error' => 400, 'reason' => 'Unsupported endpoint'];
        }
    }
}