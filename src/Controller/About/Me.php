<?php
declare(strict_types = 1);

namespace App\Controller\About;

use App\Controller\Abstracts\StaticPage;

/**
 * Class for the page which is currently used as a home page
 */
class Me extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/me', 'name' => 'me']
    ];
    #Sub service name
    protected string $subservice_name = 'me';
    #Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $h1 = 'About me';
    #Page's description. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $og_desc = 'About owner of Simbiat Software';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/ogimages/jiangshi.webp',
        '/assets/images/ogimages/dden.webp',
        '/assets/images/ogimages/RadicalResonance.png',
        '/assets/images/ogimages/bictracker.webp',
        '/assets/images/ogimages/fftracker.webp',
    ];
    
    /**
     * Static pages have all the data in Twig templates, thus we usually return the empty array
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        return [
            'timeline_content' => [
                [
                    'start_time' => '1989-05-12 02:00:00',
                    'name' => 'Human',
                    'icon' => '/assets/images/icons/Earth.svg',
                    'achievements' => [
                        'Blood donor',
                        'Patron for <a href="https://sos-dd.ru" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/SOSVillages.svg" alt="" class="link_icon" width="757" height="606"><span>SOS Children\'s Villages</span></a>',
                        'Patron for <a href="https://www.punainenristi.fi" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/Red Cross.webp" alt="" class="link_icon" width="1200" height="1200"><span>Suomen Punainen Risti</span></a>',
                        'Patron for <a href="https://www.vammr.org" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/VAMMR.webp" alt="" class="link_icon" width="400" height="400"><span>Vancouver Aquarium Marine Mammal Rescue Society</span></a>',
                        'Patron for <a href="https://www.pelastakaalapset.fi" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/Pelastakaa Lapset.webp" alt="" class="link_icon" width="1200" height="1200"><span>Pelastakaa Lapset</span></a>',
                        'Patron for <a href="https://mieli.fi" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/icons/Mieli.svg" alt="" class="link_icon" width="132" height="45"><span>Mieli</span></a>'
                    ],
                ],
                [
                    'start_time' => '1995-09-01',
                    'end_time' => '2006-06-23',
                    'name' => 'School №1208',
                    'icon' => '/assets/images/icons/1208.webp',
                    'href' => 'https://sch1208uv.mskobr.ru/',
                    'position' => 'Pupil',
                    'achievements' => [
                        '10 years of general education',
                        'High level of English',
                        'Class president in grades 6 to 9',
                        'Participated in school theater with noticeable roles of Famusov (<cite>Grief from the mind</cite>), Zvyagincev (<cite>They Were Fighting for Homeland</cite>), reindeer (<cite>Snow Queen</cite>), Carlo/Geppetto and Karabas-Barabas/Mangiafuoco/Stromboli (<cite>Buratino/Pinocchio</cite>)',
                        '<a href="/resume/MiddleSchool.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    ],
                ],
                [
                    'start_time' => '2002-01-07',
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
                    'achievements' => [
                        'Rewrote code into libraries and published on <a href="https://github.com/Simbiat" target="_blank"><img crossorigin="anonymous" decoding="async" loading="lazy" class="link_icon" src="/assets/images/social/github.svg" alt="" width="512" height="512"><span>GitHub</span></a>. The current website project is also meant to remain open source unless it can affect security.',
                        'Controlled optimization processes and served as the main developer of the <a href="https://github.com/Simbiat/DarkSteam/" target="_blank"><img crossorigin="anonymous" decoding="async" loading="lazy" class="link_icon" src="/assets/images/social/github.svg" alt="" width="512" height="512"><span>DarkSteam</span></a> project until its closure, including releasing a revamped app version with migration to a web platform to yield a 150x performance increase',
                        'Supported file storage of 8Tbs+',
                        'Administered and moderated a forum of 20,000+ users',
                        'Automated payments and donations via PayPal using vBulletin plugins',
                        'Posted most of the game reviews on <a href="https://steamcommunity.com/id/Simbiat19/recommended/" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" class="link_icon" src="/assets/images/social/steam.svg" alt="" width="512" height="512"><span>Steam</span></a>',
                        'Experimented with narrative in video by creating <a href="https://www.youtube.com/watch?v=AsCOsuaB4IE" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" class="link_icon" src="/assets/images/social/youtube.svg" alt="" width="512" height="512"><span>Welcome To My Crib</span></a> and <a href="https://www.youtube.com/watch?v=Q7fN-XDUMHA&list=PL0KIME6alndX8-8yEqF0c3IbajPJDAvJt" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" class="link_icon" src="/assets/images/social/youtube.svg" alt="" width="512" height="512"><span>Aqua Chronica</span></a> series',
                    ],
                ],
                [
                    'start_time' => '2006-09-01',
                    'end_time' => '2011-06-16',
                    'name' => 'Moscow Institute of Electronics and Mathematics',
                    'icon' => '/assets/images/icons/MIEM.svg',
                    'href' => 'https://miem.hse.ru/',
                    'position' => 'Student (specialist)',
                    'achievements' => [
                        'Class president since 2nd year',
                        'Graduate work: <cite>Testing of hardware and software solutions for 3-dimensional information representation in a virtual reality system</cite>',
                        '<a href="/resume/Specialist-Diploma.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Diploma</span></a>',
                        '<a href="/resume/Specialist-GPA.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.56',
                    ],
                    'description' => 'Specialization: management and informatics in technical systems',
                ],
                [
                    'start_time' => '2007-09-01',
                    'end_time' => '2011-06-07',
                    'name' => 'Moscow Institute of Electronics and Mathematics',
                    'icon' => '/assets/images/icons/MIEM.svg',
                    'href' => 'https://miem.hse.ru/',
                    'position' => 'Student (bachelor)',
                    'achievements' => [
                        'Class president',
                        'Graduate work: <cite>Hardware solutions for 3-dimensional information representation in a virtual reality system</cite>',
                        '<a href="/resume/Bachelor-Diploma.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Diploma</span></a>',
                        '<a href="/resume/Bachelor-GPA.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><abbr data-tooltip="Grade Point Average">GPA</abbr></a> 3.52',
                    ],
                    'description' => 'Specialization: automation and management',
                ],
                [
                    'start_time' => '2009-02-02',
                    'end_time' => '2009-03-27',
                    'name' => 'Windsor',
                    'icon' => '/assets/images/icons/Windsor.webp',
                    'href' => 'https://www.windsor.ru/',
                    'position' => 'Engineer',
                    'responsibilities' => [
                        'Manage office hardware and software',
                        'Manage company\'s website',
                        'Create digital training courses',
                    ],
                ],
                [
                    'start_time' => '2009-06-04',
                    'end_time' => '2011-05-20',
                    'name' => 'IBS Datafort',
                    'icon' => '/assets/images/icons/IBS.svg',
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
                    'start_time' => '2011-05-23',
                    'end_time' => '2015-09-14',
                    'name' => 'Citi',
                    'icon' => '/assets/images/icons/Citi.svg',
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
                ],
                [
                    'start_time' => '2015-09-15',
                    'end_time' => '2018-05-15',
                    'name' => 'Citi',
                    'icon' => '/assets/images/icons/Citi.svg',
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
                        'Participated in the <cite>Ideation</cite> program as a subject-matter expert for one of the winning ideas',
                        'Single-handedly supported the entire country of Kazakhstan for 2 years, fulfilling various roles including technical support, project manager, application manager, and business analyst',
                    ],
                ],
                [
                    'start_time' => '2018-05-16',
                    'end_time' => '2021-07-23',
                    'name' => 'Citi',
                    'icon' => '/assets/images/icons/Citi.svg',
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
                        'Participated in <cite>Want to be a leader</cite> program leading my team to first place as early as in the second month of it',
                        'Registered all externally issued certificates in local tracking system',
                    ],
                ],
                [
                    'end_time' => '2020-12-21',
                    'name' => 'Luxoft Training',
                    'icon' => '/assets/images/icons/Luxoft.svg',
                    'href' => 'https://www.luxoft-training.ru/',
                    'position' => 'Student',
                    'achievements' => '<a href="/resume/Web-Service_Certificate.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    'description' => 'Customized course <cite>Basics of web-services support</cite>, 6 hours',
                ],
                [
                    'start_time' => '2021-09-20',
                    'end_time' => '2022-06-30',
                    'name' => 'Smartly.io',
                    'icon' => '/assets/images/icons/Smartly.svg',
                    'href' => 'https://www.smartly.io/',
                    'position' => 'Tier 3 Technical Support Engineer',
                    'responsibilities' => [
                        'Ensured best-in-class technical support and distinguished customer service with lots of analysis and debugging.',
                        'Kept product documentation up to date.',
                        'Assisted and trained teammates.',
                    ],
                    'achievements' => [
                        'Drove implementation of Support Handbook, internal collection of manuals and guidelines for customer support.',
                        'Participated in leadership training',
                        '<a href="/resume/Snapchat_Essentials.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Snapchat Essentials</span></a>'
                    ],
                ],
                [
                    'end_time' => '2021-10-28',
                    'name' => 'Snap Inc.',
                    'icon' => '/assets/images/social/snapchat.svg',
                    'href' => 'https://www.snapchat.com/',
                    'position' => 'Student',
                    'achievements' => '<a href="https://focus.snapchat.com/student/award/Mky9cibA5QqZFG6ESU3SQiEy" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    'description' => 'Snapchat Essentials',
                ],
                [
                    'end_time' => '2022-01-18',
                    'name' => 'Smartly.io',
                    'icon' => '/assets/images/icons/Smartly.svg',
                    'href' => 'https://www.smartly.io/',
                    'position' => 'Student',
                    'achievements' => '<a href="https://www.credly.com/badges/746c851c-6bb2-4fc1-b3f4-e5902e789654/public_url" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Certificate</span></a>',
                    'description' => 'Creative Foundational certification',
                ],
                [
                    'start_time' => '2022-10-26',
                    'name' => 'Support from Hel',
                    'icon' => '/assets/images/icons/SupportFromHel.svg',
                    'href' => 'https://supportfromhel.fi/',
                    'position' => 'Founding Member',
                    'responsibilities' => [
                        'Sharing tech support knowledge with fellow tech supporters and specialists from related fields or those inspiring to become ones.'
                    ],
                    'description' => 'Group for professionals working in customer support to meet, network & learn with industry peers.',
                ],
                [
                    'start_time' => '2023-01-24',
                    'end_time' => '2023-05-11',
                    'name' => 'Arcada',
                    'icon' => '/assets/images/icons/Arcada.svg',
                    'href' => 'https://www.arcada.fi/en/study-arcada/continuing-education/course-calendar/game-design-and-production',
                    'position' => 'Student',
                    'achievements' => '<a href="/resume/ArcadaGameDesignTranscript.jpg" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>Transcript of records</span></a>',
                    'description' => 'Game Design and Production',
                ],
                [
                    'start_time' => '2023-10-09',
                    'end_time' => '2024-01-26',
                    'name' => 'Security Journey',
                    'icon' => '/assets/images/icons/SecurityJourney.svg',
                    'href' => 'https://www.securityjourney.com/',
                    'position' => 'Student',
                    'achievements' => '<a href="/resume/SecurityJourney.pdf" target="_blank"><img crossorigin="anonymous" loading="lazy" decoding="async" src="/assets/images/certificate.svg" alt="" class="link_icon" width="481" height="481"><span>20 certificates</span></a>',
                    'description' => '19 Green Belts and Threat Modeling',
                ],
                [
                    'start_time' => '2023-04-24',
                    'end_time' => '2025-06-30',
                    'name' => 'Signant Health',
                    'icon' => '/assets/images/icons/SignantHealth.svg',
                    'href' => 'https://www.signanthealth.com/',
                    'position' => 'R&D Support Engineer',
                    'responsibilities' => [
                        'Investigate, manage, and triage application incidents and service requests.',
                    ],
                    'achievements' => [
                        'Submitted almost a hundred suggestions for product improvement.',
                        'Helped with several de-escalations of issues with sponsors.',
                        'Wrote multiple knowledgebase articles.',
                    ],
                ],
                [
                    'start_time' => '2024-09-12',
                    'end_time' => '2024-12-28',
                    'name' => 'Far Far Games',
                    'icon' => '/assets/images/icons/Far Far Games.webp',
                    'href' => 'https://farfargames.com/',
                    'position' => 'Beta-tester',
                    'description' => 'Beta-testing new game "Bylina" a.k.a "The Epic"',
                ],
                [
                    'start_time' => '2024-10-07',
                    'name' => 'Green Sisu',
                    'icon' => '/assets/images/social/vihreät.svg',
                    'href' => 'https://www.greensisu.fi/',
                    'position' => 'Member',
                ],
                [
                    'start_time' => '2024-11-06',
                    'end_time' => '2024-11-27',
                    'name' => 'Carillon Games',
                    'icon' => '/assets/images/icons/Blue Berry.webp',
                    'href' => 'https://play.google.com/store/apps/details?id=com.carillongames.blueberry',
                    'position' => 'Beta-tester',
                    'description' => 'Beta-testing new game "Blue Berry"',
                ],
                [
                    'start_time' => '2025-07-01',
                    'name' => 'Signant Health',
                    'icon' => '/assets/images/icons/SignantHealth.svg',
                    'href' => 'https://www.signanthealth.com/',
                    'position' => 'Senior R&D Support Engineer',
                    'responsibilities' => [
                        'Investigate, manage, and triage application incidents and service requests.',
                    ],
                ]
            ],
            'carousel_content' => [
                [
                    'href' => '/resume/Signant/20250610_1.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 471,
                    'height' => 388
                ],
                [
                    'href' => '/resume/Signant/20250610_2.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 449,
                    'height' => 431
                ],
                [
                    'href' => '/resume/Signant/20241111.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 1773,
                    'height' => 1032
                ],
                [
                    'href' => '/resume/Signant/20240604_1.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 650,
                    'height' => 282
                ],
                [
                    'href' => '/resume/Signant/20240604_2.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 660,
                    'height' => 223
                ],
                [
                    'href' => '/resume/Signant/20240424.webp',
                    'alt' => 'Feedback from Jaakko Anttonen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jaakko/" target="_blank">Jaakko Anttonen</a>',
                    'thumb' => null,
                    'width' => 1054,
                    'height' => 893
                ],
                [
                    'href' => '/resume/Signant/20240205.webp',
                    'alt' => 'Feedback from Alexandru Vacaru',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/alexandru-vacaru-tech/" target="_blank">Alexandru Vacaru</a>',
                    'thumb' => null,
                    'width' => 1050,
                    'height' => 961
                ],
                [
                    'href' => '/resume/Signant/20231117.webp',
                    'alt' => 'Feedback from Jaakko Anttonen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jaakko/" target="_blank">Jaakko Anttonen</a>',
                    'thumb' => null,
                    'width' => 1067,
                    'height' => 1008
                ],
                [
                    'href' => '/resume/Signant/20231103_1.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 830,
                    'height' => 255
                ],
                [
                    'href' => '/resume/Signant/20231103_2.webp',
                    'alt' => 'Feedback from Mika Nuutilainen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/mikanuu/" target="_blank">Mika Nuutilainen</a>',
                    'thumb' => null,
                    'width' => 1755,
                    'height' => 213
                ],
                [
                    'href' => '/resume/Smartly/20220513_1.webp',
                    'alt' => 'Feedback from Marcella Armilla',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/marcelladitaarmilla/" target="_blank">Marcella Armilla</a>',
                    'thumb' => null,
                    'width' => 599,
                    'height' => 366
                ],
                [
                    'href' => '/resume/Smartly/20220513_2.webp',
                    'alt' => 'Feedback from Magalí Gomez',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/magali-gomez/" target="_blank">Magalí Gomez</a>',
                    'thumb' => null,
                    'width' => 599,
                    'height' => 366
                ],
                [
                    'href' => '/resume/Smartly/20220429.webp',
                    'alt' => 'Feedback from Jana Christoviciute',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jana-christoviciute-05ba268b/" target="_blank">Jana Christoviciute</a>',
                    'thumb' => null,
                    'width' => 597,
                    'height' => 417
                ],
                [
                    'href' => '/resume/Smartly/20220413.webp',
                    'alt' => 'Feedback from Xavier Budan',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/xavierbudan/" target="_blank">Xavier Budan</a>',
                    'thumb' => null,
                    'width' => 597,
                    'height' => 482
                ],
                [
                    'href' => '/resume/Smartly/20220404.webp',
                    'alt' => 'Feedback from Augustine Lee',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/augustinelee12/" target="_blank">Augustine Lee</a>',
                    'thumb' => null,
                    'width' => 598,
                    'height' => 389
                ],
                [
                    'href' => '/resume/Smartly/20220304.webp',
                    'alt' => 'Feedback from Ayberk Yerlikaya',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/ayberkyrlky/" target="_blank">Ayberk Yerlikaya</a>',
                    'thumb' => null,
                    'width' => 598,
                    'height' => 358
                ],
                [
                    'href' => '/resume/Smartly/20220303.webp',
                    'alt' => 'Feedback from Marcella Armilla',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/marcelladitaarmilla/" target="_blank">Marcella Armilla</a>',
                    'thumb' => null,
                    'width' => 597,
                    'height' => 339
                ],
                [
                    'href' => '/resume/Smartly/20220218.webp',
                    'alt' => 'Feedback from Jarno Marin',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/jarnomarin/" target="_blank">Jarno Marin</a>',
                    'thumb' => null,
                    'width' => 597,
                    'height' => 335
                ],
                [
                    'href' => '/resume/Smartly/20220216.webp',
                    'alt' => 'Feedback from Toivo Vaje',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/toivovaje/" target="_blank">Toivo Vaje</a>',
                    'thumb' => null,
                    'width' => 597,
                    'height' => 444
                ],
                [
                    'href' => '/resume/Smartly/20211126.webp',
                    'alt' => 'Feedback from Tatu Virtanen',
                    'caption' => 'Feedback from <a href="https://www.linkedin.com/in/tatuvirtanen/" target="_blank">Tatu Virtanen</a>',
                    'thumb' => null,
                    'width' => 598,
                    'height' => 389
                ]
            ]
        ];
    }
}
