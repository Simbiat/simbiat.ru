<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Abstracts\Pages\FileListing;

class Crests extends FileListing
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/crests', 'name' => 'Crests']
    ];
    #Sub service name
    protected string $subServiceName = 'crests';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Crests';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Crests';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'List of all Final Fantasy XIV crests\' components and crests themselves, presented as single images.';
    #Flag whether to go recursive or not
    protected bool $recursive = true;
    #Directories relative to working dir
    protected array $dirs = [
        'background' => ['path' => '/lib/FFXIV/CrestComponents/backgrounds', 'name' => 'Backgrounds', 'depth' => 1],
        'frame' => ['path' => '/lib/FFXIV/CrestComponents/frames', 'name' => 'Frames'],
        'emblem' => ['path' => '/lib/FFXIV/CrestComponents/emblems', 'name' => 'Emblems', 'depth' => 1],
        'merged' => ['path' => '/data/mergedcrests', 'name' => 'Merged crests (cached)', 'depth' => 1],
    ];
    #List of prohibited extensions, files with which should be excluded
    protected array $exclude = ['LICENSE', 'README.md', '.git'];
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['view_ff'];
    
    protected function extra(array &$fileDetails): void
    {
        $fileDetails['icon'] = str_replace('/lib/FFXIV/CrestComponents', '/assets/images/fftracker/crests-components', str_replace('/public/assets/', '/assets/', str_replace('/data/mergedcrests', '/assets/images/fftracker/merged-crests', $fileDetails['path']))).'/'.$fileDetails['filename'];
        $fileDetails['name'] = (str_contains($fileDetails['path'], 'merged') ? $fileDetails['key'] : $fileDetails['filename']);
    }
}
