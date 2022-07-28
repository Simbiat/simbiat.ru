<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\AccountKeying;

class Keying extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/keying', 'name' => 'Ключевание']
    ];
    #Sub service name
    protected string $subServiceName = 'keying';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Ключевание счёта';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Ключевание счёта';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Проверка корректности контрольного символа в номере счёта против номера банковского идентификационного кода';
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
    #Flag to indicate this is a static page
    protected bool $static = true;
    #Link to JS module for preload
    protected string $jsModule = 'bictracker/keying';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray['checkResult'] = null;
        if (!empty($path[0]) && !empty($path[1])) {
            $outputArray['checkResult'] = (new AccountKeying)->accCheck($path[0], $path[1]);
            if ($outputArray['checkResult'] !== false) {
                $outputArray['bic_value'] = $path[0];
                $outputArray['acc_value'] = $path[1];
                $this->h1 = $this->title = 'Ключевание счёта '.$path[1];
                $this->altLinks = [['href' => '/api/bictracker/keying/' . $path[0] . '/' . $path[1], 'rel' => 'alternate', 'title' => 'Ссылка на API', 'type' => 'application/json; charset=utf-8'],];
            }
            if (is_numeric($outputArray['checkResult'])) {
                $outputArray['firstHalf'] = preg_replace('/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u', '$1', $path[1]);
                $outputArray['secondHalf'] = preg_replace('/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u', '$3', $path[1]);
            }
        }
        return $outputArray;
    }
}
