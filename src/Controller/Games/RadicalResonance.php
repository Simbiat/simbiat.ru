<?php

declare(strict_types=1);

namespace App\Controller\Games;

use App\Controller\Abstracts\Game;

final class RadicalResonance extends Game
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/games/anti', 'name' => 'Radical Resonance'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'radicalresonance';

    // Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Radical Resonance';

    // Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Radical Resonance';

    // Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Music will prevail';

    // Page's banner. Defaults to website's banner
    #[\Override]
    protected string $og_image = '/ogimages/RadicalResonance.png';

    // Path to game's JS file
    #[\Override]
    protected string $gamemaker_js = '/assets/html5games/RadicalResonance/Radical Resonance.js';

    // Flag to indicate the game has sound
    #[\Override]
    protected bool $has_sound = true;

    // Flag to indicate the game has music
    #[\Override]
    protected bool $has_music = true;
}
