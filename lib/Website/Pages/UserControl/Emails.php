<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\usercontrol\User;

class Emails extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/uc/emails', 'name' => 'Emails']
    ];
    #Sub service name
    protected string $subservice_name = 'emails';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Emails';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'List of linked emails';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'List of linked emails';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cache_strategy = 'private';
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Link to JS module for preload
    protected string $js_module = 'uc/emails';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $output_array = [];
        #Get the email list
        $output_array['emails'] = new User($_SESSION['user_id'])->getEmails();
        #Count how many emails are activated (to restrict removal of emails)
        $output_array['count_activated'] = \count(\array_filter(\array_column($output_array['emails'], 'activation'), static function ($x) {
            return empty($x);
        }));
        return $output_array;
    }
}
