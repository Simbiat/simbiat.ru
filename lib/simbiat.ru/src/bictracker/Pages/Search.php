<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\bictracker\Library;
use Simbiat\bictracker\Search\ClosedBics;
use Simbiat\bictracker\Search\OpenBics;
use Simbiat\HTTP20\Headers;

class Search extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subServiceName = 'search';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Поиск по БИК Трекеру';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Поиск по БИК Трекеру';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Поиск по БИК Трекеру';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize search value
        $decodedSearch = preg_replace('/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/', '', rawurldecode($path[0] ?? ''));
        #Get date
        $outputArray['bicDate'] = (new Library)->bicDate();
        $this->lastModified($outputArray['bicDate']);
        #Check if search value was provided
        if (empty($decodedSearch)) {
            #Get search results
            $outputArray['searchresult']['openBics'] = (new OpenBics())->Search();
            $outputArray['searchresult']['closedBics'] = (new ClosedBics())->Search();
        } else {
            #Continue breadcrumbs
            $this->breadCrumb[] = ['href' => '/bictracker/search/' . $path[0], 'name' => 'Поиск `' . $decodedSearch.'`'];
            #Get search results
            $outputArray['searchresult']['openBics'] = (new OpenBics())->Search($decodedSearch);
            $outputArray['searchresult']['closedBics'] = (new ClosedBics())->Search($decodedSearch);
            $outputArray['searchvalue'] = $decodedSearch;
            #Set titles
            $this->h1 = $this->title = 'Поиск `'.$decodedSearch.'`';
            $this->ogdesc = 'Поиск `'.$decodedSearch.'` по БИК Трекеру';
        }
        return $outputArray;
    }
}
