<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Common;
use Simbiat\HTMLCut;
use Simbiat\Security;

class Post extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/talks/posts/', 'name'=>'Posts']
    ];
    #Sub service name
    protected string $subServiceName = 'post';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Talks';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Talks';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Talks';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Sanitize ID
        $id = $path[0] ?? null;
        if (empty($id) || intval($id) < 1) {
            return ['http_error' => 400, 'reason' => 'Wrong ID'];
        }
        $outputArray = (new \Simbiat\Talks\Entities\Post($id))->getArray();
        if (empty($outputArray['id']) || empty($outputArray['text'])) {
            return ['http_error' => 404, 'reason' => 'Post does not exist', 'suggested_link' => '/talks/sections/'];
        }
        #Check if private
        if ($outputArray['private'] && !in_array(1, $_SESSION['groups']) && $outputArray['createdBy'] !== $_SESSION['userid']) {
            return ['http_error' => 403, 'reason' => 'This is a private thread'];
        }
        #Try to exit early based on modification date
        $this->lastModified($outputArray['updated']);
        #Reset crumbs (we do not have "posts" list)
        $this->breadCrumb = [];
        #Add parents
        foreach ($outputArray['parents'] as $parent) {
            $this->breadCrumb[] = ['href' => '/talks/sections/'.$parent['sectionid'], 'name' => $parent['name']];
        }
        #Add thread
        $this->breadCrumb[] = ['href' => '/talks/threads/'.$outputArray['threadid'], 'name' => $outputArray['name']];
        #Add current page
        $this->breadCrumb[] = ['href' => '/talks/posts/'.$outputArray['id'], 'name' => '#'.$outputArray['id']];
        #Update title, h1 and ogdesc
        $this->title = $this->h1 = 'Post #'.$outputArray['id'];
        $this->ogdesc = HTMLCut::Cut(Security::sanitizeHTML($outputArray['text'], true), 160, 1);
        #Duplicate the array to `post` key (required for Twig template and consistency with other pages)
        $outputArray['post'] = $outputArray;
        #Add flag to hide post ID
        $outputArray['post']['nopostid'] = true;
        #Set ogtype
        $outputArray['ogtype'] = 'article';
        #Add article open graph tags
        $outputArray['ogextra'] =
            '<meta property="article:published_time" content="'.date('c', $outputArray['created']).'" />
            <meta property="article:modified_time" content="'.date('c', $outputArray['updated']).'" />'.
            ($outputArray['createdby'] === 1 ? '' : '<meta property="article:author" content="'.Common::$baseUrl.'/talks/user/'.$outputArray['createdby'].'" />').
            ($outputArray['updatedby'] !== 1 && $outputArray['updatedby'] !== $outputArray['createdby'] ? '<meta property="article:author" content="'.Common::$baseUrl.'/talks/user/'.$outputArray['createdby'].'" />' : '').
            '<meta property="article:section" content="'.$outputArray['name'].'" />'
        ;
        return $outputArray;
    }
}
