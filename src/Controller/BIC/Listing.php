<?php

declare(strict_types=1);

namespace App\Controller\BIC;

use App\Service\Search\ClosedBics;
use App\Service\Search\OpenBics;

final class Listing extends \App\Controller\Abstracts\Listing
{
    /**
     * @var string How pages are called (at the moment required only for bictracker for translation)
     */
    #[\Override]
    protected string $page_word = 'страница';

    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/bictracker/search', 'name' => 'Поиск'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'search';

    /**
     * @var string Service name for breadcrumbs
     */
    #[\Override]
    protected string $service_name = 'bictracker';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Поиск по БИК Трекеру';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Поиск по БИК Трекеру';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Поиск по БИК Трекеру';

    /**
     * @var array<string, array<string, string> Linking types to classes
     */
    #[\Override]
    protected array $types = [
        'closedbics' => ['name' => 'Закрытые БИК', 'class' => ClosedBics::class],
        'openbics' => ['name' => 'Открытые БИК', 'class' => OpenBics::class],
    ];

    /**
     * @var string Regex to sanitize search value (remove disallowed characters)
     */
    #[\Override]
    protected string $regex_search = '/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<> ]/i';

    /**
     * @var string Short title to be used for <title> and <h1> when having a search value
     */
    #[\Override]
    protected string $short_title = 'Поиск `%s`';

    /**
     * @var string Full title to be used for description metatags when having a search value
     */
    #[\Override]
    protected string $full_title = 'Поиск `%s` по БИК Трекеру';

    /**
     * @var string Language override, to be sent in header (if present)
     */
    #[\Override]
    protected string $language = 'ru-RU';

    /**
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['view_bic'];
}
