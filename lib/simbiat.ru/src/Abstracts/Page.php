<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\HomePage;

abstract class Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [];
    #Alternative representations of the content
    protected array $altLinks = [];
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
    #Language override, to be sent in header (if present)
    protected string $language = '';
    #Flag to indicate this is a static page
    protected bool $static = false;
    #Allowed methods
    protected array $methods = ['GET', 'POST', 'HEAD', 'OPTIONS'];
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'day';

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
        #Send page language
        if (!empty($this->language)) {
            @header('Content-Language: '.$this->language);
        }
        #Generate the page only if no prior errors detected
        if (empty(HomePage::$http_error) || $this->static) {
            #Generate list of allowed methods
            $allowedMethods = (array_merge(['HEAD', 'OPTIONS', 'GET'], $this->methods));
            #Send headers
            @header('Access-Control-Allow-Methods: '.implode(', ', $allowedMethods));
            @header('Allow: '.implode(', ', $allowedMethods));
            #Check if allowed method is used
            if (!in_array(HomePage::$method, $allowedMethods)) {
                $page = ['http_error' => 405];
            } else {
                #Generate the page
                $page = $this->generate($path);
                #Send Last Modified header to potentially allow earlier exit
                if (!$this->headerSent) {
                    $this->lastModified($this->lastModified);
                }
                $page = array_merge($page, HomePage::$http_error);
            }
        } else {
            $page = HomePage::$http_error;
        }
        #Ensure properties are included
        $page['http_method'] = HomePage::$method;
        $page['breadcrumbs'] = $this->breadCrumb;
        $page['subServiceName'] = $this->subServiceName;
        $page['title'] = $this->title;
        $page['h1'] = $this->h1;
        $page['ogdesc'] = $this->ogdesc;
        $page['cacheAge'] = $this->cacheAge;
        $page['cacheStrat'] = $this->cacheStrat;
        if (!empty($this->altLinks)) {
            #Send HTTP header
            if (!HomePage::$staleReturn) {
                HomePage::$headers->links($this->altLinks);
            }
            #Add link to HTML
            $page['link_extra'] = $this->altLinks;
        }
        #Check if we are loading a static page
        $page['static_page'] = $this->static;
        #Set error for Twig
        if (!empty($page['http_error'])) {
            $page['title'] = (HomePage::$dbup === false ? 'Database unavailable' : (HomePage::$dbUpdate === true ? 'Site maintenance' : 'Error '.$page['http_error']));
            $page['h1'] = $page['title'];
            if (in_array($page['http_error'], ['database', 'maintenance'])) {
                HomePage::$headers->clientReturn('503', false);
            } else {
                HomePage::$headers->clientReturn(strval($page['http_error']), false);
            }
        }
        #Limit Ogdesc to 120 characters
        $page['ogdesc'] = mb_substr($page['ogdesc'], 0, 120, 'UTF-8');
        #Generate link for cache reset, if page uses cache
        if ($this->cacheAge > 0) {
            $page['cacheReset'] = parse_url(HomePage::$canonical);
            $page['cacheReset'] = HomePage::$canonical.(empty($page['cacheReset']['query']) ? '?cacheReset=true' : '&cacheReset=true');
        }
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
        if (!HomePage::$staleReturn) {
            HomePage::$headers->lastModified($this->lastModified, true);
        }
        #Set the flag indicating, that header was sent, but we did not exit, so that the header will not be sent the 2nd time
        $this->headerSent = true;
    }

    #Generation of the page data
    abstract protected function generate(array $path): array;
}
