<?php
declare(strict_types = 1);

namespace App\Controller\FFXIV;

use App\Controller\Abstracts\Page;
use App\Entity\FFXIV\AbstractEntity;

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
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_ff'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        $entity = new \App\Entity\FFXIV\FreeCompany($id);
        $output_array['freecompany'] = $entity->getArray();
        #Check if ID was found
        if (empty($output_array['freecompany']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        #Try to exit early based on the modification date
        $this->lastModified($output_array['freecompany']['dates']['updated']);
        $output_array['freecompany']['dates']['scheduled'] = $entity->scheduleUpdate();
        #Check if linked to the current user
        if ($_SESSION['user_id'] !== 1 && \in_array($_SESSION['user_id'], \array_column($output_array['freecompany']['members'], 'user_id'), true)) {
            $output_array['freecompany']['linked'] = true;
        } else {
            $output_array['freecompany']['linked'] = false;
        }
        if (
            (
                empty($output_array['freecompany']['dates']['deleted']) && (
                    empty($output_array['freecompany']['dates']['scheduled']) ||
                    $output_array['freecompany']['linked']
                )
            ) ||
            \in_array('refresh_all_ff', $_SESSION['permissions'], true)
        ) {
            $output_array['freecompany']['can_refresh'] = true;
        } else {
            $output_array['freecompany']['can_refresh'] = false;
        }
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
        $output_array['freecompany']['is_fresh'] = (\time() - $output_array['freecompany']['dates']['updated'] < 86400);
        if (empty($output_array['freecompany']['dates']['deleted'])) {
            $output_array['freecompany']['lodestone_url'] = 'https://eu.finalfantasyxiv.com/lodestone/freecompany/'.$id;
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/freecompanies/'.$id.'/lodestone'];
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => $output_array['freecompany']['lodestone_url']];
            if (!empty($output_array['freecompany']['community'])) {
                $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Group\'s community page on Lodestone EU', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/community_finder/'.$output_array['freecompany']['community']];
            }
        } else {
            $output_array['freecompany']['lodestone_url'] = null;
        }
        #Merge crest and update favicon
        $output_array['freecompany']['crest'] = AbstractEntity::crestToFavicon($output_array['freecompany']['crest']);
        $output_array['favicon'] = $output_array['freecompany']['crest'];
        return $output_array;
    }
}
