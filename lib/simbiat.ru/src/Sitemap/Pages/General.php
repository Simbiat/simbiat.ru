<?php
declare(strict_types=1);
namespace Simbiat\Sitemap\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class General extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/sitemap/html/general', 'name'=>'Static pages']
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
        return [
            'sitemap_links' => [
                ['loc'=>'', 'name'=>'Home Page'],
                ['loc'=>'simplepages/devicedetector/', 'name'=>'Device Detector'],
                ['loc'=>'about/me/', 'name'=>'About me'],
                ['loc'=>'about/website/', 'name'=>'About website'],
                ['loc'=>'about/tech/', 'name'=>'Technology used'],
                ['loc'=>'about/resume/', 'name'=>'Resume'],
                ['loc'=>'about/contacts/', 'name'=>'Contacts'],
                ['loc'=>'about/tos/', 'name'=>'Terms of Service'],
                ['loc'=>'about/privacy/', 'name'=>'Privacy Policy'],
                ['loc'=>'about/security/', 'name'=>'Security Policy'],
                ['loc'=>'about/changelog/', 'name'=>'Changelog'],
                ['loc'=>'bictracker/search/', 'name'=>'BIC Tracker Search'],
                ['loc'=>'bictracker/keying/', 'name'=>'BIC Tracker Keying'],
                ['loc'=>'fftracker/search/', 'name'=>'FFXIV Tracker Search'],
                ['loc'=>'fftracker/track/', 'name'=>'FFXIV Tracker Registration'],
                ['loc'=>'fftracker/crests/', 'name'=>'FFXIV Crests'],
                ['loc'=>'fftracker/statistics/genetics/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Genetics'],
                ['loc'=>'fftracker/statistics/astrology/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Astrology'],
                ['loc'=>'fftracker/statistics/characters/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Characters'],
                ['loc'=>'fftracker/statistics/freecompanies/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Free Companies'],
                ['loc'=>'fftracker/statistics/cities/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Cities'],
                ['loc'=>'fftracker/statistics/grandcompanies/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Grand Companies'],
                ['loc'=>'fftracker/statistics/servers/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Servers'],
                ['loc'=>'fftracker/statistics/achievements/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Achievements'],
                ['loc'=>'fftracker/statistics/timelines/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Timelines'],
                ['loc'=>'fftracker/statistics/other/', 'changefreq' => 'weekly', 'name'=>'FFXIV: Other'],
                ['loc'=>'sitemap/xml/index/', 'name'=>'XML Sitemap'],
                ['loc'=>'sitemap/html/index/', 'name'=>'HTML Sitemap'],
                ['loc'=>'sitemap/txt/index/', 'name'=>'TXT Sitemap'],
            ],
        ];
    }
}
