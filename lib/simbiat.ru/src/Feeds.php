<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Config\Common;
use Simbiat\HTTP20\Atom;
use Simbiat\HTTP20\RSS;

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
                            'name' => Common::adminName,
                            'email' => Common::adminMail,
                            'uri' => Common::$baseUrl.'/',
                        ]],
                        'icon' => Common::$baseUrl.'/img/favicons/simbiat.png',
                        'logo' => Common::$baseUrl.'/img/ogimages/default.png',
                    ];
                } elseif ($format === 'rss') {
                    $settings = [
                        'webMaster' => Common::adminMail,
                        'managingEditor' => Common::adminMail,
                        'language' => 'en-us',
                        'ttl' => 3600,
                        'image' => [
                            'url' => Common::$baseUrl.'/img/favicons/android/144x144.png',
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
                        'bicchanged' => 'SELECT CONCAT(\''.Common::$baseUrl.'/bictracker/bics/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `updated`, \'Центральный Банк Российской Федерации\' AS `author_name`, \'https://cbr.ru/\' AS `author_uri`, `NameP` as `summary`, `Updated` as `published`, \'Центральный Банк Российской Федерации\' AS `source_title`, \'https://cbr.ru/\' AS `source_id`, `Updated` as `source_updated` FROM `bic__list` a WHERE `DateOut` IS NULL ORDER BY `Updated` DESC LIMIT 25',
                        'bicdeleted' => 'SELECT CONCAT(\''.Common::$baseUrl.'/bictracker/bics/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `updated`, \'Центральный Банк Российской Федерации\' AS `author_name`, \'https://cbr.ru/\' AS `author_uri`, `NameP` as `summary`, `Updated` as `published`, \'Центральный Банк Российской Федерации\' AS `source_title`, \'https://cbr.ru/\' AS `source_id`, `Updated` as `source_updated` FROM `bic__list` a WHERE `DateOut` IS NOT NULL ORDER BY `DateOut` DESC LIMIT 25',
                    };
                } elseif ($format === 'rss') {
                    $query = match($uri[0]) {
                        'bicchanged' => 'SELECT CONCAT(\''.Common::$baseUrl.'/bictracker/bics/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `pubDate`, \'BICs\' AS `category` FROM `bic__list` a WHERE `DateOut` IS NULL ORDER BY `Updated` DESC LIMIT 25',
                        'bicdeleted' => 'SELECT CONCAT(\''.Common::$baseUrl.'/bictracker/bics/\', `BIC`) as `link`, `NameP` as `title`, `Updated` as `pubDate`, \'BICs\' AS `category` FROM `bic__list` a WHERE `DateOut` IS NOT NULL ORDER BY `DateOut` DESC LIMIT 25',
                    };
                }
                #Generate the feed
                if (!empty($query)) {
                    if ($format === 'atom') {
                        Atom::Atom(Common::siteName.': '.$title, HomePage::$dbController->selectAll($query), feed_settings: $settings);
                    } elseif ($format === 'rss') {
                        RSS::RSS(Common::siteName.': '.$title, HomePage::$dbController->selectAll($query), feed_settings: $settings);
                    }
                }
            }
        }
        #If we reach here, it means, the requested page does not exist
        return ['http_error' => 404];
    }
}
