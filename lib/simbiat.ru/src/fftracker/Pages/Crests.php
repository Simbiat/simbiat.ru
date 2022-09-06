<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Pages\FileListing;

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
        'background' => ['path' => '/img/fftracker/crests-components/backgrounds', 'name' => 'Backgrounds', 'depth' => 1],
        'frame' => ['path' => '/img/fftracker/crests-components/frames', 'name' => 'Frames'],
        'emblem' => ['path' => '/img/fftracker/crests-components/emblems', 'name' => 'Emblems'],
        'merged' => ['path' => '/img/fftracker/merged-crests', 'name' => 'Merged crests', 'depth' => 1],
    ];
    #List of prohibited extensions, files with which should be excluded
    protected array $exclude = ['LICENSE', 'README.md', '.git'];
    
    protected function extra(array &$fileDetails): void
    {
        $fileDetails['icon'] = $fileDetails['path'].'/'.$fileDetails['filename'];
        $fileDetails['name'] = $fileDetails['key'];
    }
}
