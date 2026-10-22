<?php

declare(strict_types=1);

namespace App\Controller\FFXIV\Statistics;

use App\Controller\Abstracts\Page;
use App\Service\Config;

abstract class General extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics', 'name' => 'Statistics'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'statistics';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Statistics';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Statistics';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Statistics';

    /**
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['view_ff'];

    #[\Override]
    protected bool $static = true;

    /**
     * @var string List of JSON files, that we need to try to ingest
     */
    protected string $json_to_ingest = '';

    /**
     * This is actual page generation based on further details of the $path
     *
     * @param array<int, string> $path
     *
     * @return array<string, mixed>
     */
    protected function generate(array $path): array
    {
        $output_array = [];

        // Attempt to ingest dats from JSON file
        try {
            if (empty($this->json_to_ingest)) {
                return ['http_error' => 500, 'reason' => 'No JSON file defined for category'];
            }

            $output_array['ffstats']['category'] = $this->json_to_ingest;
            if (!\is_file(Config::$statistics.$this->json_to_ingest.'.json')) {
                return ['http_error' => 500, 'reason' => 'File `'.$this->json_to_ingest.'.json` not found'];
            }

            $output_array['ffstats']['data'] = \json_decode(\file_get_contents(Config::$statistics.$this->json_to_ingest.'.json'), flags: \JSON_THROW_ON_ERROR | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_BIGINT_AS_STRING | \JSON_OBJECT_AS_ARRAY);
            $this->lastModified($output_array['ffstats']['data']['time'] ?? 0);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to read `'.$this->json_to_ingest.'.json` file'];
        }

        // Placeholder
        return $output_array;
    }
}
