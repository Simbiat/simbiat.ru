<?php

declare(strict_types=1);

namespace App\Controller\FFXIV;

use App\Controller\Abstracts\FileListing;

final class Crests extends FileListing
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/crests', 'name' => 'Crests'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'crests';

    /**
     * @var string Page title. Practically needed only for main pages of a segment since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Crests';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Crests';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = "List of all Final Fantasy XIV crests' components and crests themselves, presented as single images.";

    /**
     * @var bool Flag whether to go recursive or not
     */
    #[\Override]
    protected bool $recursive = true;

    /**
     * @var array<string, array<string, int|string>> Directories relative to working dir
     */
    #[\Override]
    protected array $dirs = [
        'background' => ['path' => '/public/assets/images/fftracker/crests-components/backgrounds', 'name' => 'Backgrounds', 'depth' => 1],
        'emblem' => ['path' => '/public/assets/images/fftracker/crests-components/emblems', 'name' => 'Emblems', 'depth' => 1],
        'frame' => ['path' => '/public/assets/images/fftracker/crests-components/frames', 'name' => 'Frames'],
        'merged' => ['path' => '/var/mergedcrests', 'name' => 'Merged crests (cached)', 'depth' => 1],
    ];

    /**
     * @var array<int, string> List of prohibited extensions, files with which should be excluded
     */
    #[\Override]
    protected array $exclude = ['LICENSE', 'README.md', '.git'];

    /**
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['view_ff'];

    protected function extra(array &$file_details): void
    {
        $file_details['icon'] = \str_replace('/public/assets/images/fftracker/crests-components', '/assets/images/fftracker/crests-components', \str_replace('/public/assets/', '/assets/', \str_replace('/var/mergedcrests', '/assets/images/fftracker/merged-crests', $file_details['path']))).'/'.$file_details['filename'];
        $file_details['name'] = (\str_contains($file_details['path'], 'merged') ? $file_details['key'] : $file_details['filename']);
    }
}
