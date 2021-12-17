<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;
use Simbiat\HTTP20\Headers;

class Linkshell extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/linkshells', 'name' => 'Linkshells']
    ];
    #Sub service name
    protected string $subServiceName = 'linkshell';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Linkshell';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Linkshell';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Linkshell';
    protected const crossworld = false;

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = HomePage::$headers;
        #Sanitize ID
        $id = rawurldecode($path[0] ?? '');

        #Try to get details
        try {
            if ($this::crossworld) {
                $outputArray['linkshell'] = (new \Simbiat\fftracker\Entities\CrossworldLinkshell())->setId($id)->getArray();
            } else {
                $outputArray['linkshell'] = (new \Simbiat\fftracker\Entities\Linkshell())->setId($id)->getArray();
            }
        } catch (\UnexpectedValueException) {
            return ['http_error' => 404];
        }
        #Check if ID was found
        if (empty($outputArray['linkshell']['id'])) {
            return ['http_error' => 404];
        }
        if ($this::crossworld) {
            $outputArray['linkshell']['crossworld'] = true;
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['linkshell']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/'.($this::crossworld ? 'crossworld_' : '').'linkshell/' . $id, 'name' => $outputArray['linkshell']['name']];
        #Update meta
        $this->h1 = $this->title = $outputArray['linkshell']['name'];
        $this->ogdesc = $outputArray['linkshell']['name'] . ' on FFXIV Tracker';
        #Link header/tag for API
        $altLink = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/'.($this::crossworld ? 'crossworld_' : '').'linkshell/' . $id],
        ];
        if (empty($outputArray['linkshell']['dates']['deleted'])) {
            $altLink[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/'.($this::crossworld ? 'crossworld_' : '').'linkshell/' . $id.'/lodestone/'];
            $altLink[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/'.($this::crossworld ? 'crossworld_' : '').'linkshell/' . $id];
            if (!empty($outputArray['linkshell']['community'])) {
                $altLink[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/' . $outputArray['linkshell']['community']];
            }
        }
        #Send HTTP header
        $headers->links($altLink);
        #Add link to HTML
        $outputArray['link_extra'] = $headers->links($altLink, 'head');
        return $outputArray;
    }
}
