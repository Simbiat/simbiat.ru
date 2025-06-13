<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

use function in_array;

class Section extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections']
    ];
    #Sub service name
    protected string $subServiceName = 'section';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks: forums, blogs and other ways of communication';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['view_posts'];
    #Flag to indicate editor mode
    protected bool $editMode = false;
    #Link to JS module for preload
    protected string $jsModule = 'talks/sections';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Allow `blob:`
        @header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' blob:; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        #Sanitize ID
        $id = $path[0] ?? 'top';
        if ($id !== 'top' && (int)$id < 1) {
            #Redirect to top page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/sections/', false);
        }
        $outputArray = new \Simbiat\Website\Talks\Section($id)->getArray();
        if (empty($outputArray['id'])) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($outputArray['private'] && $outputArray['author'] !== $_SESSION['user_id'] && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'This section is private and you lack `view_private` permission'];
        }
        #Check if scheduled
        if ($outputArray['created'] >= time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Generate pagination data
        $page = (int)($_GET['page'] ?? 1);
        $outputArray['pagination'] = ['current' => $page, 'total' => max($outputArray['threads']['pages'] ?? 1, $outputArray['children']['pages'] ?? 1), 'prefix' => '?page='];
        if ($outputArray['pagination']['current'] > $outputArray['pagination']['total'] && $outputArray['pagination']['total'] !== 0) {
            #Redirect to last page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/sections/'.($id === 'top' ? '' : $id).'?page='.$outputArray['pagination']['total'], false);
            return [];
        }
        #Collect times
        $times = [];
        #Add time for the current section
        if (!empty($outputArray['updated'])) {
            $times[] = $outputArray['updated'];
        }
        #Add children times
        if (!empty($outputArray['children']['entities'])) {
            $times = array_merge($times, array_column($outputArray['children']['entities'], 'updated'));
        }
        #Add threads times
        if (!empty($outputArray['threads']['entities'])) {
            $times = array_merge($times, array_column($outputArray['threads']['entities'], 'updated'));
        }
        #Try to exit early based on modification date
        if (!empty($times)) {
            $this->lastModified(max($times) ?? 0);
        }
        #Add section_id, to avoid ambiguity on Twig level
        $outputArray['section_id'] = $outputArray['id'];
        if ($outputArray['id'] !== 'top') {
            #Continue breadcrumbs
            #Add parents if we have any
            foreach ($outputArray['parents'] as $parent) {
                $this->breadCrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
            }
            #Add the current section
            $this->breadCrumb[] = ['href' => '/talks/sections/'.$outputArray['id'], 'name' => $outputArray['name']];
            #Add a page if there is one
            if ($page > 1) {
                $this->attachCrumb('?page='.$page, 'Page '.$page);
            }
            #Update title, h1 and ogdesc
            $this->h1 = $outputArray['name'].($page > 1 ? ', Page '.$page : '');
            $this->title = $this->h1;
            $this->ogdesc = $outputArray['description'] ?? ($outputArray['type'].' with the name of `'.$outputArray['name'].'`');
        }
        #Set flag indicating that we are in edit mode
        $outputArray['editMode'] = $this->editMode;
        #Get section types
        if ($outputArray['owned'] || in_array('add_sections', $_SESSION['permissions'], true)) {
            $outputArray['section_types'] = \Simbiat\Website\Talks\Section::getSectionTypes($outputArray['inheritedType']);
        }
        #Get stuff for threads
        if ($outputArray['owned'] || in_array('can_post', $_SESSION['permissions'], true)) {
            $outputArray['thread_languages'] = \Simbiat\Website\Talks\Thread::getLanguages();
            $outputArray['thread_link_types'] = \Simbiat\Website\Talks\Thread::getAltLinkTypes();
        }
        if ($this->editMode) {
            #Add edit mode to breadcrumb
            $this->breadCrumb[] = ['href' => '/talks/edit/sections/'.($id !== 'top' ? $id : ''), 'name' => 'Edit mode'];
            $this->h1 = 'Editing `'.($id !== 'top' ? $outputArray['name'] : 'Root section').'`'.($page > 1 ? ', Page '.$page : '');
            $this->title = $this->h1;
        }
        return $outputArray;
    }
}
