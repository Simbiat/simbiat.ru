<?php
declare(strict_types = 1);

namespace App\Controller\FFXIV;

use App\Controller\Abstracts\Page;

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
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_ff'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        if ($this::CROSSWORLD) {
            $entity = new \App\Entity\FFXIV\CrossworldLinkshell($id);
        } else {
            $entity = new \App\Entity\FFXIV\Linkshell($id);
        }
        $output_array['linkshell'] = $entity->getArray();
        #Check if ID was found
        if (empty($output_array['linkshell']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        $output_array['linkshell']['crossworld'] = $this::CROSSWORLD;
        #Try to exit early based on the modification date
        $this->lastModified($output_array['linkshell']['dates']['updated']);
        #Check if linked to the current user
        if ($_SESSION['user_id'] !== 1 && \in_array($_SESSION['user_id'], \array_column($output_array['linkshell']['members'], 'user_id'), true)) {
            $output_array['linkshell']['linked'] = true;
        } else {
            $output_array['linkshell']['linked'] = false;
        }
        $output_array['linkshell']['dates']['scheduled'] = $entity->scheduleUpdate();
        if (
            (
                empty($output_array['linkshell']['dates']['deleted']) && (
                    empty($output_array['linkshell']['dates']['scheduled']) ||
                    $output_array['linkshell']['linked']
                )
            ) ||
            \in_array('refresh_all_ff', $_SESSION['permissions'], true)
        ) {
            $output_array['linkshell']['can_refresh'] = true;
        } else {
            $output_array['linkshell']['can_refresh'] = false;
        }
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
        $output_array['linkshell']['is_fresh'] = (\time() - $output_array['linkshell']['dates']['updated'] < 86400);
        if (empty($output_array['linkshell']['dates']['deleted'])) {
            $output_array['linkshell']['lodestone_url'] = 'https://eu.finalfantasyxiv.com/lodestone/'.($this::CROSSWORLD ? 'crossworld_' : '').'linkshell/'.$id;
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/'.($this::CROSSWORLD ? 'crossworld_' : '').'linkshells/'.$id.'/lodestone/'];
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => $output_array['linkshell']['lodestone_url']];
            if (!empty($output_array['linkshell']['community'])) {
                $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$output_array['linkshell']['community']];
            }
        } else {
            $output_array['linkshell']['lodestone_url'] = null;
        }
        return $output_array;
    }
}
