<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

class Listing extends \Simbiat\Abstracts\Pages\Listing
{
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $pageWord = 'страница';
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subServiceName = 'search';
    #Service name for breadcrumbs
    protected string $serviceName = 'bictracker';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Поиск по БИК Трекеру';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Поиск по БИК Трекеру';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Поиск по БИК Трекеру';
    #Linking types to classes
    protected array $types = [
        'openbics' => ['name' => 'Открытые БИК', 'class' => '\Simbiat\bictracker\Search\OpenBics'],
        'closedbics' => ['name' => 'Закрытые БИК', 'class' => '\Simbiat\bictracker\Search\ClosedBics'],
    ];
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Поиск `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Поиск `%s` по БИК Трекеру';
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
}
