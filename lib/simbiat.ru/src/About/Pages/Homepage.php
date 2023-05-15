<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Talks;
use Simbiat\usercontrol\User;

class Homepage extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/', 'name' => 'Home']
    ];
    #Sub service name
    protected string $subServiceName = 'homepage';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Home';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Homepage of Simbiat Software';
    #List of images to H2 push
    protected array $h2push = [
        '/img/logo.svg',
        '/img/share.svg',
        '/img/navigation/home.svg',
        '/img/navigation/talks.svg',
        '/img/navigation/skills.svg',
        '/img/navigation/experience.svg',
        '/img/navigation/projects.svg',
        '/img/navigation/testimonials.svg',
        '/img/navigation/contacts.svg',
        '/img/download.svg',
        '/img/ogimages/jiangshi.png',
        '/img/ogimages/dden.png',
        '/img/ogimages/bictracker.png',
        '/img/ogimages/fftracker.png',
    ];

    protected function generate(array $path): array
    {
        $outputArray = ['h1' => 'Home', 'serviceName' => 'homepage', 'yearsOfExperience' => (intval(date('Y')) - 2009)];
        $user = new User(Talks::userIDs['Owner']);
        $outputArray['posts'] = $user->getTalksStarters(true);
        #Limit number of posts
        $outputArray['posts'] = array_slice($outputArray['posts'], 0, 6);
        #Feedback
        $outputArray['feedbacks'] =
            [
                [
                    'href' => '/static/resume/Smartly/20211126.jpg',
                    'alt' => 'Feedback from Tatu Virtanen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/tatuvirtanen/" target="_blank">Tatu Virtanen</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220216.jpg',
                    'alt' => 'Feedback from Toivo Vaje',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/toivovaje/" target="_blank">Toivo Vaje</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220218.jpg',
                    'alt' => 'Feedback from Jarno Marin',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jarnomarin/" target="_blank">Jarno Marin</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220303.jpg',
                    'alt' => 'Feedback from Marcella Armilla',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/marcelladitaarmilla/" target="_blank">Marcella Armilla</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220304.jpg',
                    'alt' => 'Feedback from Ayberk Yerlikaya',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/ayberkyrlky/" target="_blank">Ayberk Yerlikaya</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220404.jpg',
                    'alt' => 'Feedback from Augustine Lee',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/augustinelee12/" target="_blank">Augustine Lee</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220413.jpg',
                    'alt' => 'Feedback from Xavier Budan',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/xavierbudan/" target="_blank">Xavier Budan</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220429.jpg',
                    'alt' => 'Feedback from Jana Christoviciute',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jana-christoviciute-05ba268b/" target="_blank">Jana Christoviciute</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220513_1.jpg',
                    'alt' => 'Feedback from Marcella Armilla',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/marcelladitaarmilla/" target="_blank">Marcella Armilla</a>',
                ],
                [
                    'href' => '/static/resume/Smartly/20220513_2.jpg',
                    'alt' => 'Feedback from Magalí Gomez',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/magali-gomez/" target="_blank">Magalí Gomez</a>',
                ],
            ];
        $outputArray['timeline'] =
            [
                [
                    'startTime' => '1989-05-12 02:00:00',
                    'endTime' => null,
                    'name' => 'Human',
                    'icon' => '/img/icons/Earth.svg',
                    'href' => null,
                    'position' => null,
                    'responsibilities' => null,
                    'achievements' => [
                        'Blood donor',
                        'Patron for <a href="https://sos-dd.ru/" target="_blank"><img loading="lazy" decoding="async" src="/img/icons/SOSVillages.svg" alt="SOS Children\'s Villages" class="linkIcon">SOS Children\'s Villages</a>'
                    ],
                    'description' => null,
                ],
                [
                    'startTime' => '1995-09-01',
                    'endTime' => '2006-06-23',
                    'name' => 'School №1208',
                    'icon' => '/img/icons/1208.webp',
                    'href' => 'https://sch1208uv.mskobr.ru/',
                    'position' => 'Pupil',
                    'responsibilities' => null,
                    'achievements' => [
                        '10 years of general education',
                        'High level of English',
                        'Class president in grades 6 to 9',
                        'Participated in school theatre with noticeable roles of Famusov (<cite>Grief from the mind</cite>), Zvyagincev (<cite>They Were Fighting for Homeland</cite>), reindeer (<cite>Snow Queen</cite>), Carlo/Geppetto and Karabas-Barabas/Mangiafuoco/Stromboli (<cite>Buratino/Pinocchio</cite>)',
                        '<a href="/static/resume/MiddleSchool.jpg" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Certificate" class="linkIcon">Certificate</a>',
                    ],
                    'description' => null,
                ],
                [
                    'startTime' => '2002-01-07',
                    'endTime' => null,
                    'name' => null,
                    'icon' => '/img/logo.svg',
                    'href' => null,
                    'position' => 'Content Engineer',
                    'responsibilities' => [
                        'Develop website on PHP with JavaScript',
                        'Design UI and UX of the website',
                        'Support website operations and users',
                        'Analyze all requirements and requests of users, maintaining close communications to understand needs and improve product accordingly',
                        'Write technical and client documentation',
                        'Write prose in English and Russian',
                        'Write poetry in English and Russian',
                        'Occasionally write reviews for games, anime, manga, movies and TV series',
                        'Learn narrative design through gaming experiences',
                    ],
                    'achievements' => [
                        'Rewrote code into libraries and published on <a href="https://github.com/Simbiat" target="_blank"><img decoding="async" loading="lazy" class="linkIcon" src="/img/social/github.svg" alt="GitHub">GitHub</a>. Current website project is also meant to remain open source for, unless it can affect security.',
                        'Controlled optimization processes and served as the main developer of the <a href="https://github.com/Simbiat/DarkSteam/" target="_blank"><img decoding="async" loading="lazy" class="linkIcon" src="/img/social/github.svg" alt="GitHub">DarkSteam</a> project until its closure, including releasing a revamped app version with migration to web platform to yield a 150x performance increase',
                        'Supported file storage of 8Tbs+',
                        'Administered and moderated a forum of 20,000+ users',
                        'Automated payments and donations via PayPal using vBulletin plugins',
                        'Posted most of the game reviews on <a href="https://steamcommunity.com/id/Simbiat19/recommended/" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" src="/img/social/steam.svg" alt="Steam">Steam</a>',
                        'Experimented with narrative in video by creating <a href="https://www.youtube.com/watch?v=AsCOsuaB4IE" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" src="/img/social/youtube.svg" alt="Youtube">Welcome To My Crib</a> and <a href="https://www.youtube.com/watch?v=Q7fN-XDUMHA&list=PL0KIME6alndX8-8yEqF0c3IbajPJDAvJt" target="_blank"><img loading="lazy" decoding="async" class="linkIcon" src="/img/social/youtube.svg" alt="Youtube">Aqua Chronica</a> series',
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
                        '<a href="/static/resume/Specialist-Diploma.jpg" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Diploma" class="linkIcon">Diploma</a>',
                        '<a href="/static/resume/Specialist-GPA.pdf" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="GPA" class="linkIcon"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.56',
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
                        '<a href="/static/resume/Bachelor-Diploma.jpg" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Diploma" class="linkIcon">Diploma</a>',
                        '<a href="/static/resume/Bachelor-GPA.pdf" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="GPA" class="linkIcon"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.52',
                    ],
                    'description' => 'Specialization: automation and management',
                ],
                [
                    'startTime' => '2009-02-02',
                    'endTime' => '2009-03-27',
                    'name' => 'Windsor',
                    'icon' => '/img/icons/Windsor.webp',
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
                        'Application management (mix of product ownership, project management, business analysis, quality assurance, and some other roles)',
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
                        'Participated in <cite>Ideation</cite> program as subject-matter expert for one of the winning ideas',
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
                    'achievements' => '<a href="/static/resume/Web-Service_Certificate.pdf" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Certificate" class="linkIcon">Certificate</a>',
                    'description' => 'Customized course <cite>Basics of web-services support</cite>, 6 hours',
                ],
                [
                    'startTime' => '2021-09-20',
                    'endTime' => '2022-06-30',
                    'name' => 'Smartly.io',
                    'icon' => '/img/icons/Smartly.svg',
                    'href' => 'https://www.smartly.io/',
                    'position' => 'Tier 3 Technical Support Engineer',
                    'responsibilities' => [
                        'Ensured best-in-class technical support and distinguished customer service with lots of debugging.',
                        'Analyzed, reproduced, prioritized, documented, and assisted in fixing issues, including platform bugs.',
                        'Kept product documentation up to date.',
                        'Assisted and trained teammates.',
                    ],
                    'achievements' => '<a href="/static/resume/Snapchat_Essentials.pdf" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Certificate" class="linkIcon">Snapchat Essentials</a>',
                    'description' => null,
                ],
                [
                    'startTime' => null,
                    'endTime' => '2021-10-28',
                    'name' => 'Snap Inc.',
                    'icon' => '/img/social/snapchat.svg',
                    'href' => 'https://www.snapchat.com/',
                    'position' => 'Student',
                    'responsibilities' => null,
                    'achievements' => '<a href="https://focus.snapchat.com/student/award/Mky9cibA5QqZFG6ESU3SQiEy" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Certificate" class="linkIcon">Certificate</a>',
                    'description' => 'Snapchat Essentials',
                ],
                [
                    'startTime' => null,
                    'endTime' => '2022-01-18',
                    'name' => 'Smartly.io',
                    'icon' => '/img/icons/Smartly.svg',
                    'href' => 'https://www.smartly.io/',
                    'position' => 'Student',
                    'responsibilities' => null,
                    'achievements' => '<a href="https://www.credly.com/badges/746c851c-6bb2-4fc1-b3f4-e5902e789654/public_url" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Certificate" class="linkIcon">Certificate</a>',
                    'description' => 'Creative Foundational certification',
                ],
                [
                    'startTime' => '2022-10-26',
                    'endTime' => null,
                    'name' => 'Support from Hel',
                    'icon' => '/img/icons/SupportFromHel.svg',
                    'href' => 'https://supportfromhel.fi/',
                    'position' => 'Founding Member',
                    'responsibilities' => ['Sharing tech support knowledge with fellow tech supporters and specialists from related fields or those inspiring to become ones.'],
                    'achievements' => null,
                    'description' => 'Group for professionals working in customer support to meet, network & learn with industry peers.',
                ],
                [
                    'startTime' => '2023-01-02',
                    'endTime' => null,
                    'name' => 'XAMK',
                    'icon' => '/img/icons/XAMK.svg',
                    'href' => 'https://www.xamk.fi/en/open-amk-courses/introduction-to-video-games-creation/',
                    'position' => 'Student',
                    'responsibilities' => null,
                    'achievements' => null,
                    'description' => 'Introduction to Video Game Creation',
                ],
                [
                    'startTime' => '2023-01-24',
                    'endTime' => '2023-05-11',
                    'name' => 'Arcada',
                    'icon' => '/img/icons/Arcada.svg',
                    'href' => 'https://www.arcada.fi/en/study-arcada/continuing-education/course-calendar/game-design-and-production',
                    'position' => 'Student',
                    'responsibilities' => null,
                    'achievements' => '<a href="/static/resume/ArcadaGameDesignTranscript.jpg" target="_blank"><img loading="lazy" decoding="async" src="/img/certificate.svg" alt="Transcript of records" class="linkIcon">Transcript of records</a>',
                    'description' => 'Game Design and Production',
                ],
                [
                    'startTime' => '2023-04-24',
                    'endTime' => null,
                    'name' => 'Signant Health',
                    'icon' => '/img/icons/SignantHealth.svg',
                    'href' => 'https://www.signanthealth.com/',
                    'position' => 'R&D Support Engineer',
                    'responsibilities' => [
                        'Investigate and resolve infrastructure and application incidents.',
                        'Manage and triage incidents and service requests.',
                        'Determine root cause of issues.',
                    ],
                    'achievements' => null,
                    'description' => null,
                ],
            ];
        $outputArray['contacts'] =
            [
                [
                    'url' => 'https://discord.com/users/851693133040975882/',
                    'img' => '/img/social/discord.svg',
                    'name' => 'Discord',
                    'hidden' => false,
                ],
                [
                    'url' => 'mailto:simbiat@outlook.com',
                    'img' => '/img/social/email.svg',
                    'name' => 'Email',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://facebook.com/SimbiatSoftware/',
                    'img' => '/img/social/facebook.svg',
                    'name' => 'Facebook',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://facebook.com/Simbiat19/',
                    'img' => '/img/social/facebook.svg',
                    'name' => 'Facebook (Personal)',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://github.com/Simbiat',
                    'img' => '/img/social/github.svg',
                    'name' => 'GitHub',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://habr.com/ru/users/Simbiat/',
                    'img' => '/img/social/habr.svg',
                    'name' => 'Habr',
                    'hidden' => true,
                ],
                [
                    'url' => 'https://www.instagram.com/simbiat19/',
                    'img' => '/img/social/instagram.svg',
                    'name' => 'Instagram',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://kitsu.io/users/Simbiat',
                    'img' => '/img/social/kitsu.svg',
                    'name' => 'Kitsu',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://www.linkedin.com/in/simbiat19/',
                    'img' => '/img/social/linkedin.svg',
                    'name' => 'LinkedIn',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://pinterest.com/simbiat19/',
                    'img' => '/img/social/pinterest.svg',
                    'name' => 'Pinterest',
                    'hidden' => true,
                ],
                [
                    'url' => 'https://www.reddit.com/user/Simbiat19',
                    'img' => '/img/social/reddit.svg',
                    'name' => 'Reddit',
                    'hidden' => true,
                ],
                [
                    'url' => 'skype:akuma199?chat',
                    'img' => '/img/social/skype.svg',
                    'name' => 'Skype',
                    'hidden' => true,
                ],
                [
                    'url' => 'https://www.snapchat.com/add/simbiat199',
                    'img' => '/img/social/snapchat.svg',
                    'name' => 'Snapchat',
                    'hidden' => true,
                ],
                [
                    'url' => 'https://stackoverflow.com/users/2992851/',
                    'img' => '/img/social/stackoverflow.svg',
                    'name' => 'Stack Overflow',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://steamcommunity.com/id/Simbiat19',
                    'img' => '/img/social/steam.svg',
                    'name' => 'Steam',
                    'hidden' => false,
                ],
                [
                    'url' => 'tg://resolve?domain=SimbiatSoftware',
                    'img' => '/img/social/telegram.svg',
                    'name' => 'Telegram',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://www.twitch.tv/simbiat19',
                    'img' => '/img/social/twitch.svg',
                    'name' => 'Twitch',
                    'hidden' => true,
                ],
                [
                    'url' => 'https://twitter.com/simbiat199',
                    'img' => '/img/social/twitter.svg',
                    'name' => 'Twitter',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://invite.viber.com/?g2=AQBZepIl4sHyyE27AMP%2FJhSvTIhySSA5KWMcV5NczjT2EbanY0ZoNSndR3g0eJdk',
                    'img' => '/img/social/viber.svg',
                    'name' => 'Viber',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://vk.com/simbiat19',
                    'img' => '/img/social/VK.svg',
                    'name' => 'VKontakte',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://wa.me/79057305159',
                    'img' => '/img/social/whatsapp.svg',
                    'name' => 'WhatsApp',
                    'hidden' => false,
                ],
                [
                    'url' => 'https://www.youtube.com/@simbiat',
                    'img' => '/img/social/youtube.svg',
                    'name' => 'Youtube',
                    'hidden' => false,
                ],
            ];
        return $outputArray;
    }
}
