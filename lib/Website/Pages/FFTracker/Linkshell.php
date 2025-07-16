<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Abstracts\Page;

class Linkshell extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/linkshells', 'name' => 'Linkshells']
    ];
    #Sub service name
    protected string $subservice_name = 'linkshell';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Linkshell';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Linkshell';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Linkshell';
    protected const CROSSWORLD = false;
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
        if ($this::CROSSWORLD) {
            $output_array['linkshell'] = new \Simbiat\FFXIV\CrossworldLinkshell($id)->getArray();
        } else {
            $output_array['linkshell'] = new \Simbiat\FFXIV\Linkshell($id)->getArray();
        }
        #Check if ID was found
        if (empty($output_array['linkshell']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        if ($this::CROSSWORLD) {
            $output_array['linkshell']['crossworld'] = true;
        }
        #Try to exit early based on modification date
        $this->lastModified($output_array['linkshell']['dates']['updated']);
        #Continue breadcrumbs
        $this->breadcrumb[] = ['href' => '/fftracker/'.($this::CROSSWORLD ? 'crossworld_' : '').'linkshells/'.$id, 'name' => $output_array['linkshell']['name']];
        #Update meta
        $this->title = $output_array['linkshell']['name'];
        $this->h1 = $this->title;
        $this->og_desc = $output_array['linkshell']['name'].' on FFXIV Tracker';
        #Link header/tag for API
        $this->alt_links = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/'.($this::CROSSWORLD ? 'crossworld_' : '').'linkshells/'.$id],
        ];
        if (empty($output_array['linkshell']['dates']['deleted'])) {
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/'.($this::CROSSWORLD ? 'crossworld_' : '').'linkshells/'.$id.'/lodestone/'];
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/'.($this::CROSSWORLD ? 'crossworld_' : '').'linkshell/'.$id];
            if (!empty($output_array['linkshell']['community'])) {
                $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$output_array['linkshell']['community']];
            }
        }
        #Check if linked to current user
        if ($_SESSION['user_id'] !== 1 && in_array($_SESSION['user_id'], array_column($output_array['linkshell']['members'], 'user_id'), true)) {
            $output_array['linkshell']['linked'] = true;
        }
        return $output_array;
    }
}
