<?php
declare(strict_types = 1);

namespace App\Controller\Talks;

use App\Controller\Abstracts\Page;
use App\Enum\SystemUser;
use App\Service\Config;
use function in_array;

class Post extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections']
    ];
    #Sub service name
    protected string $subservice_name = 'post';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_posts'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || (int)$id < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $post = new \App\Entity\Post($id);
        $output_array = $post->getArray();
        if (empty($output_array['id']) || empty($output_array['text'])) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($output_array['private']) {
            if ($output_array['author'] === SystemUser::Unknown->value && $output_array['owned'] === true) {
                if ($output_array['type'] === 'Support' && ($output_array['access_token'] === null || $output_array['access_token'] === '' || $output_array['access_token'] !== ($_GET['access_token'] ?? ''))) {
                    #Return same error to limit potential of brute-forcing a token
                    return ['http_error' => 403, 'reason' => 'This post is private and you lack `view_private` permission'];
                }
            } elseif ($output_array['owned'] !== true && !in_array('view_private', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'This post is private and you lack `view_private` permission'];
            }
        }
        #Check if scheduled
        if ($output_array['created'] >= \time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if we are trying to edit a post, that we can't edit
        $output_array['can_edit_post'] = false;
        if ($post->owned) {
            if (in_array('edit_own_posts', $_SESSION['permissions'], true)) {
                $output_array['can_edit_post'] = true;
            }
        } elseif (in_array('edit_others_posts', $_SESSION['permissions'], true)) {
            $output_array['can_edit_post'] = true;
        }
        if ($output_array['can_edit_post'] && $post->locked && !in_array('edit_locked', $_SESSION['permissions'], true)) {
            $output_array['can_edit_post'] = false;
        }
        #Try to exit early based on modification date
        $this->lastModified($output_array['updated']);
        #Changelogs have Unix timestamp for names, need to convert those to the desired format
        if ($output_array['type'] === 'Changelog' && \is_numeric($output_array['name'])) {
            $output_array['name'] = \date('Y.m.d', (int)$output_array['name']);
        }
        #Get history
        $history = false;
        $time = 0;
        $output_array['history'] = null;
        if (in_array('view_posts_history', $_SESSION['permissions'], true)) {
            $time = (float)($path[1] ?? 0);
            if ($time > 0) {
                $old_version = $post->getHistory($time);
                #Check if any history was returned
                if (!empty($old_version['text'])) {
                    #Update the text of the post to show
                    $output_array['text'] = $old_version['text'];
                    $output_array['created'] = (int)$old_version['time'];
                    $output_array['updated'] = $output_array['created'];
                    $this->lastModified($output_array['created']);
                    $history = true;
                    #Disable caching for the page, since history is not meant to be seen by regular users
                    $this->cache_strategy = 'private';
                    $this->cache_age = 0;
                }
            } else {
                $output_array['history'] = $post->getHistory();
            }
        }
        #Add parents to breadcrumbs
        foreach ($output_array['parents'] as $parent) {
            $this->breadcrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadcrumb[] = ['href' => '/talks/threads/'.$output_array['thread_id'].($output_array['page'] > 1 ? '?page='.$output_array['page'] : '').'#post_'.$id, 'name' => $output_array['name']];
        #Add current page
        $this->breadcrumb[] = ['href' => '/talks/posts/'.$id, 'name' => '#'.$id];
        #Add a version link to breadcrumb
        if ($history && $time > 0) {
            $this->breadcrumb[] = ['href' => '/talks/posts/'.$id.'/'.$time, 'name' => \date('d/m/Y H:i', (int)$time)];
        }
        #Update title, h1 and og_desc
        $this->h1 = 'Post #'.$id;
        $this->title = $this->h1;
        $this->setOgDesc($output_array['text']);
        #Duplicate the array to `post` key (required for Twig template and consistency with other pages)
        $output_array['post'] = $output_array;
        #Add a flag to hide the post's ID
        $output_array['post']['no_post_id'] = true;
        #Set ogtype
        $output_array['ogtype'] = 'article';
        $output_array['history_version'] = $history;
        #Add article open graph tags
        /** @noinspection DuplicatedCode */
        $output_array['ogextra'] =
            '<meta property="article:published_time" content="'.\date('c', $output_array['created']).'" />
            <meta property="article:modified_time" content="'.\date('c', $output_array['updated']).'" />'.
            ($output_array['author'] === 1 ? '' : '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$output_array['author'].'" />').
            ($output_array['editor'] !== 1 && $output_array['editor'] !== $output_array['author'] ? '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$output_array['author'].'" />' : '').
            '<meta property="article:section" content="'.$output_array['name'].'" />';
        return $output_array;
    }
}
