<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Database\Controller;
use Simbiat\HTTP20\Atom;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\RSS;
use Simbiat\HTTP20\Sitemap;

class Feeds
{
    #Fcuntion to parse URI and generate appropriate feed
    /**
     * @throws \Exception
     */
    public function uriParse(array $uri): array
    {
        if (empty($uri[0])) {
            return ['http_error' => 404];
        } else {
            return match($uri[0]) {
                'sitemap' => $this->sitemap(array_slice($uri, 1)),
                'atom' => $this->feed(array_slice($uri, 1)),
                'rss' => $this->feed(array_slice($uri, 1), 'rss'),
                default => ['http_error' => 404],
            };
        }
    }

    #Generate Atom/RSS

    /**
     * @throws \Exception
     */
    private function feed(array $uri, string $format = 'atom'): array
    {
        #Check if empty
        if (!empty($uri[0])) {
            $title = match($uri[0]) {
                'bicchanged' => 'Изменения банков',
                'bicdeleted' => 'Удаленные банки',
                default => '',
            };
            $settings = [];
            #Check that type is supported based on existence of the title
            if (!empty($title)) {
                #Set general settings first for feeds. Using one array for both types of feeds
                if ($format === 'atom') {
                    $settings = [
                        'authors' => [[
                            'name' => $GLOBALS['siteconfig']['adminname'],
                            'email' => $GLOBALS['siteconfig']['adminmail'],
                            'uri' => 'https://'.$_SERVER['HTTP_HOST'].'/',
                        ]],
                        'icon' => 'https://'.$_SERVER['HTTP_HOST'].'/img/favicons/simbiat.png',
                        'logo' => 'https://'.$_SERVER['HTTP_HOST'].'/img/favicons/ogimage.png',
                    ];
                } elseif ($format === 'rss') {
                    $settings = [
                        'webMaster' => $GLOBALS['siteconfig']['adminmail'],
                        'managingEditor' => $GLOBALS['siteconfig']['adminmail'],
                        'language' => 'en-us',
                        'ttl' => 3600,
                        'image' => [
                            'url' => 'https://'.$_SERVER['HTTP_HOST'].'/img/favicons/android/144x144.png',
                            'width' => 144,
                            'height' => 144,
                        ],
                    ];
                }
                #Set description
                $description = match($uri[0]) {
                    'bicchanged' => 'Последние 25 изменений банков',
                    'bicdeleted' => 'Последние 25 удаленных банков',
                    default => '',
                };
                #Add it to settings
                if (!empty($description)) {
                    if ($format === 'atom') {
                        $settings['subtitle'] = $description;
                    } elseif ($format === 'rss') {
                        $settings['description'] = $description;
                    }
                }
                #Change language for BICs
                if ($format === 'rss' && in_array($uri[0], ['bicchanged', 'bicdeleted'])) {
                    $settings['language'] = 'ru-ru';
                }
                #Set query for the feed
                if ($format === 'atom') {
                    $query = match($uri[0]) {
                        'bicchanged' => 'SELECT CONCAT(\'https://'.$_SERVER['HTTP_HOST'].'/bictracker/bic/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `updated`, \'Центральный Банк Российской Федерации\' AS `author_name`, \'https://cbr.ru/\' AS `author_uri`, `NameP` as `summary`, `Updated` as `published`, \'Центральный Банк Российской Федерации\' AS `source_title`, \'https://cbr.ru/\' AS `source_id`, `Updated` as `source_updated` FROM `bic__list` a WHERE `DateOut` IS NULL ORDER BY `Updated` DESC LIMIT 25',
                        'bicdeleted' => 'SELECT CONCAT(\'https://'.$_SERVER['HTTP_HOST'].'/bictracker/bic/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `updated`, \'Центральный Банк Российской Федерации\' AS `author_name`, \'https://cbr.ru/\' AS `author_uri`, `NameP` as `summary`, `Updated` as `published`, \'Центральный Банк Российской Федерации\' AS `source_title`, \'https://cbr.ru/\' AS `source_id`, `Updated` as `source_updated` FROM `bic__list` a WHERE `DateOut` IS NOT NULL ORDER BY `DateOut` DESC LIMIT 25',
                    };
                } elseif ($format === 'rss') {
                    $query = match($uri[0]) {
                        'bicchanged' => 'SELECT CONCAT(\'https://'.$_SERVER['HTTP_HOST'].'/bictracker/bic/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `pubDate`, \'BICs\' AS `category` FROM `bic__list` a WHERE `DateOut` IS NULL ORDER BY `Updated` DESC LIMIT 25',
                        'bicdeleted' => 'SELECT CONCAT(\'https://'.$_SERVER['HTTP_HOST'].'/bictracker/bic/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `pubDate`, \'BICs\' AS `category` FROM `bic__list` a WHERE `DateOut` IS NOT NULL ORDER BY `DateOut` DESC LIMIT 25',
                    };

                }
                #Generate the feed
                if ($format === 'atom') {
                    (new Atom)->Atom($GLOBALS['siteconfig']['site_name'].': '.$title, HomePage::$dbController->selectAll($query), feed_settings: $settings);
                } elseif ($format === 'rss') {
                    (new RSS)->RSS($GLOBALS['siteconfig']['site_name'].': '.$title, HomePage::$dbController->selectAll($query), feed_settings: $settings);
                }
            }
        }
        #If we reach here, it means, the requested page does not exist
        return ['http_error' => 404];
    }

    #Generate sitemap

    /**
     * @throws \Exception
     */
    private function sitemap(array $uri): array
    {
        #Cache Headers object
        $headers = HomePage::$headers;
        #Check that not empty
        if (empty($uri)) {
            #Redirect to HTML index
            if (!HomePage::$staleReturn) {
                $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/sitemap/html/index', true, true, false);
            }
        } else {
            #Check that format was provided
            if (empty($uri[0])) {
                #Check for Accept header
                if (!HomePage::$staleReturn) {
                    $format = $headers->notAccept(['application/xml', 'text/xml', 'text/plain', 'text/html']);
                }
                $format = match($format) {
                    'application/xml', 'text/xml' => 'xml',
                    'text/plain' => 'txt',
                    'text/html' => 'html',
                };
                #Redirect to index page based on acceptable headers
                if (!HomePage::$staleReturn) {
                    $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/sitemap/' . $format . '/index', true, true, false);
                }
            } else {
                if (in_array($uri[0], ['html', 'xml', 'txt'])) {
                    if (!HomePage::$staleReturn) {
                        $format = match ($uri[0]) {
                            'html' => $headers->notAccept(['text/html']),
                            'txt' => $headers->notAccept(['text/plain']),
                            'xml' => $headers->notAccept(['application/xml']),
                        };
                    }
                    #Check if initial page was provided
                    if (empty($uri[1])) {
                        #Redirect to index
                        if (!HomePage::$staleReturn) {
                            $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/sitemap/' . $uri[0] . '/index', true, true, false);
                        }
                    } else {
                        #Set base URL
                        $baseurl = 'https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/';
                        #Prepare list of links
                        $links = [];
                        if ($uri[1] === 'index') {
                            $links = $this->sitemapIndex($baseurl.'sitemap/'.$uri[0].'/');
                        } elseif ($uri[1] === 'general') {
                            #Static links
                            $links = [
                                ['loc'=>$baseurl, 'name'=>'Home Page'],
                                ['loc'=>$baseurl.'bic/search/', 'name'=>'BIC Tracker Search'],
                                ['loc'=>$baseurl.'fftracker/search/', 'name'=>'FFXIV Tracker Search'],
                                ['loc'=>$baseurl.'sitemap/xml/index/', 'name'=>'XML Sitemap'],
                                ['loc'=>$baseurl.'sitemap/html/index/', 'name'=>'HTML Sitemap'],
                                ['loc'=>$baseurl.'sitemap/txt/index/', 'name'=>'TXT Sitemap'],
                                ['loc'=>$baseurl.'about/me/', 'name'=>'About me'],
                                ['loc'=>$baseurl.'about/website/', 'name'=>'About website'],
                                ['loc'=>$baseurl.'about/tech/', 'name'=>'Technology used'],
                                ['loc'=>$baseurl.'about/resume/', 'name'=>'Resume'],
                                ['loc'=>$baseurl.'about/contacts/', 'name'=>'Contacts'],
                                ['loc'=>$baseurl.'about/tos/', 'name'=>'Terms of Service'],
                                ['loc'=>$baseurl.'about/privacy/', 'name'=>'Privacy Policy'],
                                ['loc'=>$baseurl.'about/security/', 'name'=>'Security Policy'],
                                ['loc'=>$baseurl.'about/changelog/', 'name'=>'Changelog'],
                                ['loc'=>$baseurl.'fftracker/statistics/genetics/', 'changefreq' => 'weekly', 'name'=>'Genetics'],
                                ['loc'=>$baseurl.'fftracker/statistics/astrology/', 'changefreq' => 'weekly', 'name'=>'Astrology'],
                                ['loc'=>$baseurl.'fftracker/statistics/characters/', 'changefreq' => 'weekly', 'name'=>'Characters'],
                                ['loc'=>$baseurl.'fftracker/statistics/freecompanies/', 'changefreq' => 'weekly', 'name'=>'Free Companies'],
                                ['loc'=>$baseurl.'fftracker/statistics/cities/', 'changefreq' => 'weekly', 'name'=>'Cities'],
                                ['loc'=>$baseurl.'fftracker/statistics/grandcompanies/', 'changefreq' => 'weekly', 'name'=>'Grand Companies'],
                                ['loc'=>$baseurl.'fftracker/statistics/servers/', 'changefreq' => 'weekly', 'name'=>'Servers'],
                                ['loc'=>$baseurl.'fftracker/statistics/achievements/', 'changefreq' => 'weekly', 'name'=>'Achievements'],
                                ['loc'=>$baseurl.'fftracker/statistics/timelines/', 'changefreq' => 'weekly', 'name'=>'Timelines'],
                                ['loc'=>$baseurl.'fftracker/statistics/other/', 'changefreq' => 'weekly', 'name'=>'Other'],
                            ];
                        } else {
                            if (empty($uri[2])) {
                                #Redirect to 1st page
                                if (!HomePage::$staleReturn) {
                                    $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/sitemap/' . $uri[0] . '/' . $uri[1] . '/1', true, true, false);
                                }
                            } else {
                                if (is_numeric($uri[2])) {
                                    #Get links
                                    $uri[2] = intval($uri[2]);
                                    $links = match($uri[1]) {
                                        'bic' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'bictracker/bic/\', `BIC`, \'/\') AS `loc`, `Updated` AS `lastmod`, `NameP` AS `name` FROM `bic__list` ORDER BY `NameP` ASC LIMIT '.$uri[2].', 50000'),
                                        'forum' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'thread/\', `threadid`, \'/\') AS `loc`, `date` AS `lastmod`, `title` AS `name` FROM `talks__thread` ORDER BY `title` ASC LIMIT '.$uri[2].', 50000'),
                                        'ff_achievement' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'fftracker/achievement/\', `achievementid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__achievement` FORCE INDEX (`name_order`) ORDER BY `name` ASC LIMIT '.$uri[2].', 50000'),
                                        'ff_character' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'fftracker/character/\', `characterid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__character` FORCE INDEX (`name_order`) ORDER BY `name` ASC LIMIT '.$uri[2].', 50000'),
                                        'ff_freecompany' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'fftracker/freecompany/\', `freecompanyid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__freecompany` FORCE INDEX (`name_order`) ORDER BY `name` ASC LIMIT '.$uri[2].', 50000'),
                                        'ff_linkshell' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'fftracker/linkshell/\', `linkshellid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__linkshell` FORCE INDEX (`name_order`) WHERE `crossworld` = 0 ORDER BY `name` ASC LIMIT '.$uri[2].', 50000'),
                                        'ff_crossworldlinkshell' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'fftracker/crossworld_linkshell/\', `linkshellid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__linkshell` FORCE INDEX (`name_order`) WHERE `crossworld` = 1 ORDER BY `name` ASC LIMIT '.$uri[2].', 50000'),
                                        'ff_pvpteam' => HomePage::$dbController->selectAll('SELECT CONCAT(\''.$baseurl.'fftracker/pvpteam/\', `pvpteamid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__pvpteam` FORCE INDEX (`name_order`) ORDER BY `name` ASC LIMIT '.$uri[2].', 50000'),
                                        default => [],
                                    };
                                }
                            }
                        }
                        if (!empty($links) && is_array($links)) {
                            #Send alternate links. Not using `links()`, because we need to ensure only `alternate` links for the sitemap are sent
                            $linkHeader = [];
                            #Generate string
                            foreach (['html', 'txt', 'xml'] as $type) {
                                if ($uri[0] !== $type) {
                                    #Have to use `match` due to need of different MIME types
                                    $linkHeader[] = match($type) {
                                       'html' => '<'.$baseurl.str_ireplace($uri[0], $type, $_SERVER['REQUEST_URI']).'>; title="HTML Version"; rel="alternate"; type="text/html"',
                                       'txt' => '<'.$baseurl.str_ireplace($uri[0], $type, $_SERVER['REQUEST_URI']).'>; title="Text Version"; rel="alternate"; type="text/plain"',
                                       'xml' => '<'.$baseurl.str_ireplace($uri[0], $type, $_SERVER['REQUEST_URI']).'>; title="XML Version"; rel="alternate"; type="application/xml"',
                                    };
                                }
                            }
                            @header('Link: '.implode(', ', $linkHeader), true);
                            #Return sitemap
                            (new Sitemap)->sitemap($links, ($uri[0] === 'xml' && $uri[1] === 'index' ? 'index' : $uri[0]), true);
                        }
                    }
                }
            }
        }
        #If we reach here, it means, the requested page does not exist
        return ['http_error' => 404];
    }

    #Helper function to generate index page for sitemap

    /**
     * @throws \Exception
     */
    private function sitemapIndex(string $baseurl): array
    {
        #Sitemap for general links (non-countable)
        $links = [
            ['loc'=>$baseurl.'general/', 'name'=>'General links'],
        ];
        #Get countable links
        $counts = HomePage::$dbController->selectAll('
            SELECT \'forum\' AS `link`, \'Forums\' AS `name`, COUNT(*) AS `count` FROM `talks__thread`
            UNION ALL
            SELECT \'bic\' AS `link`, \'Russian Bank Codes\' AS `name`, COUNT(*) AS `count` FROM `bic__list`
            UNION ALL
            SELECT \'ff_character\' AS `link`, \'FFXIV Characters\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__character`
            UNION ALL
            SELECT \'ff_freecompany\' AS `link`, \'FFXIV Free Companies\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__freecompany`
            UNION ALL
            (SELECT \'ff_linkshell\' AS `link`, \'FFXIV Linkshells\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `crossworld`=0)
            UNION ALL
            (SELECT \'ff_crossworldlinkshell\' AS `link`, \'FFXIV Crossworld Linkshells\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `crossworld`=1)
            UNION ALL
            SELECT \'ff_pvpteam\' AS `link`, \'FFXIV PvP Teams\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__pvpteam`
            UNION ALL
            SELECT \'ff_achievement\' AS `link`, \'FFXIV Achievements\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__achievement`
        ');
        #Generate links
        foreach ($counts as $linkType) {
            if ($linkType['count'] <= 50000) {
                $links[] = ['loc'=>$baseurl.$linkType['link'].'/', 'name'=>$linkType['name']];
            } else {
                $pages = intval(ceil($linkType['count']/50000));
                for ($page = 1; $page <= $pages; $page++) {
                    $links[] = ['loc'=>$baseurl.$linkType['link'].'/'.$page.'/', 'name'=>$linkType['name'].', Page '.$page];
                }
            }
        }
        #Return links
        return $links;
    }
}
