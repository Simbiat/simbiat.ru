<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\BICTracker;

use Simbiat\BIC\AccountKeying;
use Simbiat\Website\Abstracts\Page;

class Keying extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/keying', 'name' => 'Ключевание']
    ];
    #Sub service name
    protected string $subservice_name = 'keying';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Ключевание счёта';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Ключевание счёта';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Проверка корректности контрольного символа в номере счёта против номера банковского идентификационного кода';
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
    #Flag to indicate this is a static page
    protected bool $static = true;
    #Link to JS module for preload
    protected string $js_module = 'bictracker/keying';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_bic'];
    
    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $output_array['check_result'] = null;
        if (!empty($path[0]) && !empty($path[1])) {
            $output_array['check_result'] = AccountKeying::accCheck($path[0], $path[1]);
            if ($output_array['check_result'] !== false) {
                $output_array['bic_value'] = $path[0];
                $output_array['acc_value'] = $path[1];
                $this->title = 'Ключевание счёта '.$path[1];
                $this->h1 = $this->title;
                $this->alt_links = [['href' => '/api/bictracker/keying/'.$path[0].'/'.$path[1], 'rel' => 'alternate', 'title' => 'Ссылка на API', 'type' => 'application/json; charset=utf-8'],];
            }
            if (is_numeric($output_array['check_result'])) {
                $output_array['first_half'] = preg_replace('/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u', '$1', $path[1]);
                $output_array['second_half'] = preg_replace('/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u', '$3', $path[1]);
            }
        }
        return $output_array;
    }
}
