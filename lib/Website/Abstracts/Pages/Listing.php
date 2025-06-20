<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Config;
use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Pages\Search;

class Listing extends Search
{
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $pageWord = 'page';
    #Service name for breadcrumbs
    protected string $serviceName = 'listing';
    
    #Generation of the page data
    protected function generate(array $path): array
    {
        #Check if types are set
        $this->typesCheck();
        if (empty($path[0])) {
            return ['http_error' => 400, 'reason' => 'No endpoint provided'];
        }
        $this->subServiceName = $path[0];
        #Sanitize search value
        if (!$this->sanitize($_GET['search'] ?? '')) {
            return ['http_error' => 400, 'reason' => 'Bad search term'];
        }
        #Set page number
        $page = intval($_GET['page'] ?? 1);
        #Get search results
        $outputArray = [];
        $outputArray['numbered'] = $this->types[$this->subServiceName]['numbered'] ?? false;
        $listingType = (new $this->types[$this->subServiceName]['class']);
        $outputArray['search_result'] = $listingType->listEntities($page, $this->search_for);
        #If int is returned, we have a bad page
        if (is_int($outputArray['search_result'])) {
            #Redirect
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/'.$this->serviceName . '/' . $this->subServiceName . '/' . (!empty($this->search_for) ? '?search='.rawurlencode($this->search_for).'&page='.$outputArray['search_result'] : '?page='.$outputArray['search_result']), false);
            return [];
        }
        #Get the freshest date
        $date = $this->getDate($outputArray['search_result']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        #Generate pagination data
        $outputArray['pagination'] = ['current' => $page, 'total' => $outputArray['search_result']['pages'], 'prefix' => '?'.(empty($this->search_for) ? '' : 'search='.rawurlencode($this->search_for).'&').'page=', 'per' => $listingType->listItems];
        if (!empty($this->search_for)) {
            #Update breadcrumbs
            $this->attachCrumb('?search=' . rawurlencode($this->search_for), sprintf($this->shortTitle, $this->search_for));
            $this->breadCrumb[] = ['href' => '/'.$this->serviceName.'/' . $this->subServiceName . '/?search=' . rawurlencode($this->search_for), 'name' => $this->types[$this->subServiceName]['name']];
            if ($page > 1) {
                $this->attachCrumb('page=' . $page, mb_ucfirst($this->pageWord, 'UTF-8').' '.$page, true);
            }
            #Set search value, if available
            $outputArray['search_value'] = $this->search_for;
            #Set titles
            $this->h1 = $this->title = sprintf($this->shortTitle, $this->search_for).', '.$this->pageWord.' '.$page;
            $this->ogdesc = sprintf($this->fullTitle, $this->search_for).', '.$this->pageWord.' '.$page;
        } else {
            #Update breadcrumbs
            $this->breadCrumb = [['href' => '/' .$this->serviceName. '/' . $this->subServiceName, 'name' => $this->types[$this->subServiceName]['name']]];
            if ($page > 1) {
                $this->attachCrumb('/?page=' . $page, mb_ucfirst($this->pageWord, 'UTF-8').' ' . $page);
            }
            #Set titles
            $this->h1 = $this->title = $this->ogdesc = $this->types[$this->subServiceName]['name'].', '.$this->pageWord.' '.$page;
        }
        #Merge with extra fields and return the result
        return array_merge($outputArray, $this->extras());
    }

    #Get date from results
    protected final function getDate(array $results): int|string
    {
        #Prepare array of dates
        $dates = array_column($results['entities'], 'updated');
        #Return max value if dates array is not empty or 0 otherwise
        if (empty($dates)) {
            return 0;
        } else {
            return max($dates);
        }
    }
}
