<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HTTP20\Headers;

class FreeCompany extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/freecompanies', 'name' => 'Free Companies']
    ];
    #Sub service name
    protected string $subServiceName = 'freecompany';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Free Company';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Free Company';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Free Company';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = (new Headers);
        #Sanitize ID
        $id = rawurldecode($path[0] ?? '');


        #Placeholder
        return ['http_error' => 503, 'construction' => true, 'error_page' => true];


        #Try to get details
        try {
            $outputArray['freecompany'] = (new \Simbiat\fftracker\Entities\FreeCompany())->setId($id)->getArray();
        } catch (\UnexpectedValueException) {
            return ['http_error' => 404];
        }
        #Check if ID was found
        if (empty($outputArray['freecompany']['id'])) {
            return ['http_error' => 404];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['freecompany']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/freecompany/' . $id, 'name' => $outputArray['freecompany']['name']];
        #Update meta
        $this->h1 = $this->title = $outputArray['freecompany']['name'];
        $this->ogdesc = $outputArray['freecompany']['name'] . ' on FFXIV Tracker';
        #Link header/tag for API
        #Link header/tag for API
        $altLink = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/freecompany/' . $id],
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/freecompany/lodestone/' . $id],
            ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/freecompany/' . $id],
        ];
        #Send HTTP header
        $headers->links($altLink);
        #Add link to HTML
        $outputArray['link_extra'] = $headers->links($altLink, 'head');
        return $outputArray;
    }
}
