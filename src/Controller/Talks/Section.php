<?php

declare(strict_types=1);

namespace App\Controller\Talks;

use App\Controller\Abstracts\Page;
use App\Entity\Thread;
use App\Service\Config;
use Simbiat\http20\Headers;

final class Section extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/talks/sections/', 'name' => 'Sections'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'section';

    /**
     * @var string Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Talks';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Talks';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Talks: forums, blogs and other ways of communication';

    /**
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['view_posts'];

    /**
     * This is actual page generation based on further details of the $path
     *
     * @param array<int, string> $path
     *
     * @return array<string, mixed>
     */
    protected function generate(array $path): array
    {
        // Sanitize ID
        $id = $path[0] ?? 'top';
        if (
            $id !== 'top'
            && (int) $id < 1
        ) {
            // Redirect to top page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/sections/', permanent: false);
        }

        $output_array = new \App\Entity\Section($id)->getArray();
        if (empty($output_array['id'])) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }

        // Check if private
        if (
            $output_array['private']
            && $output_array['author'] !== $_SESSION['user_id']
            && !\in_array('view_private', $_SESSION['permissions'], strict: true)
        ) {
            return ['http_error' => 403, 'reason' => 'This section is private and you lack `view_private` permission'];
        }

        // Check if scheduled
        if (
            $output_array['created'] >= \time()
            && !\in_array('view_scheduled', $_SESSION['permissions'], strict: true)
        ) {
            return ['http_error' => 404, 'reason' => 'Section does not exist', 'suggested_link' => '/talks/sections/'];
        }

        // Generate pagination data
        $page = (int) ($_GET['page'] ?? 1);
        $output_array['pagination'] = ['current' => $page, 'total' => \max($output_array['threads']['pages'] ?? 1, $output_array['children']['pages'] ?? 1), 'prefix' => '?page='];
        if (
            $output_array['pagination']['current'] > $output_array['pagination']['total']
            && $output_array['pagination']['total'] !== 0
        ) {
            // Redirect to last page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/talks/sections/'.($id === 'top' ? '' : $id).'?page='.$output_array['pagination']['total'], permanent: false);

            return [];
        }

        // Collect times
        $times = [];
        // Add time for the current section
        if (!empty($output_array['updated'])) {
            $times[] = $output_array['updated'];
        }

        // Add children times
        if (!empty($output_array['children']['entities'])) {
            $times = \array_merge($times, \array_column($output_array['children']['entities'], 'updated'));
        }

        // Add threads times
        if (!empty($output_array['threads']['entities'])) {
            $times = \array_merge($times, \array_column($output_array['threads']['entities'], 'updated'));
        }

        // Try to exit early based on modification date
        if ($times !== []) {
            $this->lastModified(\max($times) ?? 0);
        }

        // Add section_id, to avoid ambiguity on Twig level
        $output_array['section_id'] = $output_array['id'];
        if ($output_array['id'] !== 'top') {
            // Continue breadcrumbs
            // Add parents if we have any
            foreach ($output_array['parents'] as $parent) {
                $this->breadcrumb[] = ['href' => '/talks/sections/'.$parent['section_id'], 'name' => $parent['name']];
            }

            // Add the current section
            $this->breadcrumb[] = ['href' => '/talks/sections/'.$output_array['id'], 'name' => $output_array['name']];
            // Add a page if there is one
            if ($page > 1) {
                $this->attachCrumb('?page='.$page, 'Page '.$page);
            }

            // Update title, h1 and og_desc
            $this->h1 = $output_array['name'].($page > 1 ? ', Page '.$page : '');
            $this->title = $this->h1;
            $this->og_desc = $output_array['description'] ?? ($output_array['type'].' with the name of `'.$output_array['name'].'`');
        }

        // Get section types
        if (
            $output_array['owned']
            || \in_array('add_sections', $_SESSION['permissions'], strict: true)
        ) {
            $output_array['section_types'] = \App\Entity\Section::getSectionTypes($output_array['inherited_type']);
        }

        // Get stuff for threads
        if (
            $output_array['owned']
            || \in_array('can_post', $_SESSION['permissions'], strict: true)
        ) {
            $output_array['thread_languages'] = Thread::getLanguages();
            $output_array['thread_link_types'] = Thread::getAltLinkTypes();
        }

        return $output_array;
    }
}
