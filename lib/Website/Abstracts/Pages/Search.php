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
    protected int $cache_age = 1440;
    #Linking types to classes
    protected array $types = [];
    #Items to display per page for search results per type
    protected int $search_items = 15;
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regex_search = '/[^\p{L}\p{N} _\'\-,!%]/iu';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $short_title = 'Search for `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $full_title = 'Search for `%s`';
    #Search value
    protected string $search_for = '';
    
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
            #Redirect to a proper version using GET value
            Headers::redirect(preg_replace('/(.*)(?>\/([^\/]+)\/?$)/u', '$1/?search=$2', Config::$canonical));
        }
        #Sanitize search value
        if (!$this->sanitize($_GET['search'] ?? '')) {
            return ['http_error' => 400, 'reason' => 'Bad search term'];
        }
        #Get search results
        $output_array = [];
        foreach ($this->types as $type => $class) {
            $output_array['search_result'][$type] = new $class['class']()->Search($this->search_for, $this->search_items);
        }
        #Get the freshest date
        $date = $this->getDate($output_array['search_result']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        if (!empty($this->search_for)) {
            #Continue breadcrumbs
            $this->attachCrumb('?search='.rawurlencode($this->search_for), sprintf($this->short_title, $this->search_for));
            #Set search value, if available
            $output_array['search_value'] = $this->search_for;
            #Set titles
            $this->title = sprintf($this->short_title, $this->search_for);
            $this->h1 = $this->title;
            $this->og_desc = sprintf($this->full_title, $this->search_for);
        }
        #Merge with extra fields and return the result
        return array_merge($output_array, $this->extras());
    }
    
    /**
     * Check if types are properly set
     * @return void
     */
    final protected function typesCheck(): void
    {
        #Bad if the array is empty
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
        #Prepare the array of dates
        $dates = [];
        foreach ($results as $type) {
            $dates[] = \array_column($type['results'], 'updated');
        }
        $dates = \array_merge(...$dates);
        #Return max value if the array is not empty or 0 otherwise
        if (\count($dates) === 0) {
            return 0;
        }
        return \max($dates);
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
        $sanitized = Sanitization::removeNonPrintable($term, true);
        #Remove disallowed characters. Ensure colon is removed, since it breaks binding. Using regex, in case some other characters will be required forceful removal in the future
        $decoded_search = \preg_replace([$this->regex_search, '/:/'], '', $sanitized);
        #Check if the new value is just the set of operators and if it is - consider a bad request
        if (\preg_match('/^[+\-<>~()"*]+$/', $decoded_search)) {
            return false;
        }
        #If the value is empty, ensure it's an empty string
        if (!empty($decoded_search)) {
            $this->search_for = $decoded_search;
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
