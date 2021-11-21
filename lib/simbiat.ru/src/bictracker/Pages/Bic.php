<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;

class Bic extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subServiceName = 'bic';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Детали организации из БИК трекера';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Детали организации из БИК трекера';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Детали организации из БИК трекера';
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 259200;

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = (new Headers);
        #Sanitize BIC
        $BIC = preg_replace('/[^0-9]/', '', rawurldecode($path[0] ?? ''));
        if (empty($BIC)) {
            $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/httperror/404', true, true, false);
        }
        #Try to get details
        $outputArray['bicdetails'] = (new \Simbiat\bictracker\Bic)->setId($BIC)->getArray();
        #Check if ID was found
        if (empty($outputArray['bicdetails'])) {
            #404
            $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/httperror/404', false, true, false);
        }
        #Try to exit early based on modification date
        if (!empty($outputArray['bicdetails']['Updated'])) {
            $this->lastModified($outputArray['bicdetails']['Updated']);
        }
        #Generate timeline
        if (!empty($outputArray['bicdetails']['restrictions'])) {
            $outputArray['bicdetails']['restrictions'] = (new HTML)->timeline($outputArray['bicdetails']['restrictions']);
        }
        #Continue breadcrumbs
        if (!empty($outputArray['bicdetails']['PrntBIC'])) {
            foreach(array_reverse($outputArray['bicdetails']['PrntBIC']) as $bank) {
                $this->breadCrumb = [['href' => '/bictracker/bic/' . $bank['id'], 'name' => $bank['name']]];
            }
            $this->breadCrumb[] = ['href' => '/bictracker/bic/' . $BIC, 'name' => $outputArray['bicdetails']['NameP']];
        } else {
            $this->breadCrumb = [['href' => '/bictracker/bic/' . $BIC, 'name' => $outputArray['bicdetails']['NameP']]];
        }
        #Update meta
        $this->h1 = $this->title = $outputArray['bicdetails']['NameP'];
        $this->ogdesc = $outputArray['bicdetails']['NameP'] . ' (' . $outputArray['bicdetails']['BIC'] . ') в БИК трекере';
        #Link header/tag for API
        $altLink = [['rel' => 'alternate', 'type' => 'application/json', 'title' => 'Представление в формате JSON', 'href' => '/api/bictracker/bic/' . $BIC]];
        #Send HTTP header
        $headers->links($altLink);
        #Add link to HTML
        $outputArray['link_extra'] = $headers->links($altLink, 'head');
        return $outputArray;
    }
}
