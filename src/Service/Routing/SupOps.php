<?php
declare(strict_types = 1);

namespace App\Service\Routing;

use App\Controller\SupOps\Comparison;
use App\Controller\SupOps\FACTS\Automation;
use App\Controller\SupOps\FACTS\Collaboration;
use App\Controller\SupOps\FACTS\FACTS;
use App\Controller\SupOps\FACTS\Feedback;
use App\Controller\SupOps\FACTS\Sustainability;
use App\Controller\SupOps\FACTS\Transparency;
use App\Controller\SupOps\Flow;
use App\Controller\SupOps\Glossary;
use App\Controller\SupOps\Interoperability;
use App\Controller\SupOps\Levels\L0;
use App\Controller\SupOps\Levels\L1;
use App\Controller\SupOps\Levels\L2;
use App\Controller\SupOps\Levels\L3;
use App\Controller\SupOps\Levels\L4;
use App\Controller\SupOps\Metrics;
use App\Controller\SupOps\Needs;
use App\Controller\SupOps\Pitch;
use App\Controller\SupOps\Problem;
use App\Controller\SupOps\Resolution;
use App\Controller\SupOps\Scale;
use App\Controller\SupOps\Solution;
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
