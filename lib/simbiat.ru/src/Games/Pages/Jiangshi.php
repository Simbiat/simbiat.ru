<?php
declare(strict_types=1);
namespace Simbiat\Games\Pages;

use Simbiat\Abstracts\Pages\Game;

class Jiangshi extends Game
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/games/jiangshi', 'name' => 'Jiangshi']
    ];
    #Sub service name
    protected string $subServiceName = 'jiangshi';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Jiangshi';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Jiangshi';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Jiangshi, the jumping vampire. Paint the world with human blood while striving to the top of the world.';
    #Path to game's JS file
    protected string $gameJS = '/html5games/Jiangshi/Jiangshi.js';
}
