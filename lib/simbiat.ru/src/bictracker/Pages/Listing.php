<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\bictracker\Library;
use Simbiat\bictracker\Search\ClosedBics;
use Simbiat\bictracker\Search\OpenBics;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;

class Listing extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subServiceName = 'open';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Поиск по БИК Трекеру';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Поиск по БИК Трекеру';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Поиск по БИК Трекеру';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = (new Headers);
        #Attempt early exit
        $headers->lastModified((new Library)->bicDate(), true);
        $this->subServiceName = $path[0];
        $page = intval($_GET['page'] ?? 1);
        #Sanitize search value
        $decodedSearch = preg_replace('/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<>]/', '', rawurldecode($path[1] ?? ''));
        if ($this->subServiceName === 'open') {
            $outputArray['listOfEntities'] = (new OpenBics)->listEntities($page, $decodedSearch);
        } else {
            $outputArray['listOfEntities'] = (new ClosedBics)->listEntities($page, $decodedSearch);
        }
        #If int is returned, we have a bad page
        if (is_int($outputArray['listOfEntities'])) {
            #Redirect
            $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/bictracker/'.$this->subServiceName.'/' . $path[1], false, true, false);
        } else {
            #Generate pagination
            $html = (new HTML);
            $outputArray['pagination_top'] = $html->pagination($page, $outputArray['listOfEntities']['pages'], prefix: '?page=');
            $outputArray['pagination_bottom'] = $html->pagination($page, $outputArray['listOfEntities']['pages'], prefix: '?page=');
            if (!empty($decodedSearch)) {
                #Set search value, if available
                $outputArray['searchvalue'] = $decodedSearch;
                #Continue breadcrumbs
                $this->breadCrumb = [['href' => '/bictracker/search/' . $path[1], 'name' => 'Поиск ' . $decodedSearch]];
                $this->breadCrumb[] = ['href' => '/bictracker/' . $this->subServiceName . '/' . $path[1], 'name' => match($this->subServiceName){'open'=>'Открытые', 'closed'=>'Закрытые'}];
                if ($page > 1) {
                    $this->breadCrumb[] = ['href' => '/bictracker/' . $this->subServiceName . '/' . $path[1] . '/?page=' . $page, 'name' => 'Страница ' . $page];
                }
                $this->h1 = $this->title = 'Поиск `'.$decodedSearch.'`, страница '.$page;
                $this->title = 'Поиск `'.$decodedSearch.'` по БИК Трекеру, страница '.$page;
            } else {
                #Continue breadcrumbs
                $this->breadCrumb = [['href' => '/bictracker/' . $this->subServiceName, 'name' => match($this->subServiceName){'open'=>'Открытые БИК', 'closed'=>'Закрытые БИК'}]];
                if ($page > 1) {
                    $this->breadCrumb[] = ['href' => '/bictracker/' . $this->subServiceName . '/?page=' . $page, 'name' => 'Страница ' . $page];
                }
                $this->h1 = $this->title = match($this->subServiceName){'open'=>'Открытые БИК', 'closed'=>'Закрытые БИК'}.', страница '.$page;
                $this->title = match($this->subServiceName){'open'=>'Открытые БИК', 'closed'=>'Закрытые БИК'}.', страница '.$page.' на БИК Трекере';
            }
        }
        return $outputArray;
    }
}
