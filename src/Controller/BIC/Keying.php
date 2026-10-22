<?php

declare(strict_types=1);

namespace App\Controller\BIC;

use App\Controller\Abstracts\Page;
use Simbiat\BIC\AccountKeying;

final class Keying extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/bictracker/keying', 'name' => 'Ключевание'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'keying';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Ключевание счёта';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Ключевание счёта';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Проверка корректности контрольного символа в номере счёта против номера банковского идентификационного кода';

    /**
     * @var string Language override, to be sent in header (if present)
     */
    #[\Override]
    protected string $language = 'ru-RU';

    /**
     * @var bool Flag to indicate this is a static page
     */
    #[\Override]
    protected bool $static = true;

    /**
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['view_bic'];

    /**
     * This is actual page generation based on further details of the $path
     *
     * @param array<int, string> $path
     *
     * @return array<string, mixed>
     */
    protected function generate(array $path): array
    {
        $output_array['check_result'] = null;
        $output_array['bic_value'] = null;
        $output_array['acc_value'] = null;
        $output_array['first_half'] = null;
        $output_array['second_half'] = null;
        if (
            !empty($path[0])
            && !empty($path[1])
        ) {
            $output_array['check_result'] = AccountKeying::accCheck($path[0], $path[1]);
            if ($output_array['check_result'] !== false) {
                $output_array['bic_value'] = $path[0];
                $output_array['acc_value'] = $path[1];
                $this->title = 'Ключевание счёта '.$path[1];
                $this->h1 = $this->title;
                $this->alt_links = [['href' => '/api/bictracker/keying/'.$path[0].'/'.$path[1], 'rel' => 'alternate', 'title' => 'Ссылка на API', 'type' => 'application/json; charset=utf-8']];
            }

            if (\is_numeric($output_array['check_result'])) {
                $output_array['first_half'] = \preg_replace('/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u', '$1', $path[1]);
                $output_array['second_half'] = \preg_replace('/(^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{2})(\d)(\d{11})$/u', '$3', $path[1]);
            }
        }

        return $output_array;
    }
}
