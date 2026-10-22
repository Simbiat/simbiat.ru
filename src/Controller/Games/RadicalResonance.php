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

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'radicalresonance';

    /**
     * @var string Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Radical Resonance';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Radical Resonance';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Music will prevail';

    /**
     * @var string Page's banner. Defaults to website's banner
     */
    #[\Override]
    protected string $og_image = '/ogimages/RadicalResonance.png';

    /**
     * @var string Path to game's JS file
     */
    #[\Override]
    protected string $gamemaker_js = '/assets/html5games/RadicalResonance/Radical Resonance.js';

    /**
     * @var bool Flag to indicate the game has sound
     */
    #[\Override]
    protected bool $has_sound = true;

    /**
     * @var bool Flag to indicate the game has music
     */
    #[\Override]
    protected bool $has_music = true;
}
