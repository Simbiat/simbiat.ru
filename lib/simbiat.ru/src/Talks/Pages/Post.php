<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Common;

class Post extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/talks/posts/', 'name'=>'Posts']
    ];
    #Sub service name
    protected string $subServiceName = 'post';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewPosts'];
    #Flag to indicate editor mode
    protected bool $editMode = false;

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || intval($id) < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $post = new \Simbiat\Talks\Entities\Post($id);
        $outputArray = $post->getArray();
        if (empty($outputArray['id']) || empty($outputArray['text'])) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($outputArray['private'] && !in_array('viewPrivate', $_SESSION['permissions']) && $outputArray['owned'] !== true) {
            return ['http_error' => 403, 'reason' => 'This post is private and you lack `viewPrivate` permission'];
        }
        #Check if scheduled
        if ($outputArray['created'] >= time() && !in_array('viewScheduled', $_SESSION['permissions'])) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if we are trying to edit a post, that we can't edit
        if ($this->editMode) {
            if ($post->locked && !in_array('editLocked', $_SESSION['permissions'])) {
                return ['http_error' => 403, 'reason' => 'Post is locked and no `editLocked` permission'];
            }
            if ($post->owned) {
                if (!in_array('editOwnPosts', $_SESSION['permissions'])) {
                    return ['http_error' => 403, 'reason' => 'No `editOwnPosts` permission'];
                }
            } else {
                if (!in_array('editOthersPosts', $_SESSION['permissions'])) {
                    return ['http_error' => 403, 'reason' => 'No `editOthersPosts` permission'];
                }
            }
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['updated']);
        #Changelogs have Unix timestamp for names, need to convert those to desired format
        if ($outputArray['type'] === 'Changelog' && is_numeric($outputArray['name'])) {
            $outputArray['name'] = date('Y.m.d', intval($outputArray['name']));
        }
        #Get history
        $history = false;
        $time = 0;
        if ($this->editMode) {
            $time = intval($path[1] ?? 0);
            if ($time > 0 && !in_array('viewPostsHistory', $_SESSION['permissions'])) {
                return ['http_error' => 403, 'reason' => 'No `viewPostsHistory` permission'];
            }
            $outputArray['history'] = $post->getHistory($time);
            #Check if any history was returned
            if (!empty($outputArray['history'])) {
                #Check if we are requesting a historical version, and it does exist in the array
                if (!empty($time) && isset($outputArray['history'][ $time ])) {
                    #Update the text of the post to show
                    $outputArray['text'] = $outputArray['history'][ $time ];
                    #Mark the item as "selected"
                    $outputArray['history'][ $time ] = true;
                    $history = true;
                    #Disable caching for the page, since history is not meant to be seen by regular users
                    $this->cacheStrat = 'private';
                    $this->cacheAge = 0;
                } else {
                    #Set first item as "selected" (it's the latest one)
                    $outputArray['history'][ array_key_first($outputArray['history']) ] = true;
                }
            }
        }
        #Reset crumbs (we do not have "posts" list)
        $this->breadCrumb = [];
        #Add parents
        foreach ($outputArray['parents'] as $parent) {
            $this->breadCrumb[] = ['href' => '/talks/sections/'.$parent['sectionid'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadCrumb[] = ['href' => '/talks/threads/'.$outputArray['threadid'], 'name' => $outputArray['name']];
        #Add current page
        $this->breadCrumb[] = ['href' => '/talks/posts/'.$id, 'name' => '#'.$id];
        if ($this->editMode) {
            #Add edit mode to breadcrumb
            $this->breadCrumb[] = ['href' => '/talks/edit/posts/'.$id, 'name' => 'Edit mode'];
        }
        #Add version link to breadcrumb
        if ($history) {
            $this->breadCrumb[] = ['href' => '/talks/edit/posts/'.$id.'/'.$time, 'name' => date('d/m/Y H:i', $time)];
            #We do not allow editing history
            $this->editMode = false;
        }
        #Update title, h1 and ogdesc
        if ($this->editMode) {
            $this->title = $this->h1 = 'Editing post #'.$id;
        } else {
            $this->title = $this->h1 = 'Post #'.$id;
        }
        $this->setOgDesc($outputArray['text']);
        #Duplicate the array to `post` key (required for Twig template and consistency with other pages)
        $outputArray['post'] = $outputArray;
        #Add flag to hide post ID
        $outputArray['post']['nopostid'] = true;
        #Set ogtype
        $outputArray['ogtype'] = 'article';
        #Add article open graph tags
        /** @noinspection DuplicatedCode */
        $outputArray['ogextra'] =
            '<meta property="article:published_time" content="'.date('c', $outputArray['created']).'" />
            <meta property="article:modified_time" content="'.date('c', $outputArray['updated']).'" />'.
            ($outputArray['createdBy'] === 1 ? '' : '<meta property="article:author" content="'.Common::$baseUrl.'/talks/user/'.$outputArray['createdBy'].'" />').
            ($outputArray['updatedBy'] !== 1 && $outputArray['updatedBy'] !== $outputArray['createdBy'] ? '<meta property="article:author" content="'.Common::$baseUrl.'/talks/user/'.$outputArray['createdBy'].'" />' : '').
            '<meta property="article:section" content="'.$outputArray['name'].'" />'
        ;
        #Set flag indicating that we are in edit mode
        $outputArray['editMode'] = $this->editMode;
        return $outputArray;
    }
}
