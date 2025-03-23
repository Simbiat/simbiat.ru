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
    protected array $breadCrumb = [
        ['href' => '/', 'name' => 'Home']
    ];
    #Sub service name
    protected string $subServiceName = 'homepage';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Home';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Homepage of Simbiat Software';
    #List of images to H2 push
    protected array $h2pushExtra = [
        '/assets/images/ogimages/jiangshi.png',
        '/assets/images/ogimages/dden.png',
        '/assets/images/ogimages/RadicalResonance.png',
        '/assets/images/ogimages/bictracker.png',
        '/assets/images/ogimages/fftracker.png',
    ];
    
    protected function generate(array $path): array
    {
        $outputArray = ['h1' => 'Home', 'serviceName' => 'homepage'];
        $outputArray['posts'] = new User(Config::userIDs['Owner'])->getTalksStarters(true);
        #Add ogimages to H2 push
        foreach ($outputArray['posts'] as $post) {
            $this->h2pushExtra[] = $post['ogimage']['ogimage'];
        }
        return $outputArray;
    }
}
