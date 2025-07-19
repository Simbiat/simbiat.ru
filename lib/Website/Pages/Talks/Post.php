<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

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
    #Flag to indicate editor mode
    protected bool $edit_mode = false;
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || (int)$id < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $post = new \Simbiat\Website\Talks\Post($id);
        $output_array = $post->getArray();
        if (empty($output_array['id']) || empty($output_array['text'])) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($output_array['private'] && $output_array['owned'] !== true && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'This post is private and you lack `view_private` permission'];
        }
        #Check if scheduled
        if ($output_array['created'] >= \time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if we are trying to edit a post, that we can't edit
        if ($this->edit_mode) {
            if ($post->locked && !in_array('edit_locked', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'Post is locked and no `edit_locked` permission'];
            }
            if ($post->owned) {
                if (!in_array('edit_own_posts', $_SESSION['permissions'], true)) {
                    return ['http_error' => 403, 'reason' => 'No `edit_own_posts` permission'];
                }
            } elseif (!in_array('edit_others_posts', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'No `edit_others_posts` permission'];
            }
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
        if ($this->edit_mode) {
            $time = (int)($path[1] ?? 0);
            if ($time > 0 && !in_array('view_posts_history', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'No `view_posts_history` permission'];
            }
            $output_array['history'] = $post->getHistory($time);
            #Check if any history was returned
            if (!empty($output_array['history'])) {
                #Check if we are requesting a historical version, and it does exist in the array
                if (!empty($time) && isset($output_array['history'][$time])) {
                    #Update the text of the post to show
                    $output_array['text'] = $output_array['history'][$time];
                    #Mark the item as "selected"
                    $output_array['history'][$time] = true;
                    $history = true;
                    #Disable caching for the page, since history is not meant to be seen by regular users
                    $this->cache_strategy = 'private';
                    $this->cache_age = 0;
                } else {
                    #Set the first item as "selected" (it's the latest one)
                    $output_array['history'][\array_key_first($output_array['history'])] = true;
                }
            }
        }
        #Add parents to breadcrumbs
        foreach ($output_array['parents'] as $parent) {
            $this->breadcrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadcrumb[] = ['href' => '/talks/threads/'.$output_array['thread_id'].'/'.($output_array['page'] > 1 ? '?page='.$output_array['page'] : '').'#post_'.$id, 'name' => $output_array['name']];
        #Add current page
        $this->breadcrumb[] = ['href' => '/talks/posts/'.$id, 'name' => '#'.$id];
        if ($this->edit_mode) {
            #Add edit mode to breadcrumb
            $this->breadcrumb[] = ['href' => '/talks/edit/posts/'.$id, 'name' => 'Edit mode'];
        }
        #Add a version link to breadcrumb
        if ($history) {
            $this->breadcrumb[] = ['href' => '/talks/edit/posts/'.$id.'/'.$time, 'name' => \date('d/m/Y H:i', $time)];
            #We do not allow editing history
            $this->edit_mode = false;
        }
        #Update title, h1 and og_desc
        if ($this->edit_mode) {
            $this->h1 = 'Editing post #'.$id;
        } else {
            $this->h1 = 'Post #'.$id;
        }
        $this->title = $this->h1;
        $this->setOgDesc($output_array['text']);
        #Duplicate the array to `post` key (required for Twig template and consistency with other pages)
        $output_array['post'] = $output_array;
        #Add a flag to hide the post's ID
        $output_array['post']['no_post_id'] = true;
        #Set ogtype
        $output_array['ogtype'] = 'article';
        #Add article open graph tags
        /** @noinspection DuplicatedCode */
        $output_array['ogextra'] =
            '<meta property="article:published_time" content="'.\date('c', $output_array['created']).'" />
            <meta property="article:modified_time" content="'.\date('c', $output_array['updated']).'" />'.
            ($output_array['author'] === 1 ? '' : '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$output_array['author'].'" />').
            ($output_array['editor'] !== 1 && $output_array['editor'] !== $output_array['author'] ? '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$output_array['author'].'" />' : '').
            '<meta property="article:section" content="'.$output_array['name'].'" />';
        #Set flag indicating that we are in edit mode
        $output_array['edit_mode'] = $this->edit_mode;
        return $output_array;
    }
}
