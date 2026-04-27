<?php
declare(strict_types = 1);

namespace App\Controller\About;

use App\Controller\Abstracts\Page;
use App\Entity\User;
use App\Enum\SystemUser;
use Simbiat\HTML\Cut;

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
        '/assets/images/ogimages/jiangshi.webp',
        '/assets/images/ogimages/dden.webp',
        '/assets/images/ogimages/RadicalResonance.png',
        '/assets/images/ogimages/bictracker.webp',
        '/assets/images/ogimages/fftracker.webp',
        '/assets/images/supops/logo/ogimage.webp',
    ];
    
    protected function generate(array $path): array
    {
        $output_array = ['h1' => 'Home', 'service_name' => 'homepage'];
        $output_array['posts'] = new User(SystemUser::Owner->value)->getTalksStarters(true);
        foreach ($output_array['posts'] as $post_id => $post) {
            #Add ogimages to H2 push
            $this->h2_push_extra[] = $post['og_image']['og_image'];
            $output_array['posts'][$post_id]['text'] = Cut::cut($post['text'], 400, 3, '<a href="/talks/threads/'.$post['thread_id'].'">…</a>');
        }
        return $output_array;
    }
}
