<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;
use Simbiat\http20\Headers;
use Simbiat\Website\Sanitization;

use function sprintf;

/**
 * Search page
 */
class Search extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 1440;
    #Linking types to classes
    protected array $types = [];
    #Items to display per page for search results per type
    protected int $searchItems = 15;
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^a-zA-Z\d _\'\-,!%]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Search for `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Search for `%s`';
    #Search value
    protected string $searchFor = '';
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        #Check if types are set
        $this->typesCheck();
        #Check if we got some old link (before GET implementation)
        if (empty($_GET['search']) && !empty($path[0])) {
            #Redirect to proper version using GET value
            Headers::redirect(preg_replace('/(.*)(?>\/([^\/]+)\/?$)/u', '$1/?search=$2', Config::$canonical));
        }
        #Sanitize search value
        if (!$this->sanitize($_GET['search'] ?? '')) {
            return ['http_error' => 400, 'reason' => 'Bad search term'];
        }
        #Get search results
        $outputArray = [];
        foreach ($this->types as $type => $class) {
            $outputArray['searchResult'][$type] = new $class['class']()->Search($this->searchFor, $this->searchItems);
        }
        #Get the freshest date
        $date = $this->getDate($outputArray['searchResult']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        if (!empty($this->searchFor)) {
            #Continue breadcrumbs
            $this->attachCrumb('?search='.rawurlencode($this->searchFor), sprintf($this->shortTitle, $this->searchFor));
            #Set search value, if available
            $outputArray['searchValue'] = $this->searchFor;
            #Set titles
            $this->title = sprintf($this->shortTitle, $this->searchFor);
            $this->h1 = $this->title;
            $this->ogdesc = sprintf($this->fullTitle, $this->searchFor);
        }
        #Merge with extra fields and return the result
        return array_merge($outputArray, $this->extras());
    }
    
    /**
     * Check if types are properly set
     * @return void
     */
    final protected function typesCheck(): void
    {
        #Bad if array is empty
        if (empty($this->types)) {
            throw new \RuntimeException('Search types are not set');
        }
        #Check that classes are available
        foreach ($this->types as $type) {
            if (!is_subclass_of($type['class'], \Simbiat\Website\Abstracts\Search::class)) {
                throw new \RuntimeException('`'.$type['class'].'` class does not extend `\Simbiat\Website\Abstracts\Search`');
            }
        }
    }
    
    /**
     * Get date from results
     * @param array $results
     *
     * @return int|string
     */
    protected function getDate(array $results): int|string
    {
        #Prepare array of dates
        $dates = [];
        foreach ($results as $type) {
            $dates = array_merge($dates, array_column($type['results'], 'updated'));
        }
        #Return max value if dates array is not empty or 0 otherwise
        if (empty($dates)) {
            return 0;
        }
        return max($dates);
    }
    
    /**
     * @param string $term
     *
     * @return bool
     */
    final protected function sanitize(string $term): bool
    {
        if (empty($term)) {
            return true;
        }
        $term = Sanitization::removeNonPrintable($term, true);
        $decodedSearch = preg_replace($this->regexSearch, '', $term);
        #Ensure colon is removed, since it breaks binding. Using regex, in case some other characters will be required forceful removal in future
        $decodedSearch = preg_replace('/:/', '', $decodedSearch);
        #Check if the new value is just the set of operators and if it is - consider bad request
        if (preg_match('/^[+\-<>~()"*]+$/', $decodedSearch)) {
            return false;
        }
        #If value is empty, ensure it's an empty string
        if (!empty($decodedSearch)) {
            $this->searchFor = $decodedSearch;
        }
        return true;
    }
    
    /**
     * Add any extra fields, if required by overriding this function
     * @return array
     */
    protected function extras(): array
    {
        return [];
    }
}
