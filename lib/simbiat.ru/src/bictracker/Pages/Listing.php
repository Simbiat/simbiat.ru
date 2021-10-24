<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

use Simbiat\bictracker\Library;

class Listing extends \Simbiat\Abstracts\Pages\Listing
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker', 'name' => 'БИК Трекер']
    ];
    #Sub service name
    protected string $subServiceName = 'open';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Поиск по БИК Трекеру';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Поиск по БИК Трекеру';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Поиск по БИК Трекеру';
    #Linking types to classes
    protected array $types = [
        'open' => ['name' => 'Открытые БИК', 'class' => '\Simbiat\bictracker\Search\OpenBics'],
        'closed' => ['name' => 'Закрытые БИК', 'class' => '\Simbiat\bictracker\Search\ClosedBics'],
    ];
    #Regex to sanitize search value (remove disallowed characters)
    protected string $regexSearch = '/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/i';
    #Short title to be used for <title> and <h1> when having a search value
    protected string $shortTitle = 'Поиск `%s`';
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Поиск `%s` по БИК Трекеру';
    #How pages are called (at the moment required only for bictracker for translation)
    protected string $pageWord = 'страница';

    #Add any extra fields, if required by overriding this function
    protected function extras(): array
    {
        $outputArray['bicDate'] = (new Library)->bicDate();
        return $outputArray;
    }
}
