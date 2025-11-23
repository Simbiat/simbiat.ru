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
            'sitemap_links' => [
                ['loc' => '', 'name' => 'Home Page'],
                #About pages
                ['loc' => 'about/me', 'name' => 'About the owner'],
                ['loc' => 'about/tech', 'name' => 'Technology used'],
                ['loc' => 'about/tos', 'name' => 'Terms of Service'],
                ['loc' => 'about/privacy', 'name' => 'Privacy Policy'],
                ['loc' => 'about/security', 'name' => 'Security Policy'],
                #Search pages
                ['loc' => 'bictracker/search', 'name' => 'BIC Tracker Search'],
                ['loc' => 'fftracker/search', 'name' => 'FFXIV Tracker Search'],
                #Static FFXIV pages
                ['loc' => 'fftracker/track', 'name' => 'FFXIV Tracker Registration'],
                ['loc' => 'fftracker/crests', 'name' => 'FFXIV Crests'],
                ['loc' => 'fftracker/statistics/raw', 'changefreq' => 'weekly', 'name' => 'FFXIV: Raw Character Data'],
                ['loc' => 'fftracker/statistics/characters', 'changefreq' => 'weekly', 'name' => 'FFXIV: Characters'],
                ['loc' => 'fftracker/statistics/groups', 'changefreq' => 'weekly', 'name' => 'FFXIV: Groups'],
                ['loc' => 'fftracker/statistics/bugs', 'changefreq' => 'weekly', 'name' => 'FFXIV: Bugs'],
                ['loc' => 'fftracker/statistics/achievements', 'changefreq' => 'weekly', 'name' => 'FFXIV: Achievements'],
                ['loc' => 'fftracker/statistics/timelines', 'changefreq' => 'weekly', 'name' => 'FFXIV: Timelines'],
                ['loc' => 'fftracker/statistics/other', 'changefreq' => 'weekly', 'name' => 'FFXIV: Other'],
                #Games pages
                ['loc' => 'games/dden', 'name' => 'Dangerous Dave: Endless Nightmare'],
                ['loc' => 'games/jiangshi', 'name' => 'Jiangshi'],
                ['loc' => 'games/radicalresonance', 'name' => 'Radical Resonance'],
                #SupOps pages
                ['loc' => 'supops', 'name' => 'SupOps: The Pitch'],
                ['loc' => 'supops/problem', 'name' => 'SupOps: The Problem'],
                ['loc' => 'supops/solution', 'name' => 'SupOps: The Solution'],
                ['loc' => 'supops/glossary', 'name' => 'SupOps: The Glossary'],
                ['loc' => 'supops/facts', 'name' => 'SupOps: The FACTS'],
                ['loc' => 'supops/feedback', 'name' => 'SupOps: Feedback'],
                ['loc' => 'supops/automation', 'name' => 'SupOps: Automation'],
                ['loc' => 'supops/collaboration', 'name' => 'SupOps: Collaboration'],
                ['loc' => 'supops/transparency', 'name' => 'SupOps: Transparency'],
                ['loc' => 'supops/sustainability', 'name' => 'SupOps: Sustainability'],
                ['loc' => 'supops/flow', 'name' => 'SupOps: The Flow'],
                ['loc' => 'supops/l0', 'name' => 'SupOps: Level 0'],
                ['loc' => 'supops/l1', 'name' => 'SupOps: Level 1'],
                ['loc' => 'supops/l2', 'name' => 'SupOps: Level 2'],
                ['loc' => 'supops/l3', 'name' => 'SupOps: Level 3'],
                ['loc' => 'supops/l4', 'name' => 'SupOps: Level 4'],
                ['loc' => 'supops/interoperability', 'name' => 'SupOps: The Interoperability'],
                ['loc' => 'supops/resolution', 'name' => 'SupOps: The Resolution'],
                ['loc' => 'supops/scale', 'name' => 'SupOps: The Scale'],
                ['loc' => 'supops/metrics', 'name' => 'SupOps: The Metrics'],
                ['loc' => 'supops/comparison', 'name' => 'SupOps: The Comparison'],
                ['loc' => 'supops/needs', 'name' => 'SupOps: The Needs'],
                #Other static pages
                ['loc' => 'simplepages/devicedetector', 'name' => 'Device Detector'],
                ['loc' => 'bictracker/keying', 'name' => 'BIC Tracker Keying'],
            ],
        ];
    }
}
