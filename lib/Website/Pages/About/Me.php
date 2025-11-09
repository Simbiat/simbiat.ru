<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\About;

use Simbiat\Website\Abstracts\Pages\StaticPage;

/**
 * Class for page which is currently used as home page
 */
class Me extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/', 'name' => 'me']
    ];
    #Sub service name
    protected string $subservice_name = 'me';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'About me';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'About owner of Simbiat Software';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/ogimages/jiangshi.webp',
        '/assets/images/ogimages/dden.webp',
        '/assets/images/ogimages/RadicalResonance.png',
        '/assets/images/ogimages/bictracker.webp',
        '/assets/images/ogimages/fftracker.webp',
    ];
}
