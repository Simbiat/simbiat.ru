<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Forum\Show;
use Simbiat\usercontrol\Signinup;

class MainRouter extends Abstracts\Router
{
    #Supported
    protected array $subRoutes = [
        #Empty string implies homepage
        '',
        'api',
        'tests',
        #Forum/articles
        #'forum', 'thread',
        #Pages routing
        'about', 'fftracker', 'bictracker', 'uc', 'tests',
        #Feeds
        'sitemap', 'rss', 'atom',
        #Errors
        'error', 'errors', 'httperror', 'httperrors'
    ];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/', 'name'=>'Home page'],
    ];

    /**
     * @throws \Exception
     */
    protected function pageGen(array $path): array
    {
        #Some extra processing for bictracker
        if ($path[0] === 'bictracker') {
            #Tell that content is intended for Russians
            header('Content-Language: ru-RU');
        }
        #Check if API
        if ($path[0] === 'api') {
            (new Api)->uriParse(array_slice($path, 1));
            #Ensure we exit
            exit;
        }
        return match($path[0]) {
            #Forum/Articles
            'forum' => (new Show)->forum($path),
            'thread' => (new Show)->thread($path),
            #Pages routing
            'about' => (new About\Router)->route(array_slice($path, 1)),
            'bictracker' => (new bictracker\Router)->route(array_slice($path, 1)),
            'fftracker' => (new fftracker\Router)->route(array_slice($path, 1)),
            #'uc',
            'tests' => $this->tests(array_slice($path, 1)),
            #Feeds
            'sitemap', 'rss', 'atom' => (new Feeds)->uriParse($path),
            #Errors
            'error', 'errors', 'httperror', 'httperrors' => $this->error(array_slice($path, 1)),
            '' => ['h1' => 'Home', 'serviceName' => 'landing',],
            default => $this->error(['404']),
        };
    }

    #Function to process (or rather relay) $_POST data
    public function postProcess(): void
    {
        if (!empty($_POST)) {
            if (!empty($_POST['signinup'])) {
                (new Signinup)->signinup();
            }
        }
    }

    #Function to route help route error pages on frontend
    private function error(array $uri): array {
        if (empty($uri[0]) || preg_match('/\d{3}/', $uri[0]) !== 1) {
            $outputArray['http_error'] = 404;
        } else {
            $outputArray['http_error'] = intval($uri[0]);
        }
        $outputArray['error_page'] = true;
        return $outputArray;
    }

    #Function to route tests
    private function tests(array $uri): array
    {
        $outputArray = [];
        #Forbid if on PROD
        if (HomePage::$PROD === true || empty($uri)) {
            $outputArray['http_error'] = 403;
            return $outputArray;
        }
        if ($uri[0] == 'optimize') {
            (new Tests)->testDump((new optimizeTables)->setMaintenance('sys__settings', 'setting', 'maintenance', 'value')->setJsonPath('./data/tables.json')->optimize('simbiatr_simbiat', true));
            exit;
        }
        if ($uri[0] == 'mail') {
            HomePage::sendMail('simbiat@outlook.com', 'Test Mail', 'Test body', true);
            exit;
        }
        $outputArray['http_error'] = 400;
        return $outputArray;
    }
}
