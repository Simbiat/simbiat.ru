<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\SupOps\FACTS\FACTS;
use Simbiat\Website\Pages\SupOps\FACTS\Feedback;
use Simbiat\Website\Pages\SupOps\FACTS\Automation;
use Simbiat\Website\Pages\SupOps\FACTS\Collaboration;
use Simbiat\Website\Pages\SupOps\FACTS\Transparency;
use Simbiat\Website\Pages\SupOps\FACTS\Sustainability;
use Simbiat\Website\Pages\SupOps\Flow;
use Simbiat\Website\Pages\SupOps\Glossary;
use Simbiat\Website\Pages\SupOps\Levels\L0;
use Simbiat\Website\Pages\SupOps\Levels\L1;
use Simbiat\Website\Pages\SupOps\Levels\L2;
use Simbiat\Website\Pages\SupOps\Levels\L3;
use Simbiat\Website\Pages\SupOps\Levels\L4;
use Simbiat\Website\Pages\SupOps\Metrics;
use Simbiat\Website\Pages\SupOps\Pitch;
use Simbiat\Website\Pages\SupOps\Problem;
use Simbiat\Website\Pages\SupOps\Resolution;
use Simbiat\Website\Pages\SupOps\Scale;
use Simbiat\Website\Pages\SupOps\Solution;
use Simbiat\Website\Pages\SupOps\Interoperability;
use Simbiat\Website\Pages\SupOps\Comparison;
use Simbiat\Website\Pages\SupOps\Needs;
use function array_slice;

class SupOps extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['pitch', 'glossary', 'problem', 'solution', 'flow', 'metrics', 'resolution', 'interoperability', 'scale', 'comparison', 'needs',
        'facts', 'feedback', 'automation', 'collaboration', 'transparency', 'sustainability',
        'l0', 'l1', 'l2', 'l3', 'l4',];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/', 'name' => 'SupOps']
    ];
    protected string $title = 'SupOps';
    protected string $h1 = 'SupOps';
    protected string $og_desc = 'SupOps: inspired by DevOps to reduce your customers\' frustrations with tech support';
    protected string $service_name = 'supops';
    protected string $og_image = '/supops/logo/ogimage.webp';
    
    /**
     * This is the actual page generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            '', 'pitch' => new Pitch()->get(array_slice($path, 1)),
            'problem' => new Problem()->get(array_slice($path, 1)),
            'solution' => new Solution()->get(array_slice($path, 1)),
            'glossary' => new Glossary()->get(array_slice($path, 1)),
            'facts' => new FACTS()->get(array_slice($path, 1)),
            'feedback' => new Feedback()->get(array_slice($path, 1)),
            'automation' => new Automation()->get(array_slice($path, 1)),
            'collaboration' => new Collaboration()->get(array_slice($path, 1)),
            'transparency' => new Transparency()->get(array_slice($path, 1)),
            'sustainability' => new Sustainability()->get(array_slice($path, 1)),
            'l0' => new L0()->get(array_slice($path, 1)),
            'l1' => new L1()->get(array_slice($path, 1)),
            'l2' => new L2()->get(array_slice($path, 1)),
            'l3' => new L3()->get(array_slice($path, 1)),
            'l4' => new L4()->get(array_slice($path, 1)),
            'flow' => new Flow()->get(array_slice($path, 1)),
            'metrics' => new Metrics()->get(array_slice($path, 1)),
            'resolution' => new Resolution()->get(array_slice($path, 1)),
            'interoperability' => new Interoperability()->get(array_slice($path, 1)),
            'scale' => new Scale()->get(array_slice($path, 1)),
            'comparison' => new Comparison()->get(array_slice($path, 1)),
            'needs' => new Needs()->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.\implode('`, `', $this->sub_routes).'`.'],
        };
    }
}
