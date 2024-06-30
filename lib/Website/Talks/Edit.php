<?php
declare(strict_types=1);
namespace Simbiat\Website\Talks;

use Simbiat\Website\Talks\Pages\Edit\Post;
use Simbiat\Website\Talks\Pages\Edit\Section;
use Simbiat\Website\Talks\Pages\Edit\User;

class Edit extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['sections', 'posts', 'users'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/talks/edit/', 'name'=>'Talks']
    ];
    protected string $title = 'Talks';
    protected string $h1 = 'Talks';
    protected string $ogdesc = 'Talks';
    protected string $serviceName = 'talks';
    protected string $redirectMain = '/talks/edit/sections/';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'sections' => (new Section)->get(array_slice($path, 1)),
            'posts' => (new Post)->get(array_slice($path, 1)),
            'users' => (new User)->get(array_slice($path, 1)),
        };
    }
}
