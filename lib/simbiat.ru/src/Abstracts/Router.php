<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

abstract class Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = [];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = '';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = '';
    #Service name
    protected string $serviceName = '';

    public final function __construct()
    {
        #Check that subclass has set appropriate properties
        foreach (['subRoutes', 'breadCrumb'] as $property) {
            if(empty($this->{$property})) {
                throw new \LogicException(get_class($this) . ' must have a non-empty `'.$property.'` property.');
            }
        }
    }

    #This is general routing check for supported page
    public final function route(array $path): array
    {
        #Start data
        $pageData = [];
        #Main page of the segment is called
        if (empty($path)) {
            $pageData['breadcrumbs'] = $this->breadCrumb;
        } else {
            #Check if supported path
            if (in_array($path[0], $this->subRoutes)) {
                #Generate page
                $pageData = $this->pageGen($path);
                #Update breadcrumbs
                if (!empty($pageData['breadcrumbs'])) {
                    $pageData['breadcrumbs'] = array_merge($this->breadCrumb, $pageData['breadcrumbs']);
                } else {
                    $pageData['breadcrumbs'] = $this->breadCrumb;
                }
            } else {
                #Not supported
                $pageData['breadcrumbs'] = $this->breadCrumb;
                $pageData['http_error'] = 404;
            }
        }
        #Inherit title, H1 and description, if page does not have them and router does
        if (empty($pageData['title']) && !empty($this->title)) {
            $pageData['title'] = $this->title;
        }
        if (empty($pageData['h1']) && !empty($this->h1)) {
            $pageData['h1'] = $this->h1;
        }
        if (empty($pageData['ogdesc']) && !empty($this->ogdesc)) {
            $pageData['ogdesc'] = $this->ogdesc;
        }
        #Set service name if available
        if (!empty($this->serviceName)) {
            $pageData['serviceName'] = $this->serviceName;
        }
        return $pageData;
    }

    #This is actual page generation based on further details of the $path
    abstract protected function pageGen(array $path): array;
}
