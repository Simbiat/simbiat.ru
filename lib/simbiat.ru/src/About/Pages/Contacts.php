<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class Contacts extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/contacts', 'name' => 'Contacts']
    ];
    #Sub service name
    protected string $subServiceName = 'contacts';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Contacts';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Contacts';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Contacts';
    #Flag to indicate this is a static page
    protected bool $static = true;

    protected function generate(array $path): array
    {
        return ['contacts' =>
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
                    'url' => 'https://www.youtube.com/channel/UCyzixPty8XEiUWC4c1jns_Q',
                    'img' => '/img/social/youtube.svg',
                    'name' => 'Youtube',
                    'hidden' => false,
                ],
            ]
        ];
    }
}
