<?php
declare(strict_types=1);
namespace Simbiat\Website\Games;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['jiangshi', 'dden', 'radicalresonance', 'anti'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/games/', 'name'=>'Games']
    ];
    protected string $title = 'Games';
    protected string $h1 = 'Games';
    protected string $ogdesc = 'Games';
    protected string $serviceName = 'games';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'jiangshi' => (new \Simbiat\Website\Games\Pages\Jiangshi)->get(array_slice($path, 1)),
            'dden' => (new \Simbiat\Website\Games\Pages\DDEN)->get(array_slice($path, 1)),
            'radicalresonance' => (new \Simbiat\Website\Games\Pages\RadicalResonance)->get(array_slice($path, 1)),
            'anti' => (new \Simbiat\Website\Games\Redirects\Anti)->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
