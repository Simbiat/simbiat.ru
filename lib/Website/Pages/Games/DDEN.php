<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Games;

use Simbiat\Website\Abstracts\Pages\Game;

class DDEN extends Game
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/games/dden', 'name' => 'Dangerous Dave: Endless Nightmare']
    ];
    #Sub service name
    protected string $subservice_name = 'dden';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Dangerous Dave: Endless Nightmare';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Dangerous Dave: Endless Nightmare';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Homage to Dangerous Dave in the Haunted Mansion';
    #Page's banner. Defaults to website's banner
    protected string $og_image = '/ogimages/dden.png';
    #Path to game's JS file
    protected string $game_js = '/assets/html5games/dden/Dangerous Dave Endless Nightmare.js';
    #Flag to indicate the game has sound
    protected bool $has_sound = true;
    #Flag to indicate the game has music
    protected bool $has_music = false;
}
