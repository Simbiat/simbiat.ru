<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Config;
use Simbiat\http20\Headers;

use function sprintf, is_int;

class Listing extends Search
{
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $page_word = 'page';
    #Service name for breadcrumbs
    protected string $service_name = 'listing';
    
    #Generation of the page data
    protected function generate(array $path): array
    {
        #Check if types are set
        $this->typesCheck();
        if (empty($path[0])) {
            return ['http_error' => 400, 'reason' => 'No endpoint provided'];
        }
        $this->subservice_name = $path[0];
        #Sanitize search value
        if (!$this->sanitize($_GET['search'] ?? '')) {
            return ['http_error' => 400, 'reason' => 'Bad search term'];
        }
        #Set page number
        $page = (int)($_GET['page'] ?? 1);
        #Get search results
        $output_array = [];
        $output_array['numbered'] = $this->types[$this->subservice_name]['numbered'] ?? false;
        $listing_type = new $this->types[$this->subservice_name]['class']();
        $output_array['search_result'] = $listing_type->listEntities($page, $this->search_for);
        #If int is returned, we have a bad page
        if (is_int($output_array['search_result'])) {
            #Redirect
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.$this->service_name . '/' . $this->subservice_name . '/' . (!empty($this->search_for) ? '?search='.rawurlencode($this->search_for).'&page='.$output_array['search_result'] : '?page='.$output_array['search_result']), false);
            return [];
        }
        #Get the freshest date
        $date = $this->getDate($output_array['search_result']);
        #Attempt to exit a bit earlier with Last Modified header
        if (!empty($date)) {
            $this->lastModified($date);
        }
        #Generate pagination data
        $output_array['pagination'] = ['current' => $page, 'total' => $output_array['search_result']['pages'], 'prefix' => '?'.(empty($this->search_for) ? '' : 'search='.rawurlencode($this->search_for).'&').'page=', 'per' => $listing_type->list_items];
        if (!empty($this->search_for)) {
            #Update breadcrumbs
            $this->attachCrumb('?search='.rawurlencode($this->search_for), sprintf($this->short_title, $this->search_for));
            $this->breadcrumb[] = ['href' => '/'.$this->service_name.'/'.$this->subservice_name.'/?search='.rawurlencode($this->search_for), 'name' => $this->types[$this->subservice_name]['name']];
            if ($page > 1) {
                $this->attachCrumb('page=' . $page, mb_ucfirst($this->page_word, 'UTF-8').' '.$page, true);
            }
            #Set search value, if available
            $output_array['search_value'] = $this->search_for;
            #Set titles
            $this->title = sprintf($this->short_title, $this->search_for).', '.$this->page_word.' '.$page;
            $this->h1 = $this->title;
            $this->og_desc = sprintf($this->full_title, $this->search_for).', '.$this->page_word.' '.$page;
        } else {
            #Update breadcrumbs
            $this->breadcrumb = [['href' => '/' .$this->service_name. '/' . $this->subservice_name, 'name' => $this->types[$this->subservice_name]['name']]];
            if ($page > 1) {
                $this->attachCrumb('/?page=' . $page, mb_ucfirst($this->page_word, 'UTF-8').' ' . $page);
            }
            #Set titles
            $this->og_desc = $this->types[$this->subservice_name]['name'].', '.$this->page_word.' '.$page;
            $this->title = $this->og_desc;
            $this->h1 = $this->og_desc;
        }
        #Merge with extra fields and return the result
        return array_merge($output_array, $this->extras());
    }
    
    #Get date from results
    final protected function getDate(array $results): int|string
    {
        #Prepare the array of dates
        $dates = array_column($results['entities'], 'updated');
        #Return max value if the dates' array is not empty or 0 otherwise
        if (empty($dates)) {
            return 0;
        }
        return max($dates);
    }
}
