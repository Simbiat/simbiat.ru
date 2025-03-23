<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Abstracts\Page;

class Character extends Page
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
        $outputArray['character'] = (new \Simbiat\Website\fftracker\Entities\Character($id))->getArray();
        #Check if ID was found
        if (empty($outputArray['character']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['character']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadCrumb[] = ['href' => '/fftracker/characters/' . $id, 'name' => $outputArray['character']['name']];
        #Update meta
        $this->h1 = $this->title = $outputArray['character']['name'];
        $this->ogdesc = $outputArray['character']['name'] . ' on FFXIV Tracker';
        #Setup OG profile for characters
        $outputArray['ogtype'] = 'profile';
        $profName = explode(' ', $outputArray['character']['name']);
        $outputArray['ogextra'] = '
            <meta property="profile:first_name" content="'.htmlspecialchars($profName[0]).'" />
            <meta property="profile:last_name" content="'.htmlspecialchars($profName[1]).'" />
            <meta property="profile:username" content="'.htmlspecialchars($outputArray['character']['name']).'" />
            <meta property="profile:gender" content="'.htmlspecialchars(($outputArray['character']['biology']['gender'] === 1 ? 'male' : 'female')).'" />
        ';
        #Link header/tag for API
        $this->altLinks = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/characters/' . $id],
        ];
        if (empty($outputArray['character']['dates']['deleted'])) {
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/characters/' . $id. '/lodestone'];
            $this->altLinks[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/character/' . $id];
        }
        #Set favicon to avatar
        $outputArray['favicon'] = 'https://img2.finalfantasyxiv.com/f/'.$outputArray['character']['avatarID'].'c0.jpg';
        return $outputArray;
    }
}
