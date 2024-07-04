<?php
declare(strict_types=1);
namespace Simbiat\Website\Tests;

use Simbiat\Website\Errors;
use Simbiat\http20\Common;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Simbiat\Website\usercontrol\Email;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['mail', 'styling'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/tests/', 'name'=>'Tests']
    ];
    protected string $title = 'Tests';
    protected string $h1 = 'Tests';
    protected string $ogdesc = 'Tests';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        $outputArray = [];
        #Forbid if on PROD
        if (\Simbiat\Website\Config::$PROD === true || empty($path)) {
            $outputArray['http_error'] = 403;
            return $outputArray;
        }
        switch ($path[0]) {
            case 'mail':
                if (!empty($path[1]) && $path[1] === 'send') {
                    echo (new Email(\Simbiat\Website\Config::adminMail))->send('Test Mail', ['username' => 'Simbiat'], 'Simbiat', true);
                } else {
                    try {
                        $output = EnvironmentGenerator::getTwig()->render('mail/index.twig', ['subject' => 'Test Mail', 'username' => 'Simbiat']);
                    } catch (\Throwable $exception) {
                        Errors::error_log($exception);
                        $output = 'Twig failure';
                    }
                    Common::zEcho($output, 'live');
                }
                exit;
            case 'styling':
                return ['serviceName' => 'stylingTest'];
            default:
                return ['http_error' => 400, 'reason' => 'Unsupported endpoint'];
        }
    }
}