<?php
declare(strict_types=1);
namespace Simbiat\Talks;

use Simbiat\Config\Common;
use Simbiat\Database\Controller;
use Simbiat\HomePage;
use Simbiat\HTMLCut;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;
use Simbiat\HTTP20\PrettyURL;

class Show
{
    private ?Controller $dbController;

    public function __construct()
    {
        #Cache objects
        $this->dbController = HomePage::$dbController;
    }


    public function forum(array $uri): array
    {
        $currentPage = 1;
        #Sanitize pages
        if (!empty($uri[1])) {
            $currentPage = preg_replace('/\D/', '', $uri[1]);
        }
        if (empty($currentPage)) {
            Headers::redirect(Common::$baseUrl.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/forum/1');
        }
        #Get count
        $totalPages = intval(ceil($this->dbController->count('SELECT COUNT(*) FROM `talks__posts`')/20));
        if ($currentPage > $totalPages) {
            Headers::redirect(Common::$baseUrl.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/forum/'.$totalPages);
        }
        $currentPage = intval($currentPage);
        #Get articles
        $forum = $this->dbController->selectAll('SELECT * FROM `talks__posts` ORDER BY `created` DESC LIMIT 20 OFFSET '.(($currentPage-1)*20));
        #Slightly update content
        foreach ($forum as $key=>$article) {
            $forum[$key]['url'] = PrettyURL::pretty($article['threadid'].'/'.$article['title']);
            $forum[$key]['content'] = HTMLCut::Cut($article['text'], 1000, 5, '<br><a href="/thread/'.$forum[$key]['url'].'/">⋯✀⋯Read⋯more⋯✀⋯</a>');
        }
        $outputArray['articles'] = $forum;
        #Old code
        $outputArray['h1'] = 'Forum'.($currentPage > 1 ? ' (page '.$currentPage.')' : '');
        $outputArray['title'] = 'Forum'.($currentPage > 1 ? ' (page '.$currentPage.')' : '');
        $outputArray['pagination_top'] = HTML::pagination($currentPage, $totalPages, prefix: '/forum/');
        $outputArray['pagination_bottom'] = HTML::pagination($currentPage, $totalPages, prefix: '/forum/');
        $outputArray['breadcrumbs'] = [
                ['href'=>'/', 'name'=>'Home page'],
                ['href'=>'/forum/'.$currentPage, 'name'=>'Page '.$currentPage],
            ];
        $outputArray['serviceName'] = 'forum';
        return $outputArray;
    }

    public function thread(array $uri): array
    {
        if (!empty($uri[1])) {
            $threadid = preg_replace('/\D/', '', $uri[1]);
        }
        if (empty($threadid)) {
            Headers::redirect(Common::$baseUrl.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/forum/1');
        } else {
            $outputArray['article'] = $this->dbController->selectRow('SELECT * FROM `talks__posts` WHERE `threadid` = :threadid', ['threadid'=>$threadid]);
            if (empty($outputArray['article'])) {
                $outputArray['http_error'] = 404;
            } else {
                $outputArray['h1'] = $outputArray['article']['name'];
                $outputArray['title'] = 'Simbiat Software: '.$outputArray['article']['name'];
                $outputArray['article']['url'] = PrettyURL::pretty($outputArray['article']['threadid'].'/'.$outputArray['article']['title']);
                $outputArray['article']['content'] = nl2br($outputArray['article']['text']);
                $outputArray['breadcrumbs'] = [
                        ['href'=>'/', 'name'=>'Home page'],
                        ['href'=>'/forum', 'name'=>'Forum'],
                        ['href'=>'/thread/'.$outputArray['article']['threadid'], 'name'=>$outputArray['article']['name']],
                    ];
                $outputArray['serviceName'] = 'thread';
            }
            return $outputArray;
        }
        return [];
    }
}
