<?php
declare(strict_types = 1);

namespace Simbiat\Abstracts\Pages;

use Simbiat\HomePage;
use Simbiat\http20\Headers;

/**
 * Forces redirect. While this can be done in web server configuration, doing it this way to reduce dependencies on it
 */
class Redirect extends StaticPage
{
    protected string $subServiceName = 'redirect';
    #Regex match pattern with / and flags
    protected string $searchFor = '';
    #Regex replace pattern
    protected string $replaceWith = '';
    
    #Used simply to force redirect
    protected function generate(array $path): array
    {
        $newUri = preg_replace('/'.$this->searchFor.'/ui', $this->replaceWith, HomePage::$canonical);
        Headers::redirect($newUri);
        return [];
    }
}
