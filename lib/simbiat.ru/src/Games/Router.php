<?php
declare(strict_types=1);
namespace Simbiat\Games;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['jiangshi', 'dden', 'radicalresonance'];
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
            'jiangshi' => (new Pages\Jiangshi)->get(array_slice($path, 1)),
            'dden' => (new Pages\DDEN)->get(array_slice($path, 1)),
            'radicalresonance' => (new Pages\RadicalResonance)->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
