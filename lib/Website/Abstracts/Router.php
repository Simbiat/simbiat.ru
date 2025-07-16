<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Simbiat\Website\Config;
use Simbiat\http20\Headers;
use Simbiat\Website\Images;

use function get_class, in_array;

/**
 * CLass to handle page routing while generating breadcrumbs
 */
abstract class Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = [];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [];
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = '';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = '';
    #Banner for all pages under the router. Defaults to website's banner. Needs to be inside /assets/images directory and start with /
    protected string $og_image = '';
    #Service name
    protected string $service_name = '';
    #If no path[0] is provided, but we want to show a specific page, instead of a stub - redirect to page with this address
    protected string $redirect_main = '';
    
    final public function __construct()
    {
        #Check that subclass has set appropriate properties
        foreach (['sub_routes', 'breadcrumb'] as $property) {
            if (empty($this->{$property})) {
                throw new \LogicException(get_class($this).' must have a non-empty `'.$property.'` property.');
            }
        }
    }
    
    #This is a general routing check for supported page
    final public function route(array $path): array
    {
        #Start data
        $page_data = [];
        #The main page of the segment is called
        if (empty($path)) {
            #If no path is provided, but we want to show a specific page, instead of a stub - redirect
            if (!empty($this->redirect_main) && preg_match('/^\/.+?$/u', $this->redirect_main) === 1) {
                Headers::redirect('https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Config::$http_host) === 1 ? 'www.' : '').Config::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').$this->redirect_main);
            }
            $page_data['breadcrumbs'] = $this->breadcrumb;
        } elseif (in_array($path[0], $this->sub_routes, true)) {
            #Generate page
            $page_data = $this->pageGen($path);
            #Update breadcrumbs
            if (!empty($page_data['breadcrumbs'])) {
                $page_data['breadcrumbs'] = array_merge($this->breadcrumb, $page_data['breadcrumbs']);
            } else {
                $page_data['breadcrumbs'] = $this->breadcrumb;
            }
        } else {
            #Not existent endpoint
            $page_data['breadcrumbs'] = $this->breadcrumb;
            $page_data['reason'] = 'Unsupported route';
            $page_data['http_error'] = 404;
            Headers::clientReturn($page_data['http_error'], false);
        }
        #Inherit title, H1 and description, if page does not have them and router does
        if (empty($page_data['title']) && !empty($this->title)) {
            $page_data['title'] = $this->title;
        }
        if (empty($page_data['h1']) && !empty($this->h1)) {
            $page_data['h1'] = $this->h1;
        }
        if (empty($page_data['og_desc']) && !empty($this->ogdesc)) {
            $page_data['og_desc'] = $this->ogdesc;
        }
        #Set service name if available
        if (!empty($this->service_name)) {
            $page_data['service_name'] = $this->service_name;
        }
        #Set custom og_image if available
        if (!empty($this->og_image) && empty($page_data['og_image'])) {
            $page_data = array_merge($page_data, Images::ogImage($this->og_image, true));
        }
        return $page_data;
    }
    
    /**
     * This is the actual page generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    abstract protected function pageGen(array $path): array;
}
