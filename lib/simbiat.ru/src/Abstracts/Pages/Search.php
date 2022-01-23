<?php
declare(strict_types=1);
namespace Simbiat\Abstracts\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;
use Simbiat\HTTP20\HTML;

class Search extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 1440;
    #Linking types to classes
    protected array $types = [];
    #Items to display per page for search results per type
    protected int $searchItems = 15;
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^a-zA-Z0-9 _\'\-,!%]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Search for `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Search for `%s`';
    #Search value
    protected string $searchFor = '';
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $pageWord = 'page';
    #Flag to identify that we are listing full(er) results
    protected bool $list = false;

    #Generation of the page data
    protected function generate(array $path): array
    {
        #Check if types are set
        $this->typesCheck();
        #Sanitize search value
        if ($this->list) {
            $this->subServiceName = $path[0];
            if (!$this->sanitize($path[1] ?? '')) {
                return ['http_error' => 400];
            }
        } else {
            if (!$this->sanitize($path[0] ?? '')) {
                return ['http_error' => 400];
            }
        }
        #Set page number
        $page = intval($_GET['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }
        #Get search results
        $outputArray = [];
        if ($this->list) {
            $outputArray['searchresult'] = (new $this->types[$this->subServiceName]['class'])->listEntities($page, $this->searchFor);
            #If int is returned, we have a bad page
            if (is_int($outputArray['searchresult'])) {
                #Redirect
                if (!HomePage::$staleReturn) {
                    HomePage::$headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . $this->breadCrumb[array_key_last($this->breadCrumb)]['href'] . '/' . $this->subServiceName . '/' . rawurlencode($this->searchFor), false, true, false);
                }
            }
        } else {
            foreach ($this->types as $type => $class) {
                $outputArray['searchresult'][$type] = (new $class['class'])->Search($this->searchFor, $this->searchItems);
            }
        }
        #Get the freshest date
        $date = $this->getDate($outputArray['searchresult']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        if ($this->list) {
            #Generate pagination data
            $outputArray['pagination'] = ['current' => $page, 'total' => $outputArray['searchresult']['pages'], 'prefix' => '?page='];
        }
        if (!empty($this->searchFor)) {
            if ($this->list) {
                #Get page address from default breadcrumb
                $address = $this->breadCrumb[array_key_last($this->breadCrumb)]['href'];
                #Update breadcrumbs
                $this->breadCrumb = [['href' => $address . '/search/' . rawurlencode($this->searchFor) . '/', 'name' => sprintf($this->shortTitle, $this->searchFor)]];
                $this->breadCrumb[] = ['href' => $address . '/' . $this->subServiceName . '/' . rawurlencode($this->searchFor) . '/', 'name' => $this->types[$this->subServiceName]['name']];
                if ($page > 1) {
                    $this->breadCrumb[] = ['href' => $address . '/' . $this->subServiceName . '/' . rawurlencode($this->searchFor) . '/?page=' . $page, 'name' => ucfirst($this->pageWord).' ' . $page];
                }
            } else {
                #Continue breadcrumbs
                $this->breadCrumb[] = ['href' => $this->breadCrumb[array_key_last($this->breadCrumb)]['href'] . '/' . rawurlencode($this->searchFor), 'name' => $this->searchFor];

            }
            #Set search value, if available
            $outputArray['searchvalue'] = $this->searchFor;
            #Set titles
            $this->h1 = $this->title = sprintf($this->shortTitle, $this->searchFor).($this->list ? ', '.$this->pageWord.' '.$page : '');
            $this->ogdesc = sprintf($this->fullTitle, $this->searchFor).($this->list ? ', '.$this->pageWord.' '.$page : '');
        } else {
            if ($this->list) {
                #Get page address from default breadcrumb
                $address = $this->breadCrumb[array_key_last($this->breadCrumb)]['href'];
                #Update breadcrumbs
                $this->breadCrumb = [['href' => $address . '/' . $this->subServiceName. '/', 'name' => $this->types[$this->subServiceName]['name']]];
                if ($page > 1) {
                    $this->breadCrumb[] = ['href' => $address . '/' . $this->subServiceName . '/?page=' . $page, 'name' => ucfirst($this->pageWord).' ' . $page];
                }
                #Set titles
                $this->h1 = $this->title = $this->ogdesc = $this->types[$this->subServiceName]['name'].', '.$this->pageWord.' '.$page;
            }
        }
        #Merge with extra fields and return the result
        return array_merge($outputArray, $this->extras());
    }

    #Check if types are properly set
    protected final function typesCheck(): void
    {
        #Bad if array is empty
        if (empty($this->types)) {
            throw new \RuntimeException('Search types are not set');
        }
        #Check that classes are available
        foreach ($this->types as $type) {
            if ($this->list) {
                if (!method_exists($type['class'], 'listEntities')) {
                    throw new \RuntimeException('`listEntities` method does not exist in `' . $type['class'] . '` class');
                }
            } else {
                if (!method_exists($type['class'], 'Search')) {
                    throw new \RuntimeException('`Search` method does not exist in `' . $type['class'] . '` class');
                }
            }
        }
    }

    #Get date from results
    protected final function getDate(array $results): int|string
    {
        #Prepare array of dates
        $dates = [];
        if ($this->list) {
            $dates = array_column($results['entities'], 'updated');
        } else {
            foreach ($results as $type) {
                $dates = array_merge($dates, array_column($type['results'], 'updated'));
            }
        }
        #Return max value if dates array is not empty or 0 otherwise
        if (empty($dates)) {
            return 0;
        } else {
            return max($dates);
        }
    }

    protected final function sanitize(string $term): bool
    {
        if (empty($term)) {
            return true;
        }
        $decodedSearch = preg_replace($this->regexSearch, '', $term);
        #Ensure colon is removed, since it breaks binding. Using regex, in case some other characters will be required forceful removal in future
        $decodedSearch = preg_replace('/:/i', '', $decodedSearch);
        #Check if the new value is just the set of operators and if it is - consider bad request
        if (preg_match('/^[+\-<>~()"*]+$/i', $decodedSearch)) {
            return false;
        }
        #If value is empty, ensure it's an empty string
        if (empty($decodedSearch)) {
            return false;
        }
        $this->searchFor = $decodedSearch;
        return true;
    }

    #Add any extra fields, if required by overriding this function
    protected function extras(): array
    {
        return [];
    }
}
