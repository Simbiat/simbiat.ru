<?php

declare(strict_types=1);

namespace App\Controller\About;

use App\Controller\Abstracts\StaticPage;

/**
 * Class for the page which is currently used as a home page
 */
final class Me extends StaticPage
{
    // Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/me', 'name' => 'me'],
    ];

    // Sub service name
    protected string $subservice_name = 'me';

    // Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $h1 = 'About me';

    // Page's description. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $og_desc = 'About owner of Simbiat Software';

    // List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/ogimages/jiangshi.webp',
        '/assets/images/ogimages/dden.webp',
        '/assets/images/ogimages/RadicalResonance.png',
        '/assets/images/ogimages/bictracker.webp',
        '/assets/images/ogimages/fftracker.webp',
    ];

    /**
     * Static pages have all the data in Twig templates, thus we usually return the empty array
     *
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        return [
            'carousel_content' => [
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 388,
                    'href' => '/resume/Signant/20250610_1.webp',
                    'thumb' => null,
                    'width' => 471,
                ],
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 431,
                    'href' => '/resume/Signant/20250610_2.webp',
                    'thumb' => null,
                    'width' => 449,
                ],
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 1032,
                    'href' => '/resume/Signant/20241111.webp',
                    'thumb' => null,
                    'width' => 1773,
                ],
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 282,
                    'href' => '/resume/Signant/20240604_1.webp',
                    'thumb' => null,
                    'width' => 650,
                ],
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 223,
                    'href' => '/resume/Signant/20240604_2.webp',
                    'thumb' => null,
                    'width' => 660,
                ],
                [
                    'alt' => 'Feedback from Jaakko Anttonen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jaakko/" target="_blank">Jaakko Anttonen</a>',
                    'height' => 893,
                    'href' => '/resume/Signant/20240424.webp',
                    'thumb' => null,
                    'width' => 1054,
                ],
                [
                    'alt' => 'Feedback from Alexandru Vacaru',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/alexandru-vacaru-tech/" target="_blank">Alexandru Vacaru</a>',
                    'height' => 961,
                    'href' => '/resume/Signant/20240205.webp',
                    'thumb' => null,
                    'width' => 1050,
                ],
                [
                    'alt' => 'Feedback from Jaakko Anttonen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jaakko/" target="_blank">Jaakko Anttonen</a>',
                    'height' => 1008,
                    'href' => '/resume/Signant/20231117.webp',
                    'thumb' => null,
                    'width' => 1067,
                ],
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 255,
                    'href' => '/resume/Signant/20231103_1.webp',
                    'thumb' => null,
                    'width' => 830,
                ],
                [
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'height' => 213,
                    'href' => '/resume/Signant/20231103_2.webp',
                    'thumb' => null,
                    'width' => 1755,
                ],
                [
                    'alt' => 'Feedback from Marcella Armilla',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/marcelladitaarmilla/" target="_blank">Marcella Armilla</a>',
                    'height' => 366,
                    'href' => '/resume/Smartly/20220513_1.webp',
                    'thumb' => null,
                    'width' => 599,
                ],
                [
                    'alt' => 'Feedback from Magalí Gomez',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/magali-gomez/" target="_blank">Magalí Gomez</a>',
                    'height' => 366,
                    'href' => '/resume/Smartly/20220513_2.webp',
                    'thumb' => null,
                    'width' => 599,
                ],
                [
                    'alt' => 'Feedback from Jana Christoviciute',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jana-christoviciute-05ba268b/" target="_blank">Jana Christoviciute</a>',
                    'height' => 417,
                    'href' => '/resume/Smartly/20220429.webp',
                    'thumb' => null,
                    'width' => 597,
                ],
                [
                    'alt' => 'Feedback from Xavier Budan',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/xavierbudan/" target="_blank">Xavier Budan</a>',
                    'height' => 482,
                    'href' => '/resume/Smartly/20220413.webp',
                    'thumb' => null,
                    'width' => 597,
                ],
                [
                    'alt' => 'Feedback from Augustine Lee',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/augustinelee12/" target="_blank">Augustine Lee</a>',
                    'height' => 389,
                    'href' => '/resume/Smartly/20220404.webp',
                    'thumb' => null,
                    'width' => 598,
                ],
                [
                    'alt' => 'Feedback from Ayberk Yerlikaya',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/ayberkyrlky/" target="_blank">Ayberk Yerlikaya</a>',
                    'height' => 358,
                    'href' => '/resume/Smartly/20220304.webp',
                    'thumb' => null,
                    'width' => 598,
                ],
                [
                    'alt' => 'Feedback from Marcella Armilla',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/marcelladitaarmilla/" target="_blank">Marcella Armilla</a>',
                    'height' => 339,
                    'href' => '/resume/Smartly/20220303.webp',
                    'thumb' => null,
                    'width' => 597,
                ],
                [
                    'alt' => 'Feedback from Jarno Marin',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jarnomarin/" target="_blank">Jarno Marin</a>',
                    'height' => 335,
                    'href' => '/resume/Smartly/20220218.webp',
                    'thumb' => null,
                    'width' => 597,
                ],
                [
                    'alt' => 'Feedback from Toivo Vaje',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/toivovaje/" target="_blank">Toivo Vaje</a>',
                    'height' => 444,
                    'href' => '/resume/Smartly/20220216.webp',
                    'thumb' => null,
                    'width' => 597,
                ],
                [
                    'alt' => 'Feedback from Tatu Virtanen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/tatuvirtanen/" target="_blank">Tatu Virtanen</a>',
                    'height' => 389,
                    'href' => '/resume/Smartly/20211126.webp',
                    'thumb' => null,
                    'width' => 598,
                ],
            ],
            'timeline_content' => [
                [
                    'achievements' => [
                        'Blood donor',
                        'Patron for <a href="https://sos-dd.ru" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/SOSVillages.svg" alt="" class="link_icon" width="757" height="606"><span>SOS Children\'s Villages</span></a>',
                        'Patron for <a href="https://www.punainenristi.fi" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/Red Cross.webp" alt="" class="link_icon" width="1200" height="1200"><span>Suomen Punainen Risti</span></a>',
                        'Patron for <a href="https://www.vammr.org" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/VAMMR.webp" alt="" class="link_icon" width="400" height="400"><span>Vancouver Aquarium Marine Mammal Rescue Society</span></a>',
                        'Patron for <a href="https://www.pelastakaalapset.fi" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/Pelastakaa Lapset.webp" alt="" class="link_icon" width="1200" height="1200"><span>Pelastakaa Lapset</span></a>',
                        'Patron for <a href="https://mieli.fi" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/Mieli.svg" alt="" class="link_icon" width="132" height="45"><span>Mieli</span></a>',
                    ],
                    'icon' => '/assets/images/icons/Earth.svg',
                    'name' => 'Human',
                    'start_time' => '1989-05-12 02:00:00',
                ],
                [
                    'achievements' => [
                        '10 years of general education',
                        'High level of English',
                        'Class president in grades 6 to 9',
                        'Participated in school theater with noticeable roles of Famusov (<cite>Grief from the mind</cite>), Zvyagincev (<cite>They Were Fighting for Homeland</cite>), reindeer (<cite>Snow Queen</cite>), Carlo/Geppetto and Karabas-Barabas/Mangiafuoco/Stromboli (<cite>Buratino/Pinocchio</cite>)',
                        '<a href="/resume/MiddleSchool.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    ],
                    'end_time' => '2006-06-23',
                    'href' => 'https://sch1208uv.mskobr.ru/',
                    'icon' => '/assets/images/icons/1208.webp',
                    'name' => 'School №1208',
                    'position' => 'Pupil',
                    'start_time' => '1995-09-01',
                ],
                [
                    'achievements' => [
                        'Rewrote code into libraries and published on <a href="https://github.com/Simbiat" target="_blank"><img crossorigin="anonymous" decoding="async" loading="lazy" class="link_icon" src="/assets/images/social/github.svg" alt="" width="512" height="512"><span>GitHub</span></a>. The current website project is also meant to remain open source unless it can affect security.',
                        'Controlled optimization processes and served as the main developer of the <a href="https://github.com/Simbiat/DarkSteam/" target="_blank"><img crossorigin="anonymous" decoding="async" loading="lazy" class="link_icon" src="/assets/images/social/github.svg" alt="" width="512" height="512"><span>DarkSteam</span></a> project until its closure, including releasing a revamped app version with migration to a web platform to yield a 150x performance increase',
                        'Supported file storage of 8Tbs+',
                        'Administered and moderated a forum of 20,000+ users',
                        'Automated payments and donations via PayPal using vBulletin plugins',
                        'Posted most of the game reviews on <a href="https://steamcommunity.com/id/Simbiat19/recommended/" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" class="link_icon" src="/assets/images/social/steam.svg" alt="" width="512" height="512"><span>Steam</span></a>',
                        'Experimented with narrative in video by creating <a href="https://www.youtube.com/watch?v=AsCOsuaB4IE" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" class="link_icon" src="/assets/images/social/youtube.svg" alt="" width="512" height="512"><span>Welcome To My Crib</span></a> and <a href="https://www.youtube.com/watch?v=Q7fN-XDUMHA&list=PL0KIME6alndX8-8yEqF0c3IbajPJDAvJt" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" class="link_icon" src="/assets/images/social/youtube.svg" alt="" width="512" height="512"><span>Aqua Chronica</span></a> series',
                    ],
                    'icon' => '/assets/images/logo.svg',
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
                    'start_time' => '2002-01-07',
                ],
                [
                    'achievements' => [
                        'Class president since 2nd year',
                        'Graduate work: <cite>Testing of hardware and software solutions for 3-dimensional information representation in a virtual reality system</cite>',
                        '<a href="/resume/Specialist-Diploma.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Diploma</span></a>',
                        '<a href="/resume/Specialist-GPA.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.56',
                    ],
                    'description' => 'Specialization: management and informatics in technical systems',
                    'end_time' => '2011-06-16',
                    'href' => 'https://miem.hse.ru/',
                    'icon' => '/assets/images/icons/MIEM.svg',
                    'name' => 'Moscow Institute of Electronics and Mathematics',
                    'position' => 'Student (specialist)',
                    'start_time' => '2006-09-01',
                ],
                [
                    'achievements' => [
                        'Class president',
                        'Graduate work: <cite>Hardware solutions for 3-dimensional information representation in a virtual reality system</cite>',
                        '<a href="/resume/Bachelor-Diploma.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Diploma</span></a>',
                        '<a href="/resume/Bachelor-GPA.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.52',
                    ],
                    'description' => 'Specialization: automation and management',
                    'end_time' => '2011-06-07',
                    'href' => 'https://miem.hse.ru/',
                    'icon' => '/assets/images/icons/MIEM.svg',
                    'name' => 'Moscow Institute of Electronics and Mathematics',
                    'position' => 'Student (bachelor)',
                    'start_time' => '2007-09-01',
                ],
                [
                    'end_time' => '2009-03-27',
                    'href' => 'https://www.windsor.ru/',
                    'icon' => '/assets/images/icons/Windsor.webp',
                    'name' => 'Windsor',
                    'position' => 'Engineer',
                    'responsibilities' => [
                        'Manage office hardware and software',
                        'Manage company\'s website',
                        'Create digital training courses',
                    ],
                    'start_time' => '2009-02-02',
                ],
                [
                    'achievements' => [
                        'Promoted to day-time operator after approximately 1 year',
                        'Transferred a paper-based checklist used by operators to Excel featuring several automated functions to improve traceability of work',
                    ],
                    'description' => 'Outsourced job for Citi Russia as evening operator.',
                    'end_time' => '2011-05-20',
                    'href' => 'https://www.datafort.ru/',
                    'icon' => '/assets/images/icons/IBS.svg',
                    'name' => 'IBS Datafort',
                    'position' => 'Engineer',
                    'responsibilities' => [
                        'Initiate operations related to End of Day processing',
                        'Monitor continuous night processes',
                        'Level 1 support of subset of regional applications',
                        'Level 1 or level 2 support of local applications',
                    ],
                    'start_time' => '2009-06-04',
                ],
                [
                    'achievements' => [
                        'Migration of clearing processing from Windows XP to Windows 7 and automation of some of the steps',
                        'Expert assistance in refactoring of local application for stability and speed improvements',
                        'Coached several new evening and morning operators',
                    ],
                    'end_time' => '2015-09-14',
                    'href' => 'https://www.citibank.ru/',
                    'icon' => '/assets/images/icons/Citi.svg',
                    'name' => 'Citi',
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
                    'start_time' => '2011-05-23',
                ],
                [
                    'achievements' => [
                        'Automated several manual processes used in the department',
                        'Standardized and optimized server-side scripts',
                        'Successfully managed 30 applications simultaneously, closing decade-long backlog for a handful of them',
                        'Participated in the <cite>Ideation</cite> program as a subject-matter expert for one of the winning ideas',
                        'Single-handedly supported the entire country of Kazakhstan for 2 years, fulfilling various roles including technical support, project manager, application manager, and business analyst',
                    ],
                    'end_time' => '2018-05-15',
                    'href' => 'https://www.citibank.ru/',
                    'icon' => '/assets/images/icons/Citi.svg',
                    'name' => 'Citi',
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
                    'start_time' => '2015-09-15',
                ],
                [
                    'achievements' => [
                        'Closed several potential security issues in Kazakhstan processes',
                        'Negotiated vendor pricing for a project from $100k USD down to $55k USD and led the refactoring of the application',
                        'Participated in <cite>Want to be a leader</cite> program leading my team to first place as early as in the second month of it',
                        'Registered all externally issued certificates in local tracking system',
                    ],
                    'end_time' => '2021-07-23',
                    'href' => 'https://www.citibank.ru/',
                    'icon' => '/assets/images/icons/Citi.svg',
                    'name' => 'Citi',
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
                    'start_time' => '2018-05-16',
                ],
                [
                    'achievements' => '<a href="/resume/Web-Service_Certificate.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    'description' => 'Customized course <cite>Basics of web-services support</cite>, 6 hours',
                    'end_time' => '2020-12-21',
                    'href' => 'https://www.luxoft-training.ru/',
                    'icon' => '/assets/images/icons/Luxoft.svg',
                    'name' => 'Luxoft Training',
                    'position' => 'Student',
                ],
                [
                    'achievements' => [
                        'Drove implementation of Support Handbook, internal collection of manuals and guidelines for customer support.',
                        'Participated in leadership training',
                        '<a href="/resume/Snapchat_Essentials.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Snapchat Essentials</span></a>',
                    ],
                    'end_time' => '2022-06-30',
                    'href' => 'https://www.smartly.io/',
                    'icon' => '/assets/images/icons/Smartly.svg',
                    'name' => 'Smartly.io',
                    'position' => 'Tier 3 Technical Support Engineer',
                    'responsibilities' => [
                        'Ensured best-in-class technical support and distinguished customer service with lots of analysis and debugging.',
                        'Kept product documentation up to date.',
                        'Assisted and trained teammates.',
                    ],
                    'start_time' => '2021-09-20',
                ],
                [
                    'achievements' => '<a href="https://focus.snapchat.com/student/award/Mky9cibA5QqZFG6ESU3SQiEy" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    'description' => 'Snapchat Essentials',
                    'end_time' => '2021-10-28',
                    'href' => 'https://www.snapchat.com/',
                    'icon' => '/assets/images/social/snapchat.svg',
                    'name' => 'Snap Inc.',
                    'position' => 'Student',
                ],
                [
                    'achievements' => '<a href="https://www.credly.com/badges/746c851c-6bb2-4fc1-b3f4-e5902e789654/public_url" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    'description' => 'Creative Foundational certification',
                    'end_time' => '2022-01-18',
                    'href' => 'https://www.smartly.io/',
                    'icon' => '/assets/images/icons/Smartly.svg',
                    'name' => 'Smartly.io',
                    'position' => 'Student',
                ],
                [
                    'description' => 'Group for professionals working in customer support to meet, network & learn with industry peers.',
                    'href' => 'https://supportfromhel.fi/',
                    'icon' => '/assets/images/icons/SupportFromHel.svg',
                    'name' => 'Support from Hel',
                    'position' => 'Founding Member',
                    'responsibilities' => [
                        'Sharing tech support knowledge with fellow tech supporters and specialists from related fields or those inspiring to become ones.',
                    ],
                    'start_time' => '2022-10-26',
                ],
                [
                    'achievements' => '<a href="/resume/ArcadaGameDesignTranscript.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Transcript of records</span></a>',
                    'description' => 'Game Design and Production',
                    'end_time' => '2023-05-11',
                    'href' => 'https://www.arcada.fi/en/study-arcada/continuing-education/course-calendar/game-design-and-production',
                    'icon' => '/assets/images/icons/Arcada.svg',
                    'name' => 'Arcada',
                    'position' => 'Student',
                    'start_time' => '2023-01-24',
                ],
                [
                    'achievements' => '<a href="/resume/SecurityJourney.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>20 certificates</span></a>',
                    'description' => '19 Green Belts and Threat Modeling',
                    'end_time' => '2024-01-26',
                    'href' => 'https://www.securityjourney.com/',
                    'icon' => '/assets/images/icons/SecurityJourney.svg',
                    'name' => 'Security Journey',
                    'position' => 'Student',
                    'start_time' => '2023-10-09',
                ],
                [
                    'achievements' => [
                        'Submitted almost a hundred suggestions for product improvement.',
                        'Helped with several de-escalations of issues with sponsors.',
                        'Wrote multiple knowledgebase articles.',
                    ],
                    'end_time' => '2025-06-30',
                    'href' => 'https://www.signanthealth.com/',
                    'icon' => '/assets/images/icons/SignantHealth.svg',
                    'name' => 'Signant Health',
                    'position' => 'R&D Support Engineer',
                    'responsibilities' => [
                        'Investigate, manage, and triage application incidents and service requests.',
                    ],
                    'start_time' => '2023-04-24',
                ],
                [
                    'description' => 'Beta-testing new game "Bylina" a.k.a "The Epic"',
                    'end_time' => '2024-12-28',
                    'href' => 'https://farfargames.com/',
                    'icon' => '/assets/images/icons/Far Far Games.webp',
                    'name' => 'Far Far Games',
                    'position' => 'Beta-tester',
                    'start_time' => '2024-09-12',
                ],
                [
                    'href' => 'https://www.greensisu.fi/',
                    'icon' => '/assets/images/social/vihreät.svg',
                    'name' => 'Green Sisu',
                    'position' => 'Member',
                    'start_time' => '2024-10-07',
                ],
                [
                    'description' => 'Beta-testing new game "Blue Berry"',
                    'end_time' => '2024-11-27',
                    'href' => 'https://play.google.com/store/apps/details?id=com.carillongames.blueberry',
                    'icon' => '/assets/images/icons/Blue Berry.webp',
                    'name' => 'Carillon Games',
                    'position' => 'Beta-tester',
                    'start_time' => '2024-11-06',
                ],
                [
                    'achievements' => [
                        '<a href="/resume/OHS_Basics.pdf" target="_blank">
							<img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481">
							<span>Occupational Healthcare & Safety Basics</span>
						</a>',
                    ],
                    'href' => 'https://www.signanthealth.com/',
                    'icon' => '/assets/images/icons/SignantHealth.svg',
                    'name' => 'Signant Health',
                    'position' => 'Senior R&D Support Engineer',
                    'responsibilities' => [
                        'Investigate, manage, and triage application incidents and service requests.',
                    ],
                    'start_time' => '2025-07-01',
                ],
            ],
        ];
    }
}
