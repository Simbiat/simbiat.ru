<?php

declare(strict_types=1);

namespace App\Controller\Abstracts;

/**
 * Game page class
 */
abstract class Game extends Page
{
    // Flag to indicate this is a static page
    protected bool $static = true;

    // Cache age set to 0 by default, because there is normally no need to cache static pages
    protected int $cache_age = 0;

    // Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cache_strategy = 'week';

    // Path to game's JS file
    protected string $gamemaker_js = '';

    // Flag to indicate the game has sound
    protected bool $has_sound = false;

    // Flag to indicate the game has music
    protected bool $has_music = false;

    /**
     * Generation of the page data
     *
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        if (empty($this->gamemaker_js)) {
            return ['http_error' => 500, 'reason' => 'No game script file setup'];
        }
        $file = '/app/public'.$this->gamemaker_js;
        if (!\file_exists($file)) {
            return ['http_error' => 500, 'reason' => 'Game script file is missing'];
        }
        $output_array = [];
        $output_array['gamemaker_js'] = $this->gamemaker_js.'?'.\filemtime($file);
        $output_array['has_sound'] = $this->has_sound;
        $output_array['has_music'] = $this->has_music;
        if (!empty($this->og_image)) {
            $this->h2_push_extra[] = '/assets/images'.$this->og_image;
        }

        return $output_array;
    }
}
