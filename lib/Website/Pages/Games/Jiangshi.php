<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Games;

use Simbiat\Website\Abstracts\Pages\Game;

class Jiangshi extends Game
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/games/jiangshi', 'name' => 'Jiangshi']
    ];
    #Sub service name
    protected string $subservice_name = 'jiangshi';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Jiangshi';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Jiangshi';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Jiangshi, the jumping vampire. Paint the world with human blood while striving to the top of the world.';
    #Page's banner. Defaults to website's banner
    protected string $og_image = '/ogimages/jiangshi.webp';
    #Path to game's JS file
    protected string $game_js = '/assets/html5games/Jiangshi/Jiangshi.js';
    #Flag to indicate the game has sound
    protected bool $has_sound = true;
    #Flag to indicate the game has music
    protected bool $has_music = true;
}
