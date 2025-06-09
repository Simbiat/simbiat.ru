<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Config;
use Simbiat\http20\Headers;

/**
 * Forces redirect. While this can be done in web server configuration, doing it this way to reduce dependencies on it
 */
class Redirect extends StaticPage
{
    protected string $subServiceName = 'redirect';
    #Regex match pattern with / and flags
    protected string $search_for = '';
    #Regex replace pattern
    protected string $replaceWith = '';
    
    /**
     * Unlike with parent class, we are just redirecting here
     * @param array $path
     *
     * @return array
     */
    #[\Override]
    protected function generate(array $path): array
    {
        $newUri = preg_replace('/'.$this->search_for.'/ui', $this->replaceWith, Config::$canonical);
        Headers::redirect($newUri);
        return [];
    }
}
