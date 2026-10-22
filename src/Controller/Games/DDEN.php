<?php

declare(strict_types=1);

namespace App\Controller\Games;

use App\Controller\Abstracts\Game;

final class DDEN extends Game
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/games/dden', 'name' => 'Dangerous Dave: Endless Nightmare'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'dden';

    /**
     * @var string Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Dangerous Dave: Endless Nightmare';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Dangerous Dave: Endless Nightmare';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Homage to Dangerous Dave in the Haunted Mansion';

    /**
     * @var string Page's banner. Defaults to website's banner
     */
    #[\Override]
    protected string $og_image = '/ogimages/dden.webp';

    /**
     * @var string Path to game's JS file
     */
    #[\Override]
    protected string $gamemaker_js = '/assets/html5games/dden/Dangerous Dave Endless Nightmare.js';

    /**
     * @var bool Flag to indicate the game has sound
     */
    #[\Override]
    protected bool $has_sound = true;
}
