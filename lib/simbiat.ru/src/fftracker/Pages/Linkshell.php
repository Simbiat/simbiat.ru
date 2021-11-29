<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;

class Linkshell extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/characters', 'name' => 'Characters']
    ];
    #Sub service name
    protected string $subServiceName = 'character';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Character';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Character';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Character';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = (new Headers);
        #Sanitize ID
        $id = rawurldecode($path[0] ?? '');
        #Try to get details
        try {
            $outputArray['character'] = (new \Simbiat\fftracker\Entities\Character)->setId($id)->getArray();
        } catch (\UnexpectedValueException) {
            return ['http_error' => 404];
        }
        #Check if ID was found
        if (empty($outputArray['bicdetails'])) {
            return ['http_error' => 404];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['character']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/character/' . $id, 'name' => $outputArray['character']['name']];
        #Update meta
        $this->h1 = $this->title = $outputArray['character']['name'];
        $this->ogdesc = $outputArray['character']['name'] . ' on FFXIV Tracker';
        #Link header/tag for API
        $altLink = [['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/character/' . $id]];
        $altLink[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/character/lodestone/' . $id];
        #Send HTTP header
        $headers->links($altLink);
        #Add link to HTML
        $outputArray['link_extra'] = $headers->links($altLink, 'head');
        return $outputArray;
    }
}
