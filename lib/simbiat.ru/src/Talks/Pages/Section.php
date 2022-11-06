<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Common;
use Simbiat\HTTP20\Headers;

class Section extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/talks/sections/', 'name'=>'Sections']
    ];
    #Sub service name
    protected string $subServiceName = 'section';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks: forums, blogs and other ways of communication';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? 'top';
        if ($id !== 'top' && intval($id) < 1) {
            #Redirect to top page
            Headers::redirect(Common::$baseUrl . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/talks/sections/', false);
        }
        $outputArray = (new \Simbiat\Talks\Entities\Section($id))->getArray();
        if (empty($outputArray['id'])) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($id !== 'top' && $outputArray['private'] && !in_array(1, $_SESSION['groups']) && $outputArray['createdBy'] !== $_SESSION['userid']) {
            return ['http_error' => 403, 'reason' => 'This is a private section'];
        }
        #Generate pagination data
        $page = intval($_GET['page'] ?? 1);
        $outputArray['pagination'] = ['current' => $page, 'total' => max($outputArray['threads']['pages'] ?? 1, $outputArray['children']['pages'] ?? 1), 'prefix' => '?page='];
        if ($outputArray['pagination']['current'] > $outputArray['pagination']['total'] && $outputArray['pagination']['total'] !== 0) {
            #Redirect to last page
            Headers::redirect(Common::$baseUrl . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/talks/sections/'.($id === 'top' ? '' : $id).'?page='.$outputArray['pagination']['total'], false);
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
        #Add sectionid, to avoid ambiguity on Twig level
        $outputArray['sectionid'] = $outputArray['id'];
        if ($outputArray['id'] !== 'top') {
            #Continue breadcrumbs
            #Add parents if we have any
            foreach ($outputArray['parents'] as $parent) {
                $this->breadCrumb[] = ['href' => '/talks/sections/'.$parent['sectionid'], 'name' => $parent['name']];
            }
            #Add current section
            $this->breadCrumb[] = ['href' => '/talks/sections/'.$outputArray['id'], 'name' => $outputArray['name']];
            #Add page, if there is one
            if ($page > 1) {
                $this->attachCrumb('?page=' . $page, 'Page ' . $page);
            }
            #Update title, h1 and ogdesc
            $this->title = $this->h1 = $outputArray['name'].($page > 1 ? ', Page '.$page : '');
            $this->ogdesc = $outputArray['description'] ?? $outputArray['type'].' with the name of `'.$outputArray['name'].'`';
        }
        return $outputArray;
    }
}
