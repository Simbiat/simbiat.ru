<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Games;

use Simbiat\Website\Abstracts\Pages\Game;

class RadicalResonance extends Game
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/games/anti', 'name' => 'Radical Resonance']
    ];
    #Sub service name
    protected string $subservice_name = 'radicalresonance';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Radical Resonance';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Radical Resonance';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Music will prevail';
    #Page's banner. Defaults to website's banner
    protected string $og_image = '/ogimages/RadicalResonance.png';
    #Path to game's JS file
    protected string $game_js = '/assets/html5games/RadicalResonance/Radical Resonance.js';
    #Flag to indicate the game has sound
    protected bool $has_sound = true;
    #Flag to indicate the game has music
    protected bool $has_music = true;
}
