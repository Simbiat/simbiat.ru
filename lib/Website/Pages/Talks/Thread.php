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
    protected array $breadcrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections']
    ];
    #Sub service name
    protected string $subservice_name = 'thread';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Talks';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_posts'];
    #Link to JS module for preload
    protected string $js_module = 'talks/threads';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Allow `blob:`
        @\header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' blob:; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || (int)$id < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $output_array = new \Simbiat\Website\Talks\Thread($id)->getArray();
        if (empty($output_array['id'])) {
            return ['http_error' => 404, 'reason' => 'Thread does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($output_array['private']) {
            if ($output_array['author'] === Config::USER_IDS['Unknown user'] && $output_array['author'] === $_SESSION['user_id']) {
                if ($output_array['type'] === 'Support') {
                    if (($output_array['access_token'] === null || $output_array['access_token'] === '' || $output_array['access_token'] !== ($_GET['access_token'] ?? ''))) {
                        #Return same error to limit potential of brute-forcing a token
                        return ['http_error' => 403, 'reason' => 'This thread is private and you lack `view_private` permission'];
                    }
                    #If token is valid - temporary give permission to allow posting
                    $_SESSION['permissions'][] = 'can_post';
                }
            } elseif ($output_array['author'] !== $_SESSION['user_id'] && !in_array('view_private', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'This thread is private and you lack `view_private` permission'];
            }
        }
        #Check if scheduled
        if ($output_array['created'] >= \time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Thread does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Collect times
        $times = [];
        #Add time for the current section
        if (!empty($output_array['updated'])) {
            $times[] = $output_array['updated'];
        }
        #Add posts times
        if (!empty($output_array['posts']['entities'])) {
            $times = \array_merge($times, \array_column($output_array['posts']['entities'], 'updated'));
        }
        #Try to exit early based on modification date
        if (!empty($times)) {
            $this->lastModified(\max($times) ?? 0);
        }
        #Generate pagination data
        $page = (int)($_GET['page'] ?? 1);
        $output_array['pagination'] = ['current' => $page, 'total' => $output_array['posts']['pages'] ?? 1, 'prefix' => '?page='];
        if ($output_array['pagination']['current'] > $output_array['pagination']['total'] && $output_array['pagination']['total'] !== 0) {
            #Redirect to last page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/threads/'.$id.'?page='.$output_array['pagination']['total'], false);
            return [];
        }
        #Changelogs have Unix timestamp for names, need to convert those to the desired format
        /** @noinspection DuplicatedCode */
        if ($output_array['type'] === 'Changelog' && \is_numeric($output_array['name'])) {
            $output_array['name'] = \date('Y.m.d', (int)$output_array['name']);
        }
        #Add parents to breadcrumbs if we have any
        foreach ($output_array['parents'] as $parent) {
            $this->breadcrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadcrumb[] = ['href' => '/talks/threads/'.$output_array['id'], 'name' => $output_array['name']];
        #Add a page if there is one
        if ($page > 1) {
            $this->attachCrumb('?page='.$page, 'Page '.$page);
        }
        #Update title, h1 and og_desc
        $this->title = '`'.$output_array['name'].'` thread';
        $this->h1 = $output_array['name'];
        if (!empty($output_array['posts']['entities'])) {
            $this->setOgDesc($output_array['posts']['entities'][0]['text']);
        } else {
            $this->og_desc = $output_array['name'];
        }
        #Set og_image
        if (!empty($output_array['og_image'])) {
            $output_array = \array_merge($output_array, Images::ogImage($output_array['og_image']));
            if (!empty($output_array['og_image'])) {
                $this->og_image = $output_array['og_image'];
                #Add to H2Push
                $this->h2_push_extra[] = $this->og_image;
            }
        }
        #Set ogtype
        $output_array['ogtype'] = 'article';
        #Add article open graph tags
        /** @noinspection DuplicatedCode */
        $output_array['ogextra'] =
            '<meta property="article:published_time" content="'.\date('c', $output_array['created']).'" />
            <meta property="article:modified_time" content="'.\date('c', $output_array['updated']).'" />'.
            ($output_array['author'] === 1 ? '' : '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$output_array['author'].'" />').
            ($output_array['editor'] !== 1 && $output_array['editor'] !== $output_array['author'] ? '<meta property="article:author" content="'.Config::$base_url.'/talks/user/'.$output_array['author'].'" />' : '').
            '<meta property="article:section" content="'.$output_array['parents'][\array_key_last($output_array['parents'])]['name'].'" />';
        foreach ($output_array['tags'] as $tag) {
            $output_array['ogextra'] .= '<meta property="article:tag" content="'.$tag.'" />';
        }
        #Process alternative links, if any
        foreach ($output_array['external_links'] as $type => $link) {
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => $type, 'href' => $link['url']];
        }
        #Set language
        $this->language = $output_array['language'];
        #Get stuff for thread's editing
        if (
            ($output_array['owned'] && in_array('edit_own_threads', $_SESSION['permissions'], true)) ||
            (!$output_array['owned'] && in_array('edit_others_threads', $_SESSION['permissions'], true))
        ) {
            $output_array['thread_languages'] = \Simbiat\Website\Talks\Thread::getLanguages();
            $output_array['thread_link_types'] = \Simbiat\Website\Talks\Thread::getAltLinkTypes();
        }
        #Add access token
        if ($output_array['author'] === Config::USER_IDS['Unknown user'] && $_SESSION['user_id'] === Config::USER_IDS['Unknown user']) {
            $output_array['get_access_token'] = $_GET['access_token'] ?? null;
        }
        return $output_array;
    }
}
