<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Abstracts\Page;

class Linkshell extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/linkshells', 'name' => 'Linkshells']
    ];
    #Sub service name
    protected string $subServiceName = 'linkshell';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Linkshell';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Linkshell';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Linkshell';
    protected const crossworld = false;
    #Link to JS module for preload
    protected string $jsModule = 'fftracker/entity';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['view_ff'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        if ($this::crossworld) {
            $outputArray['linkshell'] = new \Simbiat\FFXIV\CrossworldLinkshell($id)->getArray();
        } else {
            $outputArray['linkshell'] = new \Simbiat\FFXIV\Linkshell($id)->getArray();
        }
        #Check if ID was found
        if (empty($outputArray['linkshell']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        if ($this::crossworld) {
            $outputArray['linkshell']['crossworld'] = true;
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['linkshell']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/'.($this::crossworld ? 'crossworld_' : '').'linkshells/'.$id, 'name' => $outputArray['linkshell']['name']];
        #Update meta
        $this->title = $outputArray['linkshell']['name'];
        $this->h1 = $this->title;
        $this->ogdesc = $outputArray['linkshell']['name'].' on FFXIV Tracker';
        #Link header/tag for API
        $this->altLinks = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/'.($this::crossworld ? 'crossworld_' : '').'linkshells/'.$id],
        ];
        if (empty($outputArray['linkshell']['dates']['deleted'])) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/'.($this::crossworld ? 'crossworld_' : '').'linkshells/'.$id.'/lodestone/'];
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/'.($this::crossworld ? 'crossworld_' : '').'linkshell/'.$id];
            if (!empty($outputArray['linkshell']['community'])) {
                $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$outputArray['linkshell']['community']];
            }
        }
        #Check if linked to current user
        if ($_SESSION['user_id'] !== 1 && \in_array($_SESSION['user_id'], array_column($outputArray['linkshell']['members'], 'user_id'), true)) {
            $outputArray['linkshell']['linked'] = true;
        }
        return $outputArray;
    }
}
