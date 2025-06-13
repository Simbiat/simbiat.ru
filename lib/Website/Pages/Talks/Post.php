<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

use function in_array;

class Post extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections']
    ];
    #Sub service name
    protected string $subServiceName = 'post';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['view_posts'];
    #Flag to indicate editor mode
    protected bool $editMode = false;
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || (int)$id < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $post = new \Simbiat\Website\Talks\Post($id);
        $outputArray = $post->getArray();
        if (empty($outputArray['id']) || empty($outputArray['text'])) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($outputArray['private'] && $outputArray['owned'] !== true && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'This post is private and you lack `view_private` permission'];
        }
        #Check if scheduled
        if ($outputArray['created'] >= time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if we are trying to edit a post, that we can't edit
        if ($this->editMode) {
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
        $this->lastModified($outputArray['updated']);
        #Changelogs have Unix timestamp for names, need to convert those to the desired format
        if ($outputArray['type'] === 'Changelog' && is_numeric($outputArray['name'])) {
            $outputArray['name'] = date('Y.m.d', (int)$outputArray['name']);
        }
        #Get history
        $history = false;
        $time = 0;
        if ($this->editMode) {
            $time = (int)($path[1] ?? 0);
            if ($time > 0 && !in_array('view_posts_history', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'No `view_posts_history` permission'];
            }
            $outputArray['history'] = $post->getHistory($time);
            #Check if any history was returned
            if (!empty($outputArray['history'])) {
                #Check if we are requesting a historical version, and it does exist in the array
                if (!empty($time) && isset($outputArray['history'][$time])) {
                    #Update the text of the post to show
                    $outputArray['text'] = $outputArray['history'][$time];
                    #Mark the item as "selected"
                    $outputArray['history'][$time] = true;
                    $history = true;
                    #Disable caching for the page, since history is not meant to be seen by regular users
                    $this->cacheStrat = 'private';
                    $this->cacheAge = 0;
                } else {
                    #Set the first item as "selected" (it's the latest one)
                    $outputArray['history'][array_key_first($outputArray['history'])] = true;
                }
            }
        }
        #Add parents to breadcrumbs
        foreach ($outputArray['parents'] as $parent) {
            $this->breadCrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadCrumb[] = ['href' => '/talks/threads/'.$outputArray['thread_id'].'/'.($outputArray['page'] > 1 ? '?page='.$outputArray['page'] : '').'#post_'.$id, 'name' => $outputArray['name']];
        #Add current page
        $this->breadCrumb[] = ['href' => '/talks/posts/'.$id, 'name' => '#'.$id];
        if ($this->editMode) {
            #Add edit mode to breadcrumb
            $this->breadCrumb[] = ['href' => '/talks/edit/posts/'.$id, 'name' => 'Edit mode'];
        }
        #Add a version link to breadcrumb
        if ($history) {
            $this->breadCrumb[] = ['href' => '/talks/edit/posts/'.$id.'/'.$time, 'name' => date('d/m/Y H:i', $time)];
            #We do not allow editing history
            $this->editMode = false;
        }
        #Update title, h1 and ogdesc
        if ($this->editMode) {
            $this->h1 = 'Editing post #'.$id;
        } else {
            $this->h1 = 'Post #'.$id;
        }
        $this->title = $this->h1;
        $this->setOgDesc($outputArray['text']);
        #Duplicate the array to `post` key (required for Twig template and consistency with other pages)
        $outputArray['post'] = $outputArray;
        #Add a flag to hide the post's ID
        $outputArray['post']['noPostId'] = true;
        #Set ogtype
        $outputArray['ogtype'] = 'article';
        #Add article open graph tags
        /** @noinspection DuplicatedCode */
        $outputArray['ogextra'] =
            '<meta property="article:published_time" content="'.date('c', $outputArray['created']).'" />
            <meta property="article:modified_time" content="'.date('c', $outputArray['updated']).'" />'.
            ($outputArray['author'] === 1 ? '' : '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$outputArray['author'].'" />').
            ($outputArray['editor'] !== 1 && $outputArray['editor'] !== $outputArray['author'] ? '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$outputArray['author'].'" />' : '').
            '<meta property="article:section" content="'.$outputArray['name'].'" />';
        #Set flag indicating that we are in edit mode
        $outputArray['editMode'] = $this->editMode;
        return $outputArray;
    }
}
