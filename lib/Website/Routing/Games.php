<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\Games\DDEN;
use Simbiat\Website\Pages\Games\Jiangshi;
use Simbiat\Website\Pages\Games\RadicalResonance;
use Simbiat\Website\Redirects\Games\Anti;
use function array_slice;

class Games extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['jiangshi', 'dden', 'radicalresonance', 'anti'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/games/', 'name' => 'Games']
    ];
    protected string $title = 'Games';
    protected string $h1 = 'Games';
    protected string $ogdesc = 'Games';
    protected string $serviceName = 'games';
    
    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'jiangshi' => (new Jiangshi)->get(array_slice($path, 1)),
            'dden' => (new DDEN)->get(array_slice($path, 1)),
            'radicalresonance' => (new RadicalResonance)->get(array_slice($path, 1)),
            'anti' => (new Anti)->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
