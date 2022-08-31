<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Pages\StaticPage;
use Simbiat\Config\FFTracker;

class Crests extends StaticPage
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

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Get components
        $outputArray['components']['background'] = $this->genList('background');
        $outputArray['components']['frame'] = $this->genList('frame');
        $outputArray['components']['emblem'] = $this->genList('emblem');
        return $outputArray;
    }
    
    private function genList(string $type): array
    {
        $array = [];
        $icons = array_diff(scandir(FFTracker::$crestsComponents.$type.'s/'), ['..', '.']);
        foreach ($icons as $key=>$icon) {
            $array[] = [
                'name' => ucfirst($type).' #'.$key,
                'icon' => '/img/fftracker/crests-components/'.$type.'s/'.$icon,
            ];
        }
        return $array;
    }
}
