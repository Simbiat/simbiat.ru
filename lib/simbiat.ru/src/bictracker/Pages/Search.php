<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\bictracker\Library;

class Search extends \Simbiat\Abstracts\Pages\Search
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск']
    ];
    #Sub service name
    protected string $subServiceName = 'search';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Поиск по БИК Трекеру';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Поиск по БИК Трекеру';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Поиск по БИК Трекеру';
    #Linking types to classes
    protected array $types = [
        'openBics' => '\Simbiat\bictracker\Search\OpenBics',
        'closedBics' => '\Simbiat\bictracker\Search\ClosedBics',
    ];
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Поиск `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Поиск `%s` по БИК Трекеру';

    #Add any extra fields, if required by overriding this function
    protected function extras(): array
    {
        $outputArray['bicDate'] = (new Library)->bicDate();
        return $outputArray;
    }
}
