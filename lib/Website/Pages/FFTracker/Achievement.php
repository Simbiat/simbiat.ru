<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

class Achievement extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/achievements', 'name' => 'Achievements']
    ];
    #Sub service name
    protected string $subservice_name = 'achievement';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Achievement';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Achievement';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Achievement';
    #Link to JS module for preload
    protected string $js_module = 'fftracker/entity';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_ff'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        $output_array['achievement'] = new \Simbiat\FFXIV\Achievement($id)->getArray();
        #Check if ID was found
        if (empty($output_array['achievement']['id'])) {
            return ['http_error' => 404];
        }
        #Try to exit early based on modification date
        $this->lastModified($output_array['achievement']['updated']);
        #Continue breadcrumbs
        $this->breadcrumb[] = ['href' => '/fftracker/achievements/'.$id, 'name' => $output_array['achievement']['name']];
        #Update meta
        $this->title = $output_array['achievement']['name'];
        $this->h1 = $this->title;
        $this->og_desc = $output_array['achievement']['name'].' on FFXIV Tracker';
        #Link header/tag for API
        $this->alt_links = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/achievements/'.$id],
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/achievements/'.$id.'/lodestone/'],
        ];
        if (!empty($output_array['achievement']['db_id'])) {
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/'.$output_array['achievement']['db_id']];
        }
        if (!empty($output_array['achievement']['rewards']['item']['id'])) {
            $this->alt_links[] = ['type' => 'text/html', 'title' => 'Lodestone EU page of the reward item', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/'.$output_array['achievement']['rewards']['item']['id']];
        }
        #Set favicon
        if (\is_file(Config::$icons.$output_array['achievement']['icon'])) {
            $output_array['favicon'] = '/assets/images/fftracker/icons/'.$output_array['achievement']['icon'];
        }
        return $output_array;
    }
}
