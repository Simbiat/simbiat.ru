<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

class User extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/talks/users/', 'name' => 'Users']
    ];
    #Sub service name
    protected string $subservice_name = 'user';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'User profile';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'User profile';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'User profile';
    #Flag to indicate editor mode
    protected bool $edit_mode = false;
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        if (empty($path[0])) {
            Headers::redirect('https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Config::$http_host) === 1 ? 'www.' : '').Config::$http_host.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/uc/profile/');
        }
        $user = new \Simbiat\Website\usercontrol\User($path[0]);
        $output_array = [];
        $output_array['user_data'] = $user->getArray();
        if (empty($output_array['user_data']['id'])) {
            return ['http_error' => 404, 'reason' => 'User does not exist'];
        }
        #Get FF characters
        $output_array['fftracker'] = $user->getFF();
        #Get last posts and threads
        $output_array['threads'] = $user->getThreads();
        $output_array['posts'] = $user->getPosts();
        #Update meta
        $this->title = $output_array['user_data']['username'];
        $this->h1 = $this->title;
        $this->og_desc = 'Public profile of '.$output_array['user_data']['username'];
        #Setup OG profile for characters
        $output_array['ogtype'] = 'profile';
        $output_array['ogextra'] =
            '<meta property="profile:username" content="'.htmlspecialchars($output_array['user_data']['username'], ENT_QUOTES | ENT_SUBSTITUTE).'" />'.
            ($output_array['user_data']['name']['first_name'] === null ? '' : '<meta property="profile:first_name" content="'.htmlspecialchars($output_array['user_data']['name']['first_name'], ENT_QUOTES | ENT_SUBSTITUTE).'" />').
            ($output_array['user_data']['name']['last_name'] === null ? '' : '<meta property="profile:last_name" content="'.htmlspecialchars($output_array['user_data']['name']['last_name'], ENT_QUOTES | ENT_SUBSTITUTE).'" />').
            ($output_array['user_data']['sex'] === null ? '' : '<meta property="profile:gender" content="'.htmlspecialchars(($output_array['user_data']['sex'] === 1 ? 'male' : 'female'), ENT_QUOTES | ENT_SUBSTITUTE).'" />');
        #Set flag indicating that we are in edit mode
        $output_array['edit_mode'] = $this->edit_mode;
        return $output_array;
    }
}
