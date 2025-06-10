<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Page;

class FreeCompany extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/freecompanies', 'name' => 'Free Companies']
    ];
    #Sub service name
    protected string $subServiceName = 'freecompany';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Free Company';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Free Company';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Free Company';
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
        $outputArray['freecompany'] = new \Simbiat\FFXIV\FreeCompany($id)->getArray();
        #Check if ID was found
        if (empty($outputArray['freecompany']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['freecompany']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/freecompanies/'.$id, 'name' => $outputArray['freecompany']['name']];
        #Update meta
        $this->title = $outputArray['freecompany']['name'];
        $this->h1 = $this->title;
        $this->ogdesc = $outputArray['freecompany']['name'].' on FFXIV Tracker';
        #Link header/tag for API
        $this->altLinks = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/freecompanies/'.$id],
        ];
        if (empty($outputArray['freecompany']['dates']['deleted'])) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/freecompanies/'.$id.'/lodestone'];
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/freecompany/'.$id];
            if (!empty($outputArray['freecompany']['community'])) {
                $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$outputArray['freecompany']['community']];
            }
        }
        #Merge crest and update favicon
        $outputArray['freecompany']['crest'] = AbstractTrackerEntity::crestToFavicon($outputArray['freecompany']['crest']);
        $outputArray['favicon'] = $outputArray['freecompany']['crest'];
        #Check if linked to current user
        if ($_SESSION['user_id'] !== 1 && \in_array($_SESSION['user_id'], array_column($outputArray['freecompany']['members'], 'user_id'), true)) {
            $outputArray['freecompany']['linked'] = true;
        }
        return $outputArray;
    }
}
