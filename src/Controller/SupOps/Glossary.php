<?php
declare(strict_types = 1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

class Glossary extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/glossary', 'name' => 'Glossary']
    ];
    #Sub service name
    protected string $subservice_name = 'glossary';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Glossary';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Glossary';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Key terms of the concept';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/navigation/pillar.svg',
    ];
}
