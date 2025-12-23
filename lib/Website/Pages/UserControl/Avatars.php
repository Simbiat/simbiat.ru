<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Entities\User;

class Avatars extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/uc/avatars', 'name' => 'Avatars']
    ];
    #Sub service name
    protected string $subservice_name = 'avatars';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Avatars';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Your avatars';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Page to edit your avatars';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cache_strategy = 'private';
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Link to JS module for preload
    protected string $js_module = 'uc/avatars';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Allow `blob:`
        @\header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' blob:; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        $output_array = [];
        #Get the avatar list
        $output_array['avatars'] = new User($_SESSION['user_id'])->getAvatars();
        return $output_array;
    }
}
