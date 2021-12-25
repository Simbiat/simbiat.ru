<?php
declare(strict_types=1);
namespace Simbiat\Abstracts\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;
use Simbiat\HTTP20\HTML;

class Listing extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 1440;
    #Linking types to classes
    protected array $types = [];
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^a-zA-Z0-9 _\'\-,!%]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Search for `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Search for `%s`';
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $pageWord = 'page';

    #Generation of the page data
    protected final function generate(array $path): array
    {
        #Set subservice name based on URL
        $this->subServiceName = $path[0];
        #Set page number
        $page = intval($_GET['page'] ?? 1);
        #Sanitize search value
        $decodedSearch = preg_replace($this->regexSearch, '', rawurldecode($path[0] ?? ''));
        #Ensure colon is removed, since it breaks binding. Using regex, in case some other characters will be required forceful removal in future
        $decodedSearch = preg_replace('/:/i', '', $decodedSearch);
        #Check if the new value is just the set of operators and if it is - consider bad request
        if (preg_match('/^[+\-<>~()"*]+$/i', $decodedSearch)) {
            return ['http_error' => 400];
        }
        #If value is empty, ensure it's an empty string
        if (empty($decodedSearch)) {
            $decodedSearch = '';
        }
        if (!isset($this->types[$this->subServiceName]['class']) || !method_exists($this->types[$this->subServiceName]['class'], 'listEntities')) {
            throw new \RuntimeException('`listEntities` method does not exist in `'.$this->types[$this->subServiceName].'` class');
        }
        #Get entities
        $outputArray['listOfEntities'] = (new $this->types[$this->subServiceName]['class'])->listEntities($page, $decodedSearch);
        #If int is returned, we have a bad page
        if (is_int($outputArray['listOfEntities'])) {
            #Redirect
            if (!HomePage::$staleReturn) {
                HomePage::$headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . $this->breadCrumb[array_key_last($this->breadCrumb)]['href'] . '/' . $this->subServiceName . '/' . ($path[1] ?? ''), false, true, false);
            }
        } else {
            #Get the freshest date
            $dates = array_column($outputArray['listOfEntities']['entities'], 'updated');
            #Attempt to exit a bit earlier with Last Modified header
            if (!empty($dates)) {
                $this->lastModified(max($dates));
            }
            #Generate pagination
            $html = (new HTML);
            $outputArray['pagination_top'] = $html->pagination($page, $outputArray['listOfEntities']['pages'], prefix: '?page=');
            $outputArray['pagination_bottom'] = $html->pagination($page, $outputArray['listOfEntities']['pages'], prefix: '?page=');
            #Get page address from default breadcrumb
            $address = $this->breadCrumb[array_key_last($this->breadCrumb)]['href'];
            if (!empty($decodedSearch)) {
                #Set search value, if available
                $outputArray['searchvalue'] = $decodedSearch;
                #Update breadcrumbs
                $this->breadCrumb = [['href' => $address . '/search/' . $path[1], 'name' => sprintf($this->shortTitle, $decodedSearch)]];
                $this->breadCrumb[] = ['href' => $address . '/' . $this->subServiceName . '/' . $path[1], 'name' => $this->types[$this->subServiceName]['name']];
                if ($page > 1) {
                    $this->breadCrumb[] = ['href' => $address . '/' . $this->subServiceName . '/' . $path[1] . '/?page=' . $page, 'name' => ucfirst($this->pageWord).' ' . $page];
                }
                #Set titles
                $this->h1 = $this->title = sprintf($this->shortTitle, $decodedSearch).', '.$this->pageWord.' '.$page;
                $this->ogdesc = sprintf($this->fullTitle, $decodedSearch).', '.$this->pageWord.' '.$page;
            } else {
                #Update breadcrumbs
                $this->breadCrumb = [['href' => $address . '/' . $this->subServiceName, 'name' => $this->types[$this->subServiceName]['name']]];
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

    #Add any extra fields, if required by overriding this function
    protected function extras(): array
    {
        return [];
    }
}
