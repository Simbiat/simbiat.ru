<?php

declare(strict_types=1);

namespace App\Controller\UserControl;

use App\Controller\Abstracts\Page;

final class Password extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/uc/password', 'name' => 'Password'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'password';

    /**
     * @var string Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Password';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Password change';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Page to change password';

    /**
     * @var string Cache strategy: aggressive, private, live, month, week, day, hour
     */
    #[\Override]
    protected string $cache_strategy = 'private';

    /**
     * @var bool Flag indicating that authentication is required
     */
    #[\Override]
    protected bool $authentication_needed = false;

    /**
     * This is actual page generation based on further details of the $path
     *
     * @param array<int, string> $path
     *
     * @return array<string, mixed>
     */
    protected function generate(array $path): array
    {
        $output_array = ['user_id' => null, 'token' => null];
        if ($_SESSION['user_id'] === 1) {
            // Check if password reset is being attempted
            if (
                empty($path[0])
                || \preg_match('/\d+/u', $path[0]) !== 1
            ) {
                // Not authorized
                return ['http_error' => 403];
            }

            // Check token
            if (empty($path[1])) {
                return ['http_error' => 403];
            }

            $output_array = ['user_id' => $path[0], 'token' => $path[1]];
        }

        return $output_array;
    }
}
