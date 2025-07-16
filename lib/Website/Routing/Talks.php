<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\Talks\Post;
use Simbiat\Website\Pages\Talks\Section;
use Simbiat\Website\Pages\Talks\Thread;
use Simbiat\Website\Pages\Talks\User;

use function array_slice;

class Talks extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['sections', 'threads', 'posts', 'users', 'edit'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/talks/', 'name' => 'Talks']
    ];
    protected string $title = 'Talks';
    protected string $h1 = 'Talks';
    protected string $ogdesc = 'Talks';
    protected string $service_name = 'talks';
    protected string $redirect_main = '/talks/sections/';
    
    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'sections' => new Section()->get(array_slice($path, 1)),
            'threads' => new Thread()->get(array_slice($path, 1)),
            'posts' => new Post()->get(array_slice($path, 1)),
            'users' => new User()->get(array_slice($path, 1)),
            'edit' => new TalksEdit()->route(array_slice($path, 1)),
        };
    }
}
