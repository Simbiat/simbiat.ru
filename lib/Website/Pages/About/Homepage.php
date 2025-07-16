<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\About;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;
use Simbiat\Website\usercontrol\User;

/**
 * Class for page which is currently used as home page
 */
class Homepage extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/', 'name' => 'Home']
    ];
    #Sub service name
    protected string $subservice_name = 'homepage';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Home';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Homepage of Simbiat Software';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/ogimages/jiangshi.png',
        '/assets/images/ogimages/dden.png',
        '/assets/images/ogimages/RadicalResonance.png',
        '/assets/images/ogimages/bictracker.png',
        '/assets/images/ogimages/fftracker.png',
    ];
    
    protected function generate(array $path): array
    {
        $output_array = ['h1' => 'Home', 'service_name' => 'homepage'];
        $output_array['posts'] = new User(Config::USER_IDS['Owner'])->getTalksStarters(true);
        #Add ogimages to H2 push
        foreach ($output_array['posts'] as $post) {
            $this->h2_push_extra[] = $post['og_image']['og_image'];
        }
        return $output_array;
    }
}
