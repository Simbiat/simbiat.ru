<?php
declare(strict_types=1);
namespace Simbiat\Tests;

use Simbiat\Config\Twig;
use Simbiat\Errors;
use Simbiat\HTTP20\Common;
use Simbiat\optimizeTables;
use Simbiat\usercontrol\Email;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['optimize', 'mail', 'styling'];
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
        if (\Simbiat\Config\Common::$PROD === true || empty($path)) {
            $outputArray['http_error'] = 403;
            return $outputArray;
        }
        switch ($path[0]) {
            case 'optimize':
                Tests::testDump((new optimizeTables)->setMaintenance('sys__settings', 'setting', 'maintenance', 'value')->setJsonPath('./data/tables.json')->optimize('simbiatr_simbiat', true));
                exit;
            case 'mail':
                if (!empty($path[1]) && $path[1] === 'send') {
                    (new Email(\Simbiat\Config\Common::adminMail))->send('Test Mail', ['username' => 'Simbiat'], 'Simbiat');
                } else {
                    try {
                        $output = Twig::getTwig()->render('mail/index.twig', ['subject' => 'Test Mail', 'username' => 'Simbiat']);
                    } catch (\Throwable $exception) {
                        (new Errors)->error_log($exception);
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
