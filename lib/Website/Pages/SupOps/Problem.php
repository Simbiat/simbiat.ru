<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Problem extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/problem', 'name' => 'The Problem']
    ];
    #Sub service name
    protected string $subservice_name = 'problem';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The Problem';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The Problem';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Why does tech support fail?';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/overworked.avif',
        '/assets/images/supops/memes/trust_me.avif',
        '/assets/images/supops/navigation/idea.svg',
    ];
}
