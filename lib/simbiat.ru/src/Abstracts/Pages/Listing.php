<?php
declare(strict_types=1);
namespace Simbiat\Abstracts\Pages;

use Simbiat\Config\Common;
use Simbiat\HomePage;
use Simbiat\HTTP20\Headers;

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
            return ['http_error' => 400];
        }
        #Set page number
        $page = intval($_GET['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }
        #Get search results
        $outputArray = [];
        $outputArray['searchResult'] = (new $this->types[$this->subServiceName]['class'])->listEntities($page, $this->searchFor);
        #If int is returned, we have a bad page
        if (is_int($outputArray['searchResult'])) {
            #Redirect
            if (!HomePage::$staleReturn) {
                Headers::redirect(Common::$baseUrl . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . $this->getLastCrumb() . '/' . $this->subServiceName . '/' . rawurlencode($this->searchFor), false);
            }
        }
        #Get the freshest date
        $date = $this->getDate($outputArray['searchResult']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        #Generate pagination data
        $outputArray['pagination'] = ['current' => $page, 'total' => $outputArray['searchResult']['pages'], 'prefix' => '?search='.rawurlencode($this->searchFor).'&page='];
        if (!empty($this->searchFor)) {
            #Get page address from default breadcrumb
            #Update breadcrumbs
            #$this->breadCrumb = [['href' => $address . '/search/' . rawurlencode($this->searchFor) . '/', 'name' => sprintf($this->shortTitle, $this->searchFor)]];
            $this->attachCrumb('?search=' . rawurlencode($this->searchFor), sprintf($this->shortTitle, $this->searchFor));
            $this->breadCrumb[] = ['href' => '/'.$this->serviceName.'/' . $this->subServiceName . '/?search=' . rawurlencode($this->searchFor), 'name' => $this->types[$this->subServiceName]['name']];
            if ($page > 1) {
                $this->attachCrumb('page=' . $page, ucfirst($this->pageWord).' ' . $page, true);
            }
            #Set search value, if available
            $outputArray['searchValue'] = $this->searchFor;
            #Set titles
            $this->h1 = $this->title = sprintf($this->shortTitle, $this->searchFor).', '.$this->pageWord.' '.$page;
            $this->ogdesc = sprintf($this->fullTitle, $this->searchFor).', '.$this->pageWord.' '.$page;
        } else {
            #Get page address from default breadcrumb
            #Update breadcrumbs
            $this->breadCrumb = [['href' => '/' .$this->serviceName. '/' . $this->subServiceName, 'name' => $this->types[$this->subServiceName]['name']]];
            if ($page > 1) {
                $this->attachCrumb('/?page=' . $page, ucfirst($this->pageWord).' ' . $page);
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
