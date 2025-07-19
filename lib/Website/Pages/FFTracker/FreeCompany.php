<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Page;

class FreeCompany extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/freecompanies', 'name' => 'Free Companies']
    ];
    #Sub service name
    protected string $subservice_name = 'freecompany';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Free Company';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Free Company';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Free Company';
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
        $output_array['freecompany'] = new \Simbiat\FFXIV\FreeCompany($id)->getArray();
        #Check if ID was found
        if (empty($output_array['freecompany']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on modification date
        $this->lastModified($output_array['freecompany']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadcrumb[] = ['href' => '/fftracker/freecompanies/'.$id, 'name' => $output_array['freecompany']['name']];
        #Update meta
        $this->title = $output_array['freecompany']['name'];
        $this->h1 = $this->title;
        $this->og_desc = $output_array['freecompany']['name'].' on FFXIV Tracker';
        #Link header/tag for API
        $this->alt_links = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/freecompanies/'.$id],
        ];
        if (empty($output_array['freecompany']['dates']['deleted'])) {
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/freecompanies/'.$id.'/lodestone'];
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/freecompany/'.$id];
            if (!empty($output_array['freecompany']['community'])) {
                $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$output_array['freecompany']['community']];
            }
        }
        #Merge crest and update favicon
        $output_array['freecompany']['crest'] = AbstractTrackerEntity::crestToFavicon($output_array['freecompany']['crest']);
        $output_array['favicon'] = $output_array['freecompany']['crest'];
        #Check if linked to current user
        if ($_SESSION['user_id'] !== 1 && \in_array($_SESSION['user_id'], \array_column($output_array['freecompany']['members'], 'user_id'), true)) {
            $output_array['freecompany']['linked'] = true;
        }
        return $output_array;
    }
}
