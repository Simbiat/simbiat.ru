<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\fftracker\Search\Achievements;
use Simbiat\fftracker\Search\Characters;
use Simbiat\fftracker\Search\Companies;
use Simbiat\fftracker\Search\PVP;
use Simbiat\fftracker\Search\Linkshells;

class Search extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/search', 'name' => 'Search']
    ];
    #Sub service name
    protected string $subServiceName = 'search';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'FFXIV Tracker Search';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'FFXIV Tracker Search';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Tracker Search';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize search value
        $decodedSearch = preg_replace('/[^a-zA-Z0-9 _\'\-,!]/', '', rawurldecode($path[0] ?? ''));
        #Check if search value was provided
        if (empty($decodedSearch)) {
            #Get search results
            $outputArray['searchresult']['characters'] = (new Characters())->Search('', 6);
            $outputArray['searchresult']['freecompanies'] = (new Companies())->Search('', 6);
            $outputArray['searchresult']['pvpteams'] = (new PVP())->Search('', 6);
            $outputArray['searchresult']['linkshells'] = (new Linkshells())->Search('', 6);
            $outputArray['searchresult']['achievements'] = (new Achievements())->Search('', 6);
        } else {
            #Continue breadcrumbs
            $this->breadCrumb[] = ['href' => '/fftracker/search/' . $path[0], 'name' => 'Search for `' . $decodedSearch.'`'];
            #Get search results
            $outputArray['searchresult']['characters'] = (new Characters())->Search($decodedSearch, 6);
            $outputArray['searchresult']['freecompanies'] = (new Companies())->Search($decodedSearch, 6);
            $outputArray['searchresult']['pvpteams'] = (new PVP())->Search($decodedSearch, 6);
            $outputArray['searchresult']['linkshells'] = (new Linkshells())->Search($decodedSearch, 6);
            $outputArray['searchresult']['achievements'] = (new Achievements())->Search($decodedSearch, 6);
            $outputArray['searchvalue'] = $decodedSearch;
            #Set titles
            $this->h1 = $this->title = 'Search for `'.$decodedSearch.'`';
            $this->ogdesc = 'Search for `'.$decodedSearch.'` on Final Fantasy XIV Tracker';
        }
        return $outputArray;
    }
}
