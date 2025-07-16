<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\BICTracker;

use Simbiat\Website\Search\ClosedBics;
use Simbiat\Website\Search\OpenBics;

class Listing extends \Simbiat\Website\Abstracts\Pages\Listing
{
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $page_word = 'страница';
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subservice_name = 'search';
    #Service name for breadcrumbs
    protected string $service_name = 'bictracker';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Поиск по БИК Трекеру';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Поиск по БИК Трекеру';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Поиск по БИК Трекеру';
    #Linking types to classes
    protected array $types = [
        'openbics' => ['name' => 'Открытые БИК', 'class' => OpenBics::class],
        'closedbics' => ['name' => 'Закрытые БИК', 'class' => ClosedBics::class],
    ];
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regex_search = '/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $short_title = 'Поиск `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $full_title = 'Поиск `%s` по БИК Трекеру';
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_bic'];
}
