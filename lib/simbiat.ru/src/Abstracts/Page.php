<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\HTTP20\Headers;

abstract class Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [];
    #Sub service name
    protected string $subServiceName = '';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = '';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = '';
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 0;
    #Time of last data modification (defaults to current time on initialization)
    protected int $lastModified = 0;
    #Flag to check if Last Modified header was sent already
    protected bool $headerSent = false;

    public final function __construct()
    {
        #Check that subclass has set appropriate properties
        foreach (['subServiceName', 'breadCrumb'] as $property) {
            if(empty($this->{$property})) {
                throw new \LogicException(get_class($this) . ' must have a non-empty `'.$property.'` property.');
            }
        }
        #Set last modified data
        $this->lastModified = time();
    }

    public final function get(array $path): array
    {
        #Generate the page
        $page = $this->generate($path);
        #Send Last Modified header to potentially allow earlier exit
        if (!$this->headerSent) {
            $this->lastModified($this->lastModified);
        }
        #Ensure properties are included
        $page['breadcrumbs'] = $this->breadCrumb;
        $page['subServiceName'] = $this->subServiceName;
        $page['title'] = $this->title;
        $page['h1'] = $this->h1;
        $page['ogdesc'] = $this->ogdesc;
        $page['cacheAge'] = $this->cacheAge;
        return $page;
    }

    protected final function lastModified(int|string $time = 0): void
    {
        #Convert string to int
        if (is_string($time)) {
            $time = strtotime($time);
        }
        #If time is less than 0, use Last Modified set initially
        if ($time <= 0) {
            $time = $this->lastModified;
        }
        #Set Last Modified to the time
        $this->lastModified = $time;
        #Send the header
        (new Headers)->lastModified($this->lastModified, true);
        #Set the flag indicating, that header was sent, but we did not exit, so that the header will not be sent the 2nd time
        $this->headerSent = true;
    }

    #Generation of the page data
    abstract protected function generate(array $path): array;
}
