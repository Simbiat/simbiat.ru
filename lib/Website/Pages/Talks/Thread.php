<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;
use Simbiat\Website\Images;

use function in_array;

class Thread extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections']
    ];
    #Sub service name
    protected string $subServiceName = 'thread';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['view_posts'];
    #Link to JS module for preload
    protected string $jsModule = 'talks/threads';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Allow `blob:`
        @header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' blob:; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || (int)$id < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $outputArray = new \Simbiat\Website\Talks\Thread($id)->getArray();
        if (empty($outputArray['id'])) {
            return ['http_error' => 404, 'reason' => 'Thread does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($outputArray['private'] && $outputArray['author'] !== $_SESSION['user_id'] && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'This thread is private and you lack `view_private` permission'];
        }
        #Check if scheduled
        if ($outputArray['created'] >= time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Thread does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Collect times
        $times = [];
        #Add time for the current section
        if (!empty($outputArray['updated'])) {
            $times[] = $outputArray['updated'];
        }
        #Add posts times
        if (!empty($outputArray['posts']['entities'])) {
            $times = array_merge($times, array_column($outputArray['posts']['entities'], 'updated'));
        }
        #Try to exit early based on modification date
        if (!empty($times)) {
            $this->lastModified(max($times) ?? 0);
        }
        #Generate pagination data
        $page = (int)($_GET['page'] ?? 1);
        $outputArray['pagination'] = ['current' => $page, 'total' => $outputArray['posts']['pages'] ?? 1, 'prefix' => '?page='];
        if ($outputArray['pagination']['current'] > $outputArray['pagination']['total'] && $outputArray['pagination']['total'] !== 0) {
            #Redirect to last page
            Headers::redirect(Config::$baseUrl.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/threads/'.$id.'?page='.$outputArray['pagination']['total'], false);
            return [];
        }
        #Changelogs have Unix timestamp for names, need to convert those to the desired format
        /** @noinspection DuplicatedCode */
        if ($outputArray['type'] === 'Changelog' && is_numeric($outputArray['name'])) {
            $outputArray['name'] = date('Y.m.d', (int)$outputArray['name']);
        }
        #Add parents to breadcrumbs if we have any
        foreach ($outputArray['parents'] as $parent) {
            $this->breadCrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadCrumb[] = ['href' => '/talks/threads/'.$outputArray['id'], 'name' => $outputArray['name']];
        #Add a page if there is one
        if ($page > 1) {
            $this->attachCrumb('?page='.$page, 'Page '.$page);
        }
        #Update title, h1 and ogdesc
        $this->title = '`'.$outputArray['name'].'` thread';
        $this->h1 = $outputArray['name'];
        if (!empty($outputArray['posts']['entities'])) {
            $this->setOgDesc($outputArray['posts']['entities'][0]['text']);
        } else {
            $this->ogdesc = $outputArray['name'];
        }
        #Set og_image
        if (!empty($outputArray['og_image'])) {
            $outputArray = array_merge($outputArray, Images::ogImage($outputArray['og_image']));
            if (!empty($outputArray['og_image'])) {
                $this->og_image = $outputArray['og_image'];
                #Add to H2Push
                $this->h2pushExtra[] = $this->og_image;
            }
        }
        #Set ogtype
        $outputArray['ogtype'] = 'article';
        #Add article open graph tags
        /** @noinspection DuplicatedCode */
        $outputArray['ogextra'] =
            '<meta property="article:published_time" content="'.date('c', $outputArray['created']).'" />
            <meta property="article:modified_time" content="'.date('c', $outputArray['updated']).'" />'.
            ($outputArray['author'] === 1 ? '' : '<meta property="article:author" content="'.Config::$baseUrl.'/talks/user/'.$outputArray['author'].'" />').
            ($outputArray['editor'] !== 1 && $outputArray['editor'] !== $outputArray['author'] ? '<meta property="article:author" content="'.Config::$baseUrl.'/talks/user/'.$outputArray['author'].'" />' : '').
            '<meta property="article:section" content="'.$outputArray['parents'][array_key_last($outputArray['parents'])]['name'].'" />';
        foreach ($outputArray['tags'] as $tag) {
            $outputArray['ogextra'] .= '<meta property="article:tag" content="'.$tag.'" />';
        }
        #Process alternative links, if any
        foreach ($outputArray['externalLinks'] as $type => $link) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => $type, 'href' => $link['url']];
        }
        #Set language
        $this->language = $outputArray['language'];
        #Get stuff for thread's editing
        if (
            ($outputArray['owned'] && in_array('edit_own_threads', $_SESSION['permissions'], true)) ||
            (!$outputArray['owned'] && in_array('edit_others_threads', $_SESSION['permissions'], true))
        ) {
            $outputArray['thread_languages'] = \Simbiat\Website\Talks\Thread::getLanguages();
            $outputArray['thread_link_types'] = \Simbiat\Website\Talks\Thread::getAltLinkTypes();
        }
        return $outputArray;
    }
}
