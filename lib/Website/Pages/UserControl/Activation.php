<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;
use Simbiat\Website\usercontrol\Email;

/**
 * User activation
 */
class Activation extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/uc/activation', 'name' => 'Activation']
    ];
    #Sub service name
    protected string $subservice_name = 'activation';
    #Page title. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $title = 'User/email activation';
    #Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $h1 = 'User/email activation';
    #Page's description. Practically needed only for the main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Page used for user or email activation';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cache_strategy = 'private';
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        #Get user ID
        if (empty($_SESSION['user_id'])) {
            if (empty($path[0])) {
                $user_id = null;
            } else {
                $user_id = (int)$path[0];
            }
        } else {
            $user_id = (int)$_SESSION['user_id'];
        }
        #Get activation ID
        $activation = $path[1] ?? null;
        if ($user_id === null || $user_id < 1 || empty($activation)) {
            return ['http_error' => 403];
        }
        #Check if a user exists
        if (!Query::query('SELECT `user_id` FROM `uc__users` WHERE `user_id`=:user_id', [':user_id' => [$user_id, 'int']], return: 'check')) {
            #While technically this is closer to 404, we do not want potential abuse to get user IDs, although an attack vector here is unlikely
            return ['http_error' => 403];
        }
        $output_array = [];
        #Check if the user requires activation
        $output_array['activation'] = Query::query('SELECT `user_id` FROM `uc__user_to_group` WHERE `user_id`=:user_id AND `group_id`=:group_id', [':user_id' => [$user_id, 'int'], ':group_id' => [Config::GROUP_IDS['Unverified'], 'int']], return: 'check');
        #Get a list of mails for the user with activation codes
        $emails = Query::query('SELECT `email`, `activation` FROM `uc__emails` WHERE `user_id`=:user_id AND `activation` IS NOT NULL;', [':user_id' => [$user_id, 'int']], return: 'pair');
        #Check if the provided activation code fits any of those mails
        foreach ($emails as $email => $code) {
            if (\password_verify($activation, $code) && new Email($email)->activate($user_id)) {
                $output_array = ['activated' => true, 'email' => $email];
                break;
            }
        }
        return $output_array;
    }
}
