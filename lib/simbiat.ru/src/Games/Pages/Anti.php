<?php
declare(strict_types=1);
namespace Simbiat\Games\Pages;

use Simbiat\Abstracts\Pages\Game;

class Anti extends Game
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/games/anti', 'name' => 'AntI']
    ];
    #Sub service name
    protected string $subServiceName = 'anti';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'AntI';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'AntI';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Music vs AI';
    #Page's banner. Defaults to website's banner
    protected string $ogimage = '';
    #Path to game's JS file
    protected string $gameJS = '/html5games/anti/AntI.js';
    #Flag to indicate the game has sound
    protected bool $hasSound = true;
    #Flag to indicate the game has music
    protected bool $hasMusic = true;
}
