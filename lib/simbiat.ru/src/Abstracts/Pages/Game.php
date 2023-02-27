<?php
declare(strict_types=1);
namespace Simbiat\Abstracts\Pages;

use Simbiat\Abstracts\Page;

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

    #Static pages have all the data in Twig templates, thus we just return empty array
    protected function generate(array $path): array
    {
        if (empty($this->gameJS)) {
            return ['http_error' => 500, 'reason' => 'No game script file setup'];
        }
        $file = getcwd().$this->gameJS;
        if (!file_exists($file)) {
            return ['http_error' => 500, 'reason' => 'Game script file is missing'];
        }
        $outputArray = [];
        $outputArray['gameJS'] = $this->gameJS.'?'.filemtime($file);
        return $outputArray;
    }
}
