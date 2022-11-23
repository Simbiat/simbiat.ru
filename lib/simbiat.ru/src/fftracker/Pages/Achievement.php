<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\FFTracker;

class Achievement extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/achievements', 'name' => 'Achievements']
    ];
    #Sub service name
    protected string $subServiceName = 'achievement';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Achievement';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Achievement';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Achievement';
    #Link to JS module for preload
    protected string $jsModule = 'fftracker/entity';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewFF'];

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        $outputArray['achievement'] = (new \Simbiat\fftracker\Entities\Achievement($id))->getArray();
        #Check if ID was found
        if (empty($outputArray['achievement']['id'])) {
            return ['http_error' => 404];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['achievement']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/achievements/' . $id, 'name' => $outputArray['achievement']['name']];
        #Update meta
        $this->h1 = $this->title = $outputArray['achievement']['name'];
        $this->ogdesc = $outputArray['achievement']['name'] . ' on FFXIV Tracker';
        #Link header/tag for API
        $this->altLinks = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/achievements/' . $id],
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/achievements/' . $id.'/lodestone/'],
        ];
        if (!empty($outputArray['achievement']['dbid'])) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/' . $outputArray['achievement']['dbid']];
        }
        if (!empty($outputArray['achievement']['rewards']['item']['id'])) {
            $this->altLinks[] = ['type' => 'text/html', 'title' => 'Lodestone EU page of the reward item', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/' .$outputArray['achievement']['rewards']['item']['id']];
        }
        #Set favicon
        if (is_file(FFTracker::$icons.$outputArray['achievement']['icon'])) {
            $outputArray['favicon'] = '/img/fftracker/icons/'.$outputArray['achievement']['icon'];
        }
        return $outputArray;
    }
}
