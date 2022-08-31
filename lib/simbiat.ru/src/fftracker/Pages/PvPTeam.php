<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\FFTracker;

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
    #Link to JS module for preload
    protected string $jsModule = 'fftracker/entity';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        $outputArray['pvpteam'] = (new \Simbiat\fftracker\Entities\PvPTeam($id))->getArray();
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
        $this->altLinks = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/pvpteam/' . $id],
        ];
        if (empty($outputArray['pvpteam']['dates']['deleted'])) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/pvpteam/' . $id. '/lodestone'];
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/pvpteam/' . $id];
            if (!empty($outputArray['pvpteam']['community'])) {
                $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/' . $outputArray['pvpteam']['community']];
            }
        }
        #Try to change favicon
        if (!empty($outputArray['pvpteam']['crest'])) {
            #Get full path
            $fullPath = substr($outputArray['pvpteam']['crest'], 0, 2).'/'.substr($outputArray['pvpteam']['crest'], 2, 2).'/'.$outputArray['pvpteam']['crest'].'.webp';
            if (is_file(FFTracker::$crests.$fullPath)) {
                $outputArray['favicon'] = '/img/fftracker/merged-crests/'.$fullPath;
            }
        }
        #Check if linked to current user
        if (!empty($_SESSION['userid']) && in_array($_SESSION['userid'], array_column($outputArray['pvpteam']['members'], 'userid'))) {
            $outputArray['pvpteam']['linked'] = true;
        }
        return $outputArray;
    }
}
