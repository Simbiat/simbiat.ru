<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Games;

use Simbiat\Website\Abstracts\Pages\Game;

class RadicalResonance extends Game
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/games/anti', 'name' => 'Radical Resonance']
    ];
    #Sub service name
    protected string $subServiceName = 'radicalresonance';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Radical Resonance';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Radical Resonance';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Music will prevail';
    #Page's banner. Defaults to website's banner
    protected string $ogimage = '/ogimages/RadicalResonance.png';
    #Path to game's JS file
    protected string $gameJS = '/assets/html5games/RadicalResonance/Radical Resonance.js';
    #Flag to indicate the game has sound
    protected bool $hasSound = true;
    #Flag to indicate the game has music
    protected bool $hasMusic = true;
}
