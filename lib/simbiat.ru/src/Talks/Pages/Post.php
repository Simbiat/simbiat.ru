<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Common;
use Simbiat\HTMLCut;
use Simbiat\Security;

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
        if ($outputArray['private'] && !in_array('viewPrivate', $_SESSION['permissions']) && $outputArray['createdBy'] !== $_SESSION['userid']) {
            return ['http_error' => 403, 'reason' => 'This post is private and you lack `viewPrivate` permission'];
        }
        #Check if scheduled
        if ($outputArray['created'] >= time() && !in_array('viewScheduled', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'This is a scheduled post and you lack `viewScheduled` permission'];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['updated']);
        #Changelogs have Unix timestamp for names, need to convert those to desired format
        if ($outputArray['type'] === 'Changelog' && is_numeric($outputArray['name'])) {
            $outputArray['name'] = date('Y.m.d', intval($outputArray['name']));
        }
        #Get history
        $history = false;
        $time = intval($path[1] ?? 0);
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
                #Set first item as "selected" (it's the latest one)
                $outputArray['history'][array_key_first($outputArray['history'])] = true;
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
        $this->breadCrumb[] = ['href' => '/talks/posts/'.$outputArray['id'], 'name' => '#'.$outputArray['id']];
        #Add version link to breadcrumb
        if ($history) {
            $this->breadCrumb[] = ['href' => '/talks/posts/'.$outputArray['id'].'/'.$time, 'name' => date('d/m/Y H:i', $time)];
        }
        #Update title, h1 and ogdesc
        $this->title = $this->h1 = 'Post #'.$outputArray['id'];
        $this->ogdesc = Security::sanitizeHTML(HTMLCut::Cut($outputArray['text'], 160, 1), true);
        #Duplicate the array to `post` key (required for Twig template and consistency with other pages)
        $outputArray['post'] = $outputArray;
        #Add flag to hide post ID
        $outputArray['post']['nopostid'] = true;
        #Set ogtype
        $outputArray['ogtype'] = 'article';
        #Add article open graph tags
        $outputArray['ogextra'] =
            '<meta property="article:published_time" content="'.date('c', $outputArray['created']).'" />
            <meta property="article:modified_time" content="'.date('c', $outputArray['updated']).'" />'.
            ($outputArray['createdby'] === 1 ? '' : '<meta property="article:author" content="'.Common::$baseUrl.'/talks/user/'.$outputArray['createdby'].'" />').
            ($outputArray['updatedby'] !== 1 && $outputArray['updatedby'] !== $outputArray['createdby'] ? '<meta property="article:author" content="'.Common::$baseUrl.'/talks/user/'.$outputArray['createdby'].'" />' : '').
            '<meta property="article:section" content="'.$outputArray['name'].'" />'
        ;
        #Set flag indicating that we are in edit mode
        $outputArray['editMode'] = $this->editMode;
        return $outputArray;
    }
}
