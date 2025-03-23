<?php
declare(strict_types = 1);

namespace Simbiat\Website\Sitemap\Pages;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class General extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/sitemap/general.xml', 'name' => 'Static pages']
    ];
    #Sub service name
    protected string $subServiceName = 'sitemap';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Static pages';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Static pages';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Static pages';
    
    protected function generate(array $path): array
    {
        $this->h2push = [];
        return [
            'sitemap_links' => [
                ['loc' => '', 'name' => 'Home Page'],
                ['loc' => 'simplepages/devicedetector/', 'name' => 'Device Detector'],
                ['loc' => 'about/me/', 'name' => 'About the owner'],
                ['loc' => 'about/tech/', 'name' => 'Technology used'],
                ['loc' => 'about/tos/', 'name' => 'Terms of Service'],
                ['loc' => 'about/privacy/', 'name' => 'Privacy Policy'],
                ['loc' => 'about/security/', 'name' => 'Security Policy'],
                ['loc' => 'bictracker/search/', 'name' => 'BIC Tracker Search'],
                ['loc' => 'bictracker/keying/', 'name' => 'BIC Tracker Keying'],
                ['loc' => 'fftracker/search/', 'name' => 'FFXIV Tracker Search'],
                ['loc' => 'fftracker/track/', 'name' => 'FFXIV Tracker Registration'],
                ['loc' => 'fftracker/crests/', 'name' => 'FFXIV Crests'],
                ['loc' => 'fftracker/statistics/raw/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Raw Character Data'],
                ['loc' => 'fftracker/statistics/characters/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Characters'],
                ['loc' => 'fftracker/statistics/groups/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Groups'],
                ['loc' => 'fftracker/statistics/servers/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Servers'],
                ['loc' => 'fftracker/statistics/achievements/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Achievements'],
                ['loc' => 'fftracker/statistics/timelines/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Timelines'],
                ['loc' => 'fftracker/statistics/other/', 'changefreq' => 'weekly', 'name' => 'FFXIV: Other'],
                ['loc' => 'games/dden/', 'name' => 'Dangerous Dave: Endless Nightmare'],
                ['loc' => 'games/jiangshi/', 'name' => 'Jiangshi'],
                ['loc' => 'games/radicalresonance/', 'name' => 'Radical Resonance'],
            ],
        ];
    }
}
