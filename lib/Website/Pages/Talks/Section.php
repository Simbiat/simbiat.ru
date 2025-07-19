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
    protected array $breadcrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections']
    ];
    #Sub service name
    protected string $subservice_name = 'section';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Talks: forums, blogs and other ways of communication';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_posts'];
    #Flag to indicate editor mode
    protected bool $edit_mode = false;
    #Link to JS module for preload
    protected string $js_module = 'talks/sections';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Allow `blob:`
        @\header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' blob:; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        #Sanitize ID
        $id = $path[0] ?? 'top';
        if ($id !== 'top' && (int)$id < 1) {
            #Redirect to top page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/sections/', false);
        }
        $output_array = new \Simbiat\Website\Talks\Section($id)->getArray();
        if (empty($output_array['id'])) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($output_array['private'] && $output_array['author'] !== $_SESSION['user_id'] && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'This section is private and you lack `view_private` permission'];
        }
        #Check if scheduled
        if ($output_array['created'] >= \time() && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Generate pagination data
        $page = (int)($_GET['page'] ?? 1);
        $output_array['pagination'] = ['current' => $page, 'total' => \max($output_array['threads']['pages'] ?? 1, $output_array['children']['pages'] ?? 1), 'prefix' => '?page='];
        if ($output_array['pagination']['current'] > $output_array['pagination']['total'] && $output_array['pagination']['total'] !== 0) {
            #Redirect to last page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/sections/'.($id === 'top' ? '' : $id).'?page='.$output_array['pagination']['total'], false);
            return [];
        }
        #Collect times
        $times = [];
        #Add time for the current section
        if (!empty($output_array['updated'])) {
            $times[] = $output_array['updated'];
        }
        #Add children times
        if (!empty($output_array['children']['entities'])) {
            $times = \array_merge($times, \array_column($output_array['children']['entities'], 'updated'));
        }
        #Add threads times
        if (!empty($output_array['threads']['entities'])) {
            $times = \array_merge($times, \array_column($output_array['threads']['entities'], 'updated'));
        }
        #Try to exit early based on modification date
        if (!empty($times)) {
            $this->lastModified(\max($times) ?? 0);
        }
        #Add section_id, to avoid ambiguity on Twig level
        $output_array['section_id'] = $output_array['id'];
        if ($output_array['id'] !== 'top') {
            #Continue breadcrumbs
            #Add parents if we have any
            foreach ($output_array['parents'] as $parent) {
                $this->breadcrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
            }
            #Add the current section
            $this->breadcrumb[] = ['href' => '/talks/sections/'.$output_array['id'], 'name' => $output_array['name']];
            #Add a page if there is one
            if ($page > 1) {
                $this->attachCrumb('?page='.$page, 'Page '.$page);
            }
            #Update title, h1 and og_desc
            $this->h1 = $output_array['name'].($page > 1 ? ', Page '.$page : '');
            $this->title = $this->h1;
            $this->og_desc = $output_array['description'] ?? ($output_array['type'].' with the name of `'.$output_array['name'].'`');
        }
        #Set flag indicating that we are in edit mode
        $output_array['edit_mode'] = $this->edit_mode;
        #Get section types
        if ($output_array['owned'] || in_array('add_sections', $_SESSION['permissions'], true)) {
            $output_array['section_types'] = \Simbiat\Website\Talks\Section::getSectionTypes($output_array['inherited_type']);
        }
        #Get stuff for threads
        if ($output_array['owned'] || in_array('can_post', $_SESSION['permissions'], true)) {
            $output_array['thread_languages'] = \Simbiat\Website\Talks\Thread::getLanguages();
            $output_array['thread_link_types'] = \Simbiat\Website\Talks\Thread::getAltLinkTypes();
        }
        if ($this->edit_mode) {
            #Add edit mode to breadcrumb
            $this->breadcrumb[] = ['href' => '/talks/edit/sections/'.($id !== 'top' ? $id : ''), 'name' => 'Edit mode'];
            $this->h1 = 'Editing `'.($id !== 'top' ? $output_array['name'] : 'Root section').'`'.($page > 1 ? ', Page '.$page : '');
            $this->title = $this->h1;
        }
        return $output_array;
    }
}
