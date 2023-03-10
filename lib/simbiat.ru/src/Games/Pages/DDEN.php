<?php
declare(strict_types=1);
namespace Simbiat\Games\Pages;

use Simbiat\Abstracts\Pages\Game;

class DDEN extends Game
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/games/dden', 'name' => 'Dangerous Dave: Endless Nightmare']
    ];
    #Sub service name
    protected string $subServiceName = 'dden';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Dangerous Dave: Endless Nightmare';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Dangerous Dave: Endless Nightmare';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Homage to Dangerous Dave in the Haunted Mansion';
    #Page's banner. Defaults to website's banner
    protected string $ogimage = '/img/ogimages/dden.png';
    #Path to game's JS file
    protected string $gameJS = '/html5games/dden/Dangerous Dave Endless Nightmare.js';
    #Flag to indicate the game has sound
    protected bool $hasSound = true;
    #Flag to indicate the game has music
    protected bool $hasMusic = false;
}
