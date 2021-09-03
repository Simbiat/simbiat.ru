<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;
use Simbiat\usercontrol\Signinup;

class HomeRouter
{
    #Function to process (or rather relay) $_POST data
    public function postProcess(): void
    {
        if (!empty($_POST)) {
            if (!empty($_POST['signinup'])) {
                (new Signinup)->signinup();
            }
        }
    }


    #Function to prepare data for user control pages
    public function usercontrol(array $uri): array
    {
        $headers = (new Headers);
        $html = (new HTML);
        #Check if URI is empty
        if (empty($uri)) {
            $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/uc/registration', true, true, false);
        }
        #Prepare array
        $outputArray = [
            'service_name' => 'usercontrol',
            'h1' => 'User Control',
            'title' => 'User Control',
            'ogdesc' => 'User\'s Control Panel',
        ];
        #Start breadcrumbs
        $breadArray = [
            ['href'=>'/', 'name'=>'Home page'],
        ];
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]) {
            #Process search page
            case 'registration':
            case 'register':
            case 'login':
            case 'signin':
            case 'signup':
            case 'join':
                if (empty($_SESSION['username'])) {
                    $outputArray['subservice'] = 'registration';
                    $outputArray['h1'] = $outputArray['title'] = 'User sign in/join';
                } else {
                    #Redirect to main page if user is already authenticated
                    $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : ''), false, true, false);
                }
                break;
            default:
                $outputArray['http_error'] = 404;
                break;
        }
        #Add breadcrumbs
        $outputArray['breadcrumbs'] = $html->breadcrumbs($breadArray);
        return $outputArray;
    }

    #Function to route tests
    public function tests(array $uri): array
    {
        $outputArray = [];
        #Forbid if on PROD
        if (HomePage::$PROD === true || empty($uri)) {
            $outputArray['http_error'] = 403;
            return $outputArray;
        }
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]) {
            #Lodestone tests
            case 'lodestone':
                if (empty($uri[1])) {
                    (new HomeTests)->ffTest(true);
                    exit;
                }
                $uri[1] = strtolower($uri[1]);
                switch ($uri[1]) {
                    case 'full':
                        (new HomeTests)->ffTest(true);
                        exit;
                    case 'freecompany':
                    case 'linkshell':
                    case 'pvpteam':
                    case 'character':
                        (new HomeTests)->ffTest(false, $uri[1], $uri[2] ?? '');
                        exit;
                }
                break;
            case 'optimize':
                (new HomeTests)->testDump((new optimizeTables)->setMaintenance("sys__settings","setting","maintenance","value")->setJsonPath('./data/tables.json')->optimize('simbiatr_simbiat', true));
                exit;
        }
        $outputArray['http_error'] = 400;
        return $outputArray;
    }

    #Function to prepare data for "About" pages
    public function about(array $uri): array {
        $html = (new HTML);
        #Prepare array
        $outputArray = [
            'service_name' => 'about',
            'h1' => 'About '.$GLOBALS['siteconfig']['site_name'],
            'title' => 'About '.$GLOBALS['siteconfig']['site_name'],
            'ogdesc' => 'About '.$GLOBALS['siteconfig']['site_name'],
        ];
        #Start breadcrumbs
        $breadArray = [
            ['href'=>'/', 'name'=>'Home page'],
            ['href'=>'/about/', 'name'=>'About'],
        ];
        #Check if URI is empty
        if (!empty($uri)) {
            $uri[0] = strtolower($uri[0]);
            switch ($uri[0]) {
                case 'me':
                case 'website':
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/about/'.$uri[0], 'name'=>ucfirst($uri[0])];
                    #Update page title
                    $outputArray['title'] .= ' '.ucfirst($uri[0]);
                    break;
                case 'tech':
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/about/'.$uri[0], 'name'=>'Technology'];
                    #Update page title
                    $outputArray['title'] .= '\'s Technology';
                    break;
                case 'resume':
                case 'contacts':
                case 'changelog':
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/about/'.$uri[0], 'name'=>ucfirst($uri[0])];
                    #Update page title
                    $outputArray['title'] = ucfirst($uri[0]);
                    break;
                case 'tos':
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/about/'.$uri[0], 'name'=>'Terms of Service'];
                    #Update page title
                    $outputArray['title'] = 'Terms of Service';
                    break;
                case 'privacy':
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/about/'.$uri[0], 'name'=>'Privacy Policy'];
                    #Update page title
                    $outputArray['title'] = 'Privacy Policy';
                    break;
                case 'security':
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/about/'.$uri[0], 'name'=>'Security Policy'];
                    #Update page title
                    $outputArray['title'] = 'Security Policy';
                    break;
                default:
                    $outputArray['http_error'] = 404;
                    break;
            }
            if ($uri[0] === 'resume') {
                $outputArray['timeline'] = (new HTTP20\HTML)->timeline(
                    [
                        [
                            'startTime' => '1989-05-12 02:00:00',
                            'endTime' => null,
                            'name' => 'Human on Earth',
                            'icon' => '/img/icons/Earth.svg',
                            'href' => null,
                            'position' => null,
                            'responsibilities' => null,
                            'achievements' => [
                                'Blood donor',
                                'Patron for <a href="https://sos-dd.ru/" target="_blank"><img src="/img/icons/SOSVillages.svg" alt="SOS Children\'s Villages" class="linkIcon">SOS Children\'s Villages</a>'
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => '1995-09-01',
                            'endTime' => '2006-06-23',
                            'name' => 'School №1208',
                            'icon' => '/img/icons/1208.png',
                            'href' => 'https://sch1208uv.mskobr.ru/',
                            'position' => 'Pupil',
                            'responsibilities' => null,
                            'achievements' => [
                                '10 years of general education',
                                'High level of English',
                                'Class president in grades 6 to 9',
                                'Participated in school theatre with noticeable roles of Famusov (<cite>Grief from the mind</cite>), Zvyagincev (<cite>They Were Fighting for Homeland</cite>), reindeer (<cite>Snow Queen</cite>), Carlo/Geppetto and Karabas-Barabas/Mangiafuoco/Stromboli (<cite>Buratino/Pinocchio</cite>)',
                                '<a href="/static/resume/MiddleSchool.jpg" target="_blank"><img src="/img/certificate.svg" alt="Certificate" class="linkIcon">Certificate</a>',
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => '2002-01-07',
                            'endTime' => null,
                            'name' => null,
                            'icon' => '/img/logo.svg',
                            'href' => null,
                            'position' => 'Writer',
                            'responsibilities' => [
                                'Write prose in English and Russian',
                                'Write poetry in English and Russian',
                                'Occasionally write reviews for games, anime, manga, movies and TV series',
                                'Learn narrative design through gaming experiences',
                            ],
                            'achievements' => [
                                'Posted most of game reviews on <a href="https://steamcommunity.com/id/Simbiat19/recommended/" target="_blank"><img loading="lazy" class="linkIcon" src="/img/social/steam.svg" alt="Steam">Steam</a>',
                                'Experimented with narrative in video by creating <a href="https://www.youtube.com/watch?v=AsCOsuaB4IE" target="_blank"><img loading="lazy" class="linkIcon" src="/img/social/youtube.svg" alt="Youtube">Welcome To My Crib</a> and <a href="https://www.youtube.com/watch?v=Q7fN-XDUMHA&list=PL0KIME6alndX8-8yEqF0c3IbajPJDAvJt" target="_blank"><img class="linkIcon" src="/img/social/youtube.svg" alt="Youtube">Aqua Chronica</a> series',
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => '2006-09-01',
                            'endTime' => '2011-06-16',
                            'name' => 'Moscow Institute of Electronics and Mathematics',
                            'icon' => '/img/icons/MIEM.svg',
                            'href' => 'https://miem.hse.ru/',
                            'position' => 'Student (specialist)',
                            'responsibilities' => null,
                            'achievements' => [
                                'Class president since 2nd year',
                                'Graduate work: <cite>Testing of hardware and software solutions for 3-dimensional information representation in virtual reality system</cite>',
                                '<a href="/static/resume/Specialist-Diploma.jpg" target="_blank"><img src="/img/certificate.svg" alt="Diploma" class="linkIcon">Diploma</a>',
                                '<a href="/static/resume/Specialist-GPA.pdf" target="_blank"><img src="/img/certificate.svg" alt="GPA" class="linkIcon"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.56',
                            ],
                            'description' => 'Specialization: management and informatics in technical systems',
                        ],
                        [
                            'startTime' => '2007-09-01',
                            'endTime' => '2011-06-07',
                            'name' => 'Moscow Institute of Electronics and Mathematics',
                            'icon' => '/img/icons/MIEM.svg',
                            'href' => 'https://miem.hse.ru/',
                            'position' => 'Student (bachelor)',
                            'responsibilities' => null,
                            'achievements' => [
                                'Class president',
                                'Graduate work: <cite>Hardware solutions for 3-dimensional information representation in virtual reality system</cite>',
                                '<a href="/static/resume/Bachelor-Diploma.jpg" target="_blank"><img src="/img/certificate.svg" alt="Diploma" class="linkIcon">Diploma</a>',
                                '<a href="/static/resume/Bachelor-GPA.pdf" target="_blank"><img src="/img/certificate.svg" alt="GPA" class="linkIcon"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.52',
                            ],
                            'description' => 'Specialization: automation and management',
                        ],
                        [
                            'startTime' => '2008-03-27',
                            'endTime' => null,
                            'name' => null,
                            'icon' => '/img/logo.svg',
                            'href' => null,
                            'position' => 'Developer',
                            'responsibilities' => [
                                'Develop website on PHP with JavaScript',
                                'Design UI and UX of the website',
                                'Support website operations and users',
                                'Analyze all requirements and requests of users, maintaining close communications to understand needs and improve product accordingly',
                                'Write technical and client documentation',
                            ],
                            'achievements' => [
                                'Rewrote code into libraries and published on <a href="https://github.com/Simbiat" target="_blank"><img loading="lazy" class="linkIcon" src="/img/social/github.svg" alt="GitHub">GitHub</a>. Current website project is also meant to remain open source for, unless it can affect security.',
                                'Controlled optimization processes and served as the main developer of the <a href="https://github.com/Simbiat/DarkSteam/" target="_blank"><img loading="lazy" class="linkIcon" src="/img/social/github.svg" alt="GitHub">DarkSteam</a> project until its closure, including releasing a revamped app version with migration to web platform to yield a 150x performance increase',
                                'Supported file storage of 8Tbs+',
                                'Administered and moderated a forum of 20,000+ users',
                                'Automated payments and donations via PayPal using vBulletin plugins',
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => '2009-02-02',
                            'endTime' => '2009-03-27',
                            'name' => 'Windsor',
                            'icon' => '/img/icons/Windsor.jpg',
                            'href' => 'https://www.windsor.ru/',
                            'position' => 'Engineer',
                            'responsibilities' => [
                                'Manage office hardware and software',
                                'Manage company\'s website',
                                'Create digital training courses',
                            ],
                            'achievements' => null,
                            'description' => null,
                        ],
                        [
                            'startTime' => '2009-06-04',
                            'endTime' => '2011-05-20',
                            'name' => 'IBS Datafort',
                            'icon' => '/img/icons/IBS.svg',
                            'href' => 'https://www.datafort.ru/',
                            'position' => 'Engineer',
                            'responsibilities' => [
                                'Initiate operations related to End of Day processing',
                                'Monitor continuous night processes',
                                'Level 1 support of subset of regional applications',
                                'Level 1 or level 2 support of local applications'
                            ],
                            'achievements' => [
                                'Promoted to day-time operator after approximately 1 year',
                                'Transferred a paper-based checklist used by operators to Excel featuring several automated functions to improve traceability of work',
                            ],
                            'description' => 'Outsourced job for Citi Russia as evening operator.',
                        ],
                        [
                            'startTime' => '2011-05-23',
                            'endTime' => '2015-09-14',
                            'name' => 'Citi',
                            'icon' => '/img/icons/Citi.svg',
                            'href' => 'https://www.citibank.ru/',
                            'position' => 'Technical Support Specialist',
                            'responsibilities' => [
                                'Level 1 support of subset of regional applications',
                                'Level 1 to level 2 support of local applications',
                                'Subject matter expert for several local applications',
                                'Application management',
                                'Participation in projects',
                                'Testing of fixes and new features in supported applications',
                                'Assistance with integration of new applications or processes',
                            ],
                            'achievements' => [
                                'Migration of clearing processing from Windows XP to Windows 7 and automation of some of the steps',
                                'Expert assistance in refactoring of local application for stability and speed improvements',
                                'Coached several new evening and morning operators',
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => '2015-09-15',
                            'endTime' => '2018-05-15',
                            'name' => 'Citi',
                            'icon' => '/img/icons/Citi.svg',
                            'href' => 'https://www.citibank.ru/',
                            'position' => 'Technical Support Analyst',
                            'responsibilities' => [
                                'Level 1 support of subset of regional applications',
                                'Level 1 to level 3 support of local applications',
                                'Subject matter expert for several local applications',
                                'Application management',
                                'Participate in projects',
                                'Testing of fixes and new features in supported applications',
                                'Assistance with integration of new applications or processes',
                                'Primary contact person for clearing operations\' technology, processes and applications including cryptography',
                                'Write technical and user documentation',
                                'Participate in audits both internal and external',
                                'Team leader for operators working in shifts',
                            ],
                            'achievements' => [
                                'Automated several manual processes used in the department',
                                'Standardized and optimized server-side scripts',
                                'Successfully managed 30 applications simultaneously, closing decade-long backlog for a handful of them',
                                'Participated in <cite>Ideation</cite> program as subject matter expert for one of the winning ideas',
                                'Single-handedly supported the entire country of Kazakhstan for 2 years, fulfilling various roles including technical support, project manager, application manager, and business analyst',
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => '2018-05-16',
                            'endTime' => '2021-07-23',
                            'name' => 'Citi',
                            'icon' => '/img/icons/Citi.svg',
                            'href' => 'https://www.citibank.ru/',
                            'position' => 'Senior Technical Support Analyst',
                            'responsibilities' => [
                                'Level 1 to level 3 support of local applications',
                                'Subject matter expert for local applications',
                                'Participate in projects',
                                'Primary contact person for clearing operations\' technology, processes and applications including cryptography',
                                'Write technical and user documentation',
                                'Participate in audits both internal and external',
                                'Changes management',
                                'Team leader for operators working in shifts',
                            ],
                            'achievements' => [
                                'Closed several potential security issues in Kazakhstan processes',
                                'Negotiated vendor pricing for a project from $100k USD down to $55k USD and led the refactoring of the application',
                                'Participated in <cite>Want to be a leader</cite> program leading my team to 1st place as early as in the 2nd month of it',
                                'Registered all externally issued certificates in local tracking system',
                            ],
                            'description' => null,
                        ],
                        [
                            'startTime' => null,
                            'endTime' => '2020-12-21',
                            'name' => 'Luxoft Training',
                            'icon' => '/img/icons/Luxoft.svg',
                            'href' => 'https://www.luxoft-training.ru/',
                            'position' => 'Student',
                            'responsibilities' => null,
                            'achievements' => '<a href="/static/resume/Web-Service_Certificate.pdf" target="_blank"><img src="/img/certificate.svg" alt="Certificate" class="linkIcon">Certificate</a>',
                            'description' => 'Customized course <cite>Basics of web-services support</cite>, 6 hours',
                        ],
                        [
                            'startTime' => '2021-09-20',
                            'endTime' => null,
                            'name' => 'Smartly.io',
                            'icon' => '/img/icons/Smartly.svg',
                            'href' => 'https://www.smartly.io/',
                            'position' => 'Tier 3 Technical Support Engineer',
                            'responsibilities' => [
                                'Ensure best-in-class technical support and distinguished customer service',
                                'Solve technological challenges of the world\'s largest online advertisers, helping them to solve issues in an expedient and affable manner',
                                'Help to launch products, including supporting alpha and beta features',
                                'Resolve advertisers\' issues and identify product bugs using internal troubleshooting tools',
                                'Perform tech-heavy investigations and resolve sophisticated support critical issues',
                                'Analyze, reproduce, prioritize, and document platform bugs',
                                'Work quickly to identify and fix the root causes of problems',
                                'Assist and train teammates',
                            ],
                            'achievements' => null,
                            'description' => null,
                        ],
                    ]
                , brLimit: 6);
            }
            $outputArray['h1'] = $outputArray['title'];
            $outputArray['ogdesc'] = $outputArray['title'];
            $outputArray['subservice'] = $uri[0];
        }
        $outputArray['breadcrumbs'] = $html->breadcrumbs($breadArray);
        return $outputArray;
    }

    #Function to prepare data for FFTracker depending on the URI
    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function fftracker(array $uri): array
    {
        $fftracker = (new FFTracker);
        $html = (new HTML);
        $headers = (new Headers);
        #Check if URI is empty
        if (empty($uri)) {
            $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/search', true, true, false);
        }
        #Prepare array
        $outputArray = [
            'service_name' => 'fftracker',
            'h1' => 'Final Fantasy XIV Tracker',
            'title' => 'Final Fantasy XIV Tracker',
            'ogdesc' => 'Tracker for Final Fantasy XIV entities and respective statistics',
        ];
        #Start breadcrumbs
        $breadArray = [
            ['href'=>'/', 'name'=>'Home page'],
        ];
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]) {
            #Process search page
            case 'search':
                $outputArray['subservice'] = 'search';
                #Set search value
                if (!isset($uri[1])) {
                    $uri[1] = '';
                }
                $decodedSearch = rawurldecode($uri[1]);
                #Continue breadcrumb
                $breadArray[] = ['href'=>'/fftracker/search', 'name'=>'Search'];
                if (!empty($uri[1])) {
                    $breadArray[] = ['href'=>'/fftracker/search/'.$uri[1], 'name'=>'Search for '.$decodedSearch];
                } else {
                    #Cache due to random entities
                    $outputArray['cache_age'] = 86400;
                }
                #Set specific values
                $outputArray['searchvalue'] = $decodedSearch;
                $outputArray['searchresult'] = $fftracker->Search($decodedSearch);
                break;
            #Process statistics
            case 'statistics':
                #Check if type is set
                if (empty($uri[1])) {
                    $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/statistics/genetics', true, true, false);
                } else {
                    $uri[1] = strtolower($uri[1]);
                    if (in_array($uri[1], ['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other', 'bugs'])) {
                        $outputArray['subservice'] = 'statistics';
                        #Set statistics type
                        $outputArray['ff_stat_type'] = $uri[1];
                        #Continue breadcrumb
                        $tempName = ucfirst(preg_replace('/s$/i', 's\'', preg_replace('/companies/i', ' Companies', $uri[1])).' statistics');
                        $breadArray[] = ['href'=>'/fftracker/statistics/'.$uri[1], 'name'=>$tempName];
                        #Get the data
                        $outputArray[$uri[0]] = $fftracker->Statistics($uri[1]);
                        #Update meta
                        $outputArray['h1'] .= ': Statistics';
                        $outputArray['title'] .= ': Statistics';
                        $outputArray['ogdesc'] = $tempName.' on '.$outputArray['ogdesc'];
                        $outputArray['cache_age'] = 86400;
                    } else {
                        $outputArray['http_error'] = 404;
                    }
                }
                break;
            #Process lists
            case 'crossworldlinkshells':
            case 'crossworld_linkshells':
                #Redirect to linkshells list, since we do not differentiate between them that much
                $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/linkshells/'.(empty($uri[1]) ? '' : $uri[1]), true, true, false);
                break;
            case 'freecompanies':
            case 'linkshells':
            case 'characters':
            case 'achievements':
            case 'pvpteams':
                #Check if page was provided and is numeric
                if (empty($uri[1]) || !is_numeric($uri[1]) || intval($uri[1]) < 1) {
                    $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/'.$uri[0].'/1', true, true, false);
                }
                #Ensure that use INT
                $uri[1] = intval($uri[1]);
                #Get data
                $outputArray['searchresult'] = $fftracker->listEntities($uri[0], ($uri[1]-1)*100);
                #Check that we requested page is not more than what was requested
                $lastPage = intval(ceil($outputArray['searchresult']['statistics']['count']/100));
                if ($uri[1] > $lastPage) {
                    #Bad page
                    unset($outputArray['searchresult']);
                    $outputArray['http_error'] = 404;
                } else {
                    #Try to get out earlier based on date of last update of the list. Unlikely, that will help, but still.
                    $headers->lastModified($outputArray['searchresult']['statistics']['updated'], true);
                    $outputArray['subservice'] = $uri[0];
                    #Adjust list type to human-readable value
                    $tempName = match($uri[0]) {
                        'freecompanies' => 'Free Companies',
                        'pvpteams' => 'PvP Teams',
                        default => ucfirst($uri[0]),
                    };
                    #Continue breadcrumb
                    $breadArray[] = ['href'=>'/fftracker/'.$uri[0].'/'.$uri[1], 'name'=>$tempName.', page '.$uri[1]];
                    #Update meta
                    $outputArray['h1'] .= ': '.$tempName.', page '.$uri[1];
                    $outputArray['title'] .= ': '.$tempName.', page '.$uri[1];
                    $outputArray['ogdesc'] = 'List of '.$tempName.' on '.$outputArray['ogdesc'];
                    #Prepare pagination
                    $outputArray['pagination_top'] = $html->pagination($uri[1], $lastPage);
                    $outputArray['pagination_bottom'] = $html->pagination($uri[1], $lastPage);
                }
                break;
            case 'crossworldlinkshell':
            case 'crossworld_linkshell':
                #Redirect to linkshell page, since we do not differentiate between them that much
                $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/linkshell/'.(empty($uri[1]) ? '' : $uri[1]), true, true, false);
                break;
            case 'achievement':
            case 'character':
            case 'freecompany':
            case 'linkshell':
            case 'pvpteam':
                #Check if id was provided and has valid format
                if (empty($uri[1]) || preg_match('/^[0-9a-f]{1,40}$/i', $uri[1]) !== 1) {
                    $outputArray['http_error'] = 404;
                } else {
                    #Grab data
                    $outputArray[$uri[0]] = $fftracker->TrackerGrab($uri[0], $uri[1]);
                    #Check if ID was found
                    if (empty($outputArray[$uri[0]])) {
                        $outputArray['http_error'] = 404;
                    } else {
                        $outputArray['subservice'] = $uri[0];
                        #Try to exit early based on modification date
                        $headers->lastModified($outputArray[$uri[0]]['updated'], true);
                        #Continue breadcrumb by adding link to list (1 page)
                        $breadArray[] = match($uri[0]) {
                            'freecompany' => ['href'=>'/fftracker/freecompanies/1', 'name'=>'Free Companies'],
                            'pvpteam' => ['href'=>'/fftracker/'.$uri[0].'s/1', 'name'=>'PvP Teams'],
                            default => ['href'=>'/fftracker/'.$uri[0].'s/1', 'name'=>ucfirst($uri[0]).'s'],
                        };
                        #Continue breadcrumb by adding link to current entity
                        $breadArray[] = ['href' => '/fftracker/'.$uri[0].'/'.$outputArray[$uri[0]][$uri[0].'id'].'/'.rawurlencode($outputArray[$uri[0]]['name']), 'name' => $outputArray[$uri[0]]['name']];
                        #Generate levels' list if we have members
                        if (!empty($outputArray[$uri[0]]['members'])) {
                            $outputArray[$uri[0]]['levels'] = array_unique(array_column($outputArray[$uri[0]]['members'], 'rank'));
                        }
                        #Update meta
                        $outputArray['h1'] = $outputArray[$uri[0]]['name'];
                        $outputArray['title'] = $outputArray[$uri[0]]['name'];
                        $outputArray['ogdesc'] = $outputArray[$uri[0]]['name'].' on '.$outputArray['ogdesc'];
                        #Setup OG profile for characters
                        if ($uri[0] === 'character') {
                            $outputArray['ogtype'] = 'profile';
                            $profName = explode(' ', $outputArray[$uri[0]]['name']);
                            $outputArray['ogextra'] = '
                                <meta property="og:type" content="profile" />
                                <meta property="profile:first_name" content="'.htmlspecialchars($profName[0]).'" />
                                <meta property="profile:last_name" content="'.htmlspecialchars($profName[1]).'" />
                                <meta property="profile:username" content="'.htmlspecialchars($outputArray[$uri[0]]['name']).'" />
                                <meta property="profile:gender" content="'.htmlspecialchars(($outputArray[$uri[0]]['genderid'] === 1 ? 'male' : 'female')).'" />
                            ';
                        }
                        #Link header/tag for API
                        $altLink = [['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation', 'href' => '/api/fftracker/'.$uri[0].'/'.$outputArray[$uri[0]][$uri[0].'id']]];
                        #Send HTTP header
                        $headers->links($altLink);
                        #Add link to HTML
                        $outputArray['link_extra'] = $headers->links($altLink, 'head');
                        #Cache age for achievements (due to random characters)
                        if ($uri[0] === 'achievement') {
                            $outputArray['cache_age'] = 86400;
                        }
                    }
                }
                break;
            default:
                $outputArray['http_error'] = 404;
                break;
        }
        #If we have 404 - generate random entities as suggestions
        if (!empty($outputArray['http_error']) && $outputArray['http_error'] === 404) {
            $outputArray['ff_suggestions'] = $fftracker->GetRandomEntities($fftracker->maxlines);
            #Cache due to random entities
            $outputArray['cache_age'] = 86400;
        }
        #Add breadcrumbs
        $outputArray['breadcrumbs'] = $html->breadcrumbs($breadArray);
        return $outputArray;
    }

    #Function to prepare data for BICTracker depending on the URI
    /**
     * @throws \Exception
     */
    public function bictracker(array $uri): array
    {
        $headers = (new Headers);
        $bictracker = (new bicXML);

        $bictracker->dbUpdate();
        exit;

        #Tell that content is intended for Russians
        header('Content-Language: ru-RU');
        #Prepare array
        $outputArray = [
            'service_name' => 'bictracker',
            'h1' => 'BIC Tracker '.$GLOBALS['siteconfig']['site_name'],
            'title' => 'BIC Tracker '.$GLOBALS['siteconfig']['site_name'],
            'ogdesc' => 'BIC Tracker '.$GLOBALS['siteconfig']['site_name'],
        ];
        #Start breadcrumbs
        $breadArray = [
            ['href'=>'/', 'name'=>'Home page'],
            ['href'=>'/bictracker/', 'name'=>'BIC Tracker'],
        ];
        #Check if URI is empty
        if (!empty($uri)) {
            $uri[0] = strtolower($uri[0]);
            #Gracefully handle legacy links
            if ($uri[0] !== 'search' && mb_strlen(rawurldecode($uri[0])) === 8) {
                #Assume legacy '/bic/vkey' link type was used and redirect to proper link
                $headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/bictracker/bic/' . $uri[0], true, true, false);
            }
            #Prepare array
            $outputArray = [
                'service_name' => 'bictracker',
                'h1' => 'BIC Tracker',
                'title' => 'BIC Tracker',
                'ogdesc' => 'Representation of Bank Identification Codes from Central Bank of Russia',
            ];
            switch ($uri[0]) {
                #Process keying page
                case 'keying':
                    $outputArray['subservice'] = $uri[0];
                    #Continue breadcrumbs
                    $breadArray[] = ['href' => '/bictracker/keying', 'name' => 'Keying'];
                    $outputArray['h1'] = $outputArray['title'] = 'Ключевание счёта';
                    $outputArray['ogdesc'] = 'Проверка корректности контрольного символа в номере счёта против номера банковского идентификационного кода.';
                    $outputArray['checkResult'] = null;
                    if (!empty($uri[1]) && !empty($uri[2])) {
                        $outputArray['checkResult'] = (new AccountKeying)->accCheck($uri[1], $uri[2]);
                        if ($outputArray['checkResult'] !== false) {
                            $outputArray['bic_value'] = $uri[1];
                            $outputArray['acc_value'] = $uri[2];
                            $outputArray['h1'] = $outputArray['title'] = 'Ключевание счёта '.$uri[2];
                            $outputArray['link_extra'] = HomePage::$headers->links([['href' => '/api/bictracker/keying/'.$uri[1].'/'.$uri[2], 'rel' => 'alternate', 'title' => 'API link', 'type' => 'application/json; charset=utf-8'],], 'head');
                        }
                        if (is_numeric($outputArray['checkResult'])) {
                            $outputArray['properKey'] = preg_replace('/(^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх][0-9]{2})([0-9])([0-9]{11})$/u', '$1<span class="success">'.$outputArray['checkResult'].'</span>$3', $uri[2]);
                        }
                    }
                    break;
                #Process search page
                case 'search':
                    $outputArray['subservice'] = $uri[0];
                    $outputArray['maxresults'] = 50;
                    #Continue breadcrumbs
                    $breadArray[] = ['href' => '/bictracker/search', 'name' => 'Search'];
                    #Set search value
                    if (!isset($uri[1])) {
                        $uri[1] = '';
                    }
                    #Sanitize search value
                    $decodedSearch = preg_replace('/[^\P{Cyrillic}a-zA-Z0-9!@#\$%&*()\-+=|?<>]/', '', rawurldecode($uri[1]));
                    #Check if search value was provided
                    if (empty($uri[1])) {
                        #Get statistics
                        $outputArray = array_merge($outputArray, $bictracker->Statistics());
                    } else {
                        #Continue breadcrumbs
                        $breadArray[] = ['href' => '/bictracker/search/' . $uri[1], 'name' => 'Search for ' . $decodedSearch];
                        #Get search results
                        $outputArray['searchresult'] = $bictracker->Search($uri[1]);
                        $outputArray['searchvalue'] = $uri[1];
                    }
                    break;
                #Process bic details page
                case 'bic':
                    $outputArray['subservice'] = $uri[0];
                    if (empty($uri[1])) {
                        $outputArray['http_error'] = 404;
                    } else {
                        #Sanitize vkey
                        $vkey = preg_replace('/[^a-zA-Z0-9!@#\$%&*()\-+=|?<>]/', '', rawurldecode($uri[1]));
                        #Try to get details
                        $outputArray['bicdetails'] = $bictracker->getCurrent($vkey);
                        #Check if key was found
                        if (empty($outputArray['bicdetails'])) {
                            $outputArray['http_error'] = 404;
                        } else {
                            #Try to exit early based on modification date
                            if (!empty($outputArray['bicdetails']['DT_IZM'])) {
                                $headers->lastModified(strtotime($outputArray['bicdetails']['DT_IZM']), true);
                            }
                            #Continue breadcrumbs
                            if (!empty($outputArray['bicdetails']['BIC_UF'])) {
                                foreach(array_reverse($outputArray['bicdetails']['BIC_UF']) as $bank) {
                                    $breadArray[] = ['href' => '/bictracker/bic/' . $bank['id'], 'name' => $bank['name']];
                                }
                            }
                            $breadArray[] = ['href' => '/bictracker/bic/' . $uri[1], 'name' => $outputArray['bicdetails']['NAMEP']];
                            #Set cache due to query complexity
                            $outputArray['cache_age'] = 259200;
                            #Update meta
                            $outputArray['title'] = $outputArray['bicdetails']['NAMEP'];
                            $outputArray['h1'] = $outputArray['bicdetails']['NAMEP'];
                            $outputArray['ogdesc'] = $outputArray['bicdetails']['NAMEP'] . ' (' . $outputArray['bicdetails']['NEWNUM'] . ') in BIC Tracker';
                            #Link header/tag for API
                            $altLink = [['rel' => 'alternate', 'type' => 'application/json', 'title' => 'JSON representation', 'href' => '/api/bictracker/bic/' . rawurlencode($vkey)]];
                            #Send HTTP header
                            $headers->links($altLink);
                            #Add link to HTML
                            $outputArray['link_extra'] = $headers->links($altLink, 'head');
                        }
                    }
                    break;
                default:
                    $outputArray['http_error'] = 404;
                    break;
            }
        }
        #Add breadcrumbs
        $outputArray['breadcrumbs'] = (new HTML)->breadcrumbs($breadArray);
        return $outputArray;
    }

    #Function to route error pages
    public function error(array $uri): array {
        if (empty($uri[0]) || preg_match('/\d{3}/', $uri[0]) !== 1) {
            $outputArray['http_error'] = 404;
        } else {
            $outputArray['http_error'] = intval($uri[0]);
        }
        $outputArray['error_page'] = true;
        return $outputArray;
    }
}
