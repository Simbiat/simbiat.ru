<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\BICTracker;

use Simbiat\Website\Abstracts\Page;

class Bic extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subservice_name = 'bic';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Детали организации из БИК трекера';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Детали организации из БИК трекера';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Детали организации из БИК трекера';
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_bic'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize BIC
        $bic = $path[0] ?? '';
        #Try to get details
        $output_array['bicdetails'] = new \Simbiat\BIC\BIC($bic)->getArray();
        #Check if ID was found
        if ($output_array['bicdetails']['id'] === null) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on modification date
        if (!empty($output_array['bicdetails']['Updated'])) {
            $this->lastModified($output_array['bicdetails']['Updated']);
        }
        #Continue breadcrumbs
        if (!empty($output_array['bicdetails']['PrntBIC'])) {
            foreach(\array_reverse($output_array['bicdetails']['PrntBIC']) as $bank) {
                $this->breadcrumb = [['href' => '/bictracker/bics/'.$bank['id'], 'name' => $bank['name']]];
            }
            $this->breadcrumb[] = ['href' => '/bictracker/bics/'.$bic, 'name' => $output_array['bicdetails']['NameP']];
        } else {
            $this->breadcrumb = [['href' => '/bictracker/bics/'.$bic, 'name' => $output_array['bicdetails']['NameP']]];
        }
        #Update meta
        $this->title = $output_array['bicdetails']['NameP'];
        $this->h1 = $this->title;
        $this->og_desc = $output_array['bicdetails']['NameP'].' ('.$output_array['bicdetails']['BIC'].') в БИК трекере';
        #Link header/tag for API
        $this->alt_links = [['rel' => 'alternate', 'type' => 'application/json', 'title' => 'Представление в формате JSON', 'href' => '/api/bictracker/bics/'.$bic]];
        if (\in_array($output_array['bicdetails']['Rgn'], ['ДОНЕЦКАЯ НАРОДНАЯ РЕСПУБЛИКА', 'ЗАПОРОЖСКАЯ ОБЛАСТЬ', 'ЛУГАНСКАЯ НАРОДНАЯ РЕСПУБЛИКА', 'ХЕРСОНСКАЯ ОБЛАСТЬ'])) {
            $output_array['bicdetails']['Ukraine'] = true;
            $this->title = 'Це Україна! Іди додому, окупанте!';
            $this->h1 = 'Це Україна! Іди додому, окупанте!';
        } else {
            $output_array['bicdetails']['Ukraine'] = false;
        }
        return $output_array;
    }
}
