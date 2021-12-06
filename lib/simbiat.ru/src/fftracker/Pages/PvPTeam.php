<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HTTP20\Headers;

class PvPTeam extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/pvpteams', 'name' => 'PvP Teams']
    ];
    #Sub service name
    protected string $subServiceName = 'pvpteam';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'PvP Team';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'PvP Team';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'PvP Team';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = (new Headers);
        #Sanitize ID
        $id = rawurldecode($path[0] ?? '');
        #Try to get details
        try {
            $outputArray['pvpteam'] = (new \Simbiat\fftracker\Entities\PvPTeam())->setId($id)->getArray();
        } catch (\UnexpectedValueException) {
            return ['http_error' => 404];
        }
        #Check if ID was found
        if (empty($outputArray['pvpteam']['id'])) {
            return ['http_error' => 404];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['pvpteam']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/pvpteam/' . $id, 'name' => $outputArray['pvpteam']['name']];
        #Update meta
        $this->h1 = $this->title = $outputArray['pvpteam']['name'];
        $this->ogdesc = $outputArray['pvpteam']['name'] . ' on FFXIV Tracker';
        #Link header/tag for API
        $altLink = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/pvpteam/' . $id],
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/pvpteam/' . $id.'/lodestone/'],
            ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/pvpteam/' . $id],
        ];
        #Send HTTP header
        $headers->links($altLink);
        #Add link to HTML
        $outputArray['link_extra'] = $headers->links($altLink, 'head');
        return $outputArray;
    }
}
