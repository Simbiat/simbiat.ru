<?php
declare(strict_types=1);
namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Abstracts\Page;

class Game extends Page
{
    #Flag to indicate this is a static page
    protected bool $static = true;
    #Cache age set to 0 by default, because there is normally no need to cache static pages
    protected int $cacheAge = 0;
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'week';
    #Path to game's JS file
    protected string $gameJS = '';
    #Flag to indicate the game has sound
    protected bool $hasSound = false;
    #Flag to indicate the game has music
    protected bool $hasMusic = false;

    #Static pages have all the data in Twig templates, thus we just return empty array
    protected function generate(array $path): array
    {
        #Allow `data:`
        @header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' https://img2.finalfantasyxiv.com; manifest-src \'self\'; media-src \'self\' data:; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        if (empty($this->gameJS)) {
            return ['http_error' => 500, 'reason' => 'No game script file setup'];
        }
        $file = '/app/public'.$this->gameJS;
        if (!file_exists($file)) {
            return ['http_error' => 500, 'reason' => 'Game script file is missing'];
        }
        $outputArray = [];
        $outputArray['gameJS'] = $this->gameJS.'?'.filemtime($file);
        $outputArray['hasSound'] = $this->hasSound;
        $outputArray['hasMusic'] = $this->hasMusic;
        if (!empty($this->ogimage)) {
            $this->h2pushExtra[] = '/assets/images'.$this->ogimage;
        }
        return $outputArray;
    }
}
