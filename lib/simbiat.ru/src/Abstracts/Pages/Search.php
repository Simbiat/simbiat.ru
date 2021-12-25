<?php
declare(strict_types=1);
namespace Simbiat\Abstracts\Pages;

use Simbiat\Abstracts\Page;

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

    #Generation of the page data
    protected final function generate(array $path): array
    {
        #Check if types are set
        $this->typesCheck();
        #Sanitize search value
        $decodedSearch = preg_replace($this->regexSearch, '', rawurldecode($path[0] ?? ''));
        #Check if the new value is just the set of operators and if it is - consider bad request
        if (preg_match('/[+\-<>~()"*]+/u', $decodedSearch)) {
            return ['http_error' => 400];
        }
        #If value is empty, ensure it's an empty string
        if (empty($decodedSearch)) {
            $decodedSearch = '';
        }
        #Get search results
        $outputArray = [];
        foreach ($this->types as $type=>$class) {
            $outputArray['searchresult'][$type] = (new $class)->Search($decodedSearch, $this->searchItems);
        }
        #Get the freshest date
        $date = $this->getDate($outputArray['searchresult']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        if (!empty($decodedSearch)) {
            #Continue breadcrumbs
            $this->breadCrumb[] = ['href' => $this->breadCrumb[array_key_last($this->breadCrumb)]['href'] . '/' . $path[0], 'name' => '' . $decodedSearch.''];
            $outputArray['searchvalue'] = $decodedSearch;
            #Set titles
            $this->h1 = $this->title = sprintf($this->shortTitle, $decodedSearch);
            $this->ogdesc = sprintf($this->fullTitle, $decodedSearch);
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
            if (!method_exists($type, 'Search')) {
                throw new \RuntimeException('`Search` method does not exist in `'.$type.'` class');
            }
        }
    }

    #Get date from results
    protected final function getDate(array $results): int|string
    {
        #Prepare array of dates
        $dates = [];
        foreach ($results as $type) {
            $dates = array_merge($dates, array_column($type['results'], 'updated'));
        }
        #Return max value if dates array is not empty or 0 otherwise
        if (empty($dates)) {
            return 0;
        } else {
            return max($dates);
        }
    }

    #Add any extra fields, if required by overriding this function
    protected function extras(): array
    {
        return [];
    }
}
