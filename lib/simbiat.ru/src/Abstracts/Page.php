<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\Config\Common;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\HTTP20\Headers;

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
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = false;
    #Link to JS module for preload
    protected string $jsModule = '';

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
            #Check that user is authenticated
            } elseif ($this->authenticationNeeded && empty($_SESSION['userid'])) {
                $page = ['http_error' => 403];
            } else {
                #Generate the page
                try {
                    $page = $this->generate($path);
                } catch (\Throwable $exception) {
                    if (preg_match('/(ID `.*` for entity `.*` has incorrect format\.)|(ID can\'t be empty\.)/ui', $exception->getMessage()) === 1) {
                        $page = ['http_error' => 400, 'reason' => $exception->getMessage()];
                    } else {
                        Errors::error_log($exception);
                        $page = ['http_error' => 500, 'reason' => 'Unknown error occurred'];
                    }
                }
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
        if (!empty($this->altLinks) || !empty($this->jsModule)) {
            if (!empty($this->jsModule)) {
                $this->altLinks = array_merge($this->altLinks, [['rel' => 'modulepreload', 'href' => '/js/Pages/'.$this->jsModule.'.'.filemtime(Common::$jsDir.'/Pages/'.$this->jsModule.'.js').'.js', 'as' => 'script']]);
            }
            #Send HTTP header
            if (!HomePage::$staleReturn) {
                Headers::links($this->altLinks);
            }
            #Add link to HTML
            $page['link_extra'] = $this->altLinks;
        }
        #Check if we are loading a static page
        $page['static_page'] = $this->static;
        #Set error for Twig
        if (!empty($page['http_error'])) {
            //$page['h1'] .= ' ('.(HomePage::$dbup === false ? 'Database unavailable' : (HomePage::$dbUpdate === true ? 'Site maintenance' : 'Error '.$page['http_error'])).')';
            if (in_array($page['http_error'], ['database', 'maintenance'])) {
                Headers::clientReturn('503', false);
            } else {
                Headers::clientReturn(strval($page['http_error']), false);
            }
        }
        #Limit Ogdesc to 120 characters
        $page['ogdesc'] = mb_substr($page['ogdesc'], 0, 120, 'UTF-8');
        #Generate link for cache reset, if page uses cache
        if ($this->cacheAge > 0 && $this->static === false) {
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
            Headers::lastModified($this->lastModified, true);
        }
        #Set the flag indicating, that header was sent, but we did not exit, so that the header will not be sent the 2nd time
        $this->headerSent = true;
    }
    
    #Function to append a breadcrumb, which is based on last crumb currently set
    protected final function attachCrumb(string $path, string $name, bool $query = false): void
    {
        #Add path to breadcrumbs
        $this->breadCrumb[] = [
            'href' => $this->breadCrumb[array_key_last($this->breadCrumb)]['href'].($query ? '&' : '/').$path,
            'name' => $name,
        ];
    }
    
    #Function to get last breadcrumb's href
    protected final function getLastCrumb(): string
    {
        return $this->breadCrumb[array_key_last($this->breadCrumb)]['href'];
    }
    
    #Generation of the page data
    abstract protected function generate(array $path): array;
}
