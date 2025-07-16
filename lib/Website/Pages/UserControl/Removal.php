<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;

class Removal extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/uc/removal', 'name' => 'Removal']
    ];
    #Sub service name
    protected string $subservice_name = 'removal';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Removal';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Profile removal';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Page to remove your profile';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cache_strategy = 'private';
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Link to JS module for preload
    protected string $js_module = 'uc/removal';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #This page is essentially static with 2 buttons.
        #It's not marked as static, because it will still require a DB to get session data, without which it would be useless
        return [];
    }
}
