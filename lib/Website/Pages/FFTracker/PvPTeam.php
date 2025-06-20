<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Page;

class PvPTeam extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/pvpteams', 'name' => 'PvP Teams']
    ];
    #Sub service name
    protected string $subServiceName = 'pvpteam';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'PvP Team';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'PvP Team';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'PvP Team';
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
        $outputArray['pvpteam'] = new \Simbiat\FFXIV\PvPTeam($id)->getArray();
        #Check if ID was found
        if (empty($outputArray['pvpteam']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['pvpteam']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/pvpteams/'.$id, 'name' => $outputArray['pvpteam']['name']];
        #Update meta
        $this->title = $outputArray['pvpteam']['name'];
        $this->h1 = $this->title;
        $this->ogdesc = $outputArray['pvpteam']['name'].' on FFXIV Tracker';
        #Link header/tag for API
        $this->altLinks = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/pvpteams/'.$id],
        ];
        if (empty($outputArray['pvpteam']['dates']['deleted'])) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/pvpteams/'.$id.'/lodestone'];
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/pvpteam/'.$id];
            if (!empty($outputArray['pvpteam']['community'])) {
                $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$outputArray['pvpteam']['community']];
            }
        }
        #Merge crest and update favicon
        $outputArray['pvpteam']['crest'] = AbstractTrackerEntity::crestToFavicon($outputArray['pvpteam']['crest']);
        $outputArray['favicon'] = $outputArray['pvpteam']['crest'];
        #Check if linked to current user
        if ($_SESSION['user_id'] !== 1 && \in_array($_SESSION['user_id'], array_column($outputArray['pvpteam']['members'], 'user_id'), true)) {
            $outputArray['pvpteam']['linked'] = true;
        }
        return $outputArray;
    }
}
