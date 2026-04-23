<?php
declare(strict_types = 1);

namespace Simbiat\Website\Sitemap\Pages;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class General extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/sitemap/general.xml', 'name' => 'Static pages']
    ];
    #Sub service name
    protected string $subservice_name = 'sitemap';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Static pages';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Static pages';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Static pages';
    
    protected function generate(array $path): array
    {
        $this->h2_push = [];
        return [
            'index' => false,
            'sitemap_links' => [
                ['loc' => '', 'name' => 'Home Page', 'lastmod' => null, 'changefreq' => null],
                #About pages
                ['loc' => 'about/me', 'name' => 'About the owner', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'about/tech', 'name' => 'Technology used', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'about/tos', 'name' => 'Terms of Service', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'about/privacy', 'name' => 'Privacy Policy', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'about/security', 'name' => 'Security Policy', 'lastmod' => null, 'changefreq' => null],
                #Search pages
                ['loc' => 'bictracker/search', 'name' => 'BIC Tracker Search', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'fftracker/search', 'name' => 'FFXIV Tracker Search', 'lastmod' => null, 'changefreq' => null],
                #Static FFXIV pages
                ['loc' => 'fftracker/track', 'name' => 'FFXIV Tracker Registration', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'fftracker/crests', 'name' => 'FFXIV Crests', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'fftracker/statistics/raw', 'changefreq' => 'weekly', 'name' => 'FFXIV: Raw Character Data', 'lastmod' => null],
                ['loc' => 'fftracker/statistics/characters', 'changefreq' => 'weekly', 'name' => 'FFXIV: Characters', 'lastmod' => null],
                ['loc' => 'fftracker/statistics/groups', 'changefreq' => 'weekly', 'name' => 'FFXIV: Groups', 'lastmod' => null],
                ['loc' => 'fftracker/statistics/bugs', 'changefreq' => 'weekly', 'name' => 'FFXIV: Bugs', 'lastmod' => null],
                ['loc' => 'fftracker/statistics/achievements', 'changefreq' => 'weekly', 'name' => 'FFXIV: Achievements', 'lastmod' => null],
                ['loc' => 'fftracker/statistics/timelines', 'changefreq' => 'weekly', 'name' => 'FFXIV: Timelines', 'lastmod' => null],
                ['loc' => 'fftracker/statistics/other', 'changefreq' => 'weekly', 'name' => 'FFXIV: Other', 'lastmod' => null],
                #Games pages
                ['loc' => 'games/dden', 'name' => 'Dangerous Dave: Endless Nightmare', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'games/jiangshi', 'name' => 'Jiangshi', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'games/radicalresonance', 'name' => 'Radical Resonance', 'lastmod' => null, 'changefreq' => null],
                #SupOps pages
                ['loc' => 'supops', 'name' => 'SupOps: The Pitch', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/problem', 'name' => 'SupOps: The Problem', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/solution', 'name' => 'SupOps: The Solution', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/glossary', 'name' => 'SupOps: The Glossary', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/facts', 'name' => 'SupOps: The FACTS', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/feedback', 'name' => 'SupOps: Feedback', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/automation', 'name' => 'SupOps: Automation', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/collaboration', 'name' => 'SupOps: Collaboration', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/transparency', 'name' => 'SupOps: Transparency', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/sustainability', 'name' => 'SupOps: Sustainability', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/flow', 'name' => 'SupOps: The Flow', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/l0', 'name' => 'SupOps: Level 0', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/l1', 'name' => 'SupOps: Level 1', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/l2', 'name' => 'SupOps: Level 2', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/l3', 'name' => 'SupOps: Level 3', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/l4', 'name' => 'SupOps: Level 4', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/interoperability', 'name' => 'SupOps: The Interoperability', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/resolution', 'name' => 'SupOps: The Resolution', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/scale', 'name' => 'SupOps: The Scale', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/metrics', 'name' => 'SupOps: The Metrics', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/comparison', 'name' => 'SupOps: The Comparison', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'supops/needs', 'name' => 'SupOps: The Needs', 'lastmod' => null, 'changefreq' => null],
                #Other static pages
                ['loc' => 'simplepages/devicedetector', 'name' => 'Device Detector', 'lastmod' => null, 'changefreq' => null],
                ['loc' => 'bictracker/keying', 'name' => 'BIC Tracker Keying', 'lastmod' => null, 'changefreq' => null],
            ],
        ];
    }
}
