<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\BICTracker;

use Simbiat\BIC\Library;
use Simbiat\Website\Search\ClosedBics;
use Simbiat\Website\Search\OpenBics;

class Search extends \Simbiat\Website\Abstracts\Pages\Search
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
        'openbics' => ['name' => 'Открытые БИК', 'class' => OpenBics::class],
        'closedbics' => ['name' => 'Закрытые БИК', 'class' => ClosedBics::class],
    ];
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Поиск `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Поиск `%s` по БИК Трекеру';
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
    #Link to JS module for preload
    protected string $jsModule = 'bictracker/search';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['view_bic'];

    #Add any extra fields, if required by overriding this function
    protected function extras(): array
    {
        $outputArray['bicDate'] = (new Library)->bicDate();
        return $outputArray;
    }
}
