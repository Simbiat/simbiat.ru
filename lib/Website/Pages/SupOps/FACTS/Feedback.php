<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\FACTS;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Feedback extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/feedback', 'name' => 'Feedback']
    ];
    #Sub service name
    protected string $subservice_name = 'feedback';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Feedback';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Feedback';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Every user voice is a data point; listen, inform, improve';
}
