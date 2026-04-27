<?php
declare(strict_types = 1);

namespace App\Controller\FFXIV;

use App\Controller\Abstracts\Page;

class Character extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/characters', 'name' => 'Characters']
    ];
    #Sub service name
    protected string $subservice_name = 'character';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Character';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Character';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Character';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_ff'];
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? '';
        #Try to get details
        $entity = new \App\Entity\FFXIV\Character($id);
        $output_array['character'] = $entity->getArray();
        #Check if ID was found
        if (empty($output_array['character']['id'])) {
            return ['http_error' => 404, 'suggested_link' => $this->getLastCrumb()];
        }
        if (!empty($output_array['character']['dates']['hidden'])) {
            #Do not cache hidden characters on our side
            $this->cache_age = 0;
            #Try to not cache hidden characters in browsers/proxies
            $this->cache_strategy = 'none';
        }
        #Try to exit early based on the modification date
        $this->lastModified($output_array['character']['dates']['updated']);
        $output_array['character']['dates']['scheduled'] = $entity->scheduleUpdate();
        if (
            (
                empty($output_array['character']['dates']['deleted']) && (
                    empty($output_array['character']['dates']['scheduled']) ||
                    !empty($output_array['character']['owned']['user_id'])
                )
            ) ||
            \in_array('refresh_all_ff', $_SESSION['permissions'], true)
        ) {
            $output_array['character']['can_refresh'] = true;
        } else {
            $output_array['character']['can_refresh'] = false;
        }
        #Continue breadcrumbs
        $this->breadcrumb[] = ['href' => '/fftracker/characters/'.$id, 'name' => $output_array['character']['name']];
        #Update meta
        $this->title = $output_array['character']['name'];
        $this->h1 = $this->title;
        $this->og_desc = $output_array['character']['name'].' on FFXIV Tracker';
        #Setup OG profile for characters
        $output_array['ogtype'] = 'profile';
        $prof_name = \explode(' ', $output_array['character']['name']);
        $output_array['ogextra'] = '
            <meta property="profile:first_name" content="'.\htmlspecialchars($prof_name[0], \ENT_QUOTES | \ENT_SUBSTITUTE).'" />
            <meta property="profile:last_name" content="'.\htmlspecialchars($prof_name[1], \ENT_QUOTES | \ENT_SUBSTITUTE).'" />
            <meta property="profile:username" content="'.\htmlspecialchars($output_array['character']['name'], \ENT_QUOTES | \ENT_SUBSTITUTE).'" />
            <meta property="profile:gender" content="'.\htmlspecialchars(($output_array['character']['biology']['gender'] === 1 ? 'male' : 'female'), \ENT_QUOTES | \ENT_SUBSTITUTE).'" />
        ';
        #Link header/tag for API
        $this->alt_links = [
            ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/characters/'.$id],
        ];
        $output_array['character']['is_fresh'] = (\time() - $output_array['character']['dates']['updated'] < 86400);
        if (empty($output_array['character']['dates']['deleted'])) {
            $output_array['character']['lodestone_url'] = 'https://eu.finalfantasyxiv.com/lodestone/character/'.$id;
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/characters/'.$id.'/lodestone'];
            $this->alt_links[] = ['rel' => 'alternate', 'type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => $output_array['character']['lodestone_url']];
        } else {
            $output_array['character']['lodestone_url'] = null;
        }
        #Set favicon to avatar
        if ($output_array['character']['avatar_id'] !== 'defaultf') {
            $output_array['favicon'] = 'https://img2.finalfantasyxiv.com/f/'.$output_array['character']['avatar_id'].'c0.jpg';
        }
        return $output_array;
    }
}
