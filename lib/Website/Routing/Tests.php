<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\http20\Common;
use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Simbiat\Website\usercontrol\Email;

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
    protected string $ogdesc = 'Tests';
    
    #This is the actual page generation based on further details of the $path
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
                if (!empty($path[1]) && $path[1] === 'send') {
                    echo new Email(Config::ADMIN_MAIL)->send('Test Mail', ['username' => 'Simbiat'], 'Simbiat', true);
                } else {
                    try {
                        $output = EnvironmentGenerator::getTwig()->render('mail/index.twig', ['subject' => 'Test Mail', 'username' => 'Simbiat']);
                    } catch (\Throwable $exception) {
                        Errors::error_log($exception);
                        $output = 'Twig failure';
                    }
                    Common::zEcho($output, 'live');
                }
                exit(0);
            case 'styling':
                return ['service_name' => 'styling_test', 'static_page' => true];
            default:
                return ['http_error' => 400, 'reason' => 'Unsupported endpoint'];
        }
    }
}