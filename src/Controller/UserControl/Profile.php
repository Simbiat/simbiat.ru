<?php

declare(strict_types=1);

namespace App\Controller\UserControl;

use App\Controller\Abstracts\Page;
use App\Entity\User;

final class Profile extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/uc/profile', 'name' => 'Profile'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'profile';

    // Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Profile';

    // Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Your profile';

    // Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Page to edit your profile';

    // Cache strategy: aggressive, private, live, month, week, day, hour
    #[\Override]
    protected string $cache_strategy = 'private';

    // Flag indicating that authentication is required
    #[\Override]
    protected bool $authentication_needed = true;

    // This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $output_array = [];
        $output_array['user_data'] = new User($_SESSION['user_id'])->getArray();
        $now = new \DateTimeImmutable();
        $output_array['timezones'] = [];
        foreach (\timezone_identifiers_list() as $timezone) {
            $now = $now->setTimezone(new \DateTimeZone($timezone));
            $output_array['timezones'][$timezone] = ['offset' => \sprintf('%+03d:%02d', (int) ($now->getOffset() / 3600), \abs((int) ($now->getOffset() % 3600 / 60))), 'current' => ($timezone === $output_array['user_data']['timezone'])];
        }

        return $output_array;
    }
}
