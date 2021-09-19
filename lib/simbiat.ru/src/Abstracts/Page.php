<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

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

    public final function __construct()
    {
        #Check that subclass has set appropriate properties
        foreach (['subServiceName', 'breadCrumb'] as $property) {
            if(empty($this->{$property})) {
                throw new \LogicException(get_class($this) . ' must have a non-empty `'.$property.'` property.');
            }
        }
    }

    public final function get(array $path): array
    {
        #Generate the page
        $page = $this->generate($path);
        #Ensure properties are included
        $page['breadcrumbs'] = $this->breadCrumb;
        $page['subServiceName'] = $this->subServiceName;
        $page['title'] = $this->title;
        $page['h1'] = $this->h1;
        $page['ogdesc'] = $this->ogdesc;
        $page['cacheAge'] = $this->cacheAge;
        return $page;
    }

    #Generation of the page data
    abstract protected function generate(array $path): array;
}
