<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\Abstracts\Page;

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
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewBic'];

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize BIC
        $BIC = $path[0] ?? '';
        #Try to get details
        $outputArray['bicdetails'] = (new \Simbiat\bictracker\Bic($BIC))->getArray();
        #Check if ID was found
        if ($outputArray['bicdetails']['id'] === null) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on modification date
        if (!empty($outputArray['bicdetails']['Updated'])) {
            $this->lastModified($outputArray['bicdetails']['Updated']);
        }
        #Continue breadcrumbs
        if (!empty($outputArray['bicdetails']['PrntBIC'])) {
            foreach(array_reverse($outputArray['bicdetails']['PrntBIC']) as $bank) {
                $this->breadCrumb = [['href' => '/bictracker/bics/' . $bank['id'], 'name' => $bank['name']]];
            }
            $this->breadCrumb[] = ['href' => '/bictracker/bics/' . $BIC, 'name' => $outputArray['bicdetails']['NameP']];
        } else {
            $this->breadCrumb = [['href' => '/bictracker/bics/' . $BIC, 'name' => $outputArray['bicdetails']['NameP']]];
        }
        #Update meta
        $this->h1 = $this->title = $outputArray['bicdetails']['NameP'];
        $this->ogdesc = $outputArray['bicdetails']['NameP'] . ' (' . $outputArray['bicdetails']['BIC'] . ') в БИК трекере';
        #Link header/tag for API
        $this->altLinks = [['rel' => 'alternate', 'type' => 'application/json', 'title' => 'Представление в формате JSON', 'href' => '/api/bictracker/bics/' . $BIC]];
        if (in_array($outputArray['bicdetails']['Rgn'], ['ДОНЕЦКАЯ НАРОДНАЯ РЕСПУБЛИКА', 'ЗАПОРОЖСКАЯ ОБЛАСТЬ', 'ЛУГАНСКАЯ НАРОДНАЯ РЕСПУБЛИКА', 'ХЕРСОНСКАЯ ОБЛАСТЬ'])) {
            $outputArray['bicdetails']['Ukraine'] = true;
            $this->h1 = $this->title = 'Це Україна! Іди додому, окупанте!';
        } else {
            $outputArray['bicdetails']['Ukraine'] = false;
        }
        return $outputArray;
    }
}
