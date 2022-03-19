<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* about/contacts.twig */
class __TwigTemplate_977e6ecaafc4e654d92bccd70c904b73 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<p>If required or desired you can try contacting or following me using the social media below. My level of activity varies depending on the platform, though, thus keep in mind potential delays in answer.</p>
<ul>
    <li><a href=\"https://discord.com/users/851693133040975882/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/discord.svg\" alt=\"Discord\">Discord</a></li>
    <li><a href=\"mailto:simbiat@outlook.com\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/email.svg\" alt=\"Email\">Email</a></li>
    <li><a href=\"https://facebook.com/SimbiatSoftware/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/facebook.svg\" alt=\"Facebook\">Facebook</a></li>
    <li><a href=\"https://facebook.com/Simbiat19/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/facebook.svg\" alt=\"Facebook\">Facebook (Personal)</a></li>
    <li><a href=\"https://github.com/Simbiat\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/github.svg\" alt=\"GitHub\">GitHub</a></li>
    <li><a href=\"https://habr.com/ru/users/Simbiat/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/habr.svg\" alt=\"Habr\">Habr</a></li>
    <li><a href=\"https://www.instagram.com/simbiat19/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/instagram.svg\" alt=\"Instagram\">Instagram</a></li>
    <li><a href=\"https://kitsu.io/users/Simbiat\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/kitsu.svg\" alt=\"Kitsu\">Kitsu</a></li>
    <li><a href=\"https://www.linkedin.com/in/simbiat19/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/linkedin.svg\" alt=\"LinkedIn\">LinkedIn</a></li>
    <li><a href=\"https://pinterest.com/simbiat19/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/pinterest.svg\" alt=\"Pinterest\">Pinterest</a></li>
    <li><a href=\"https://www.reddit.com/user/Simbiat19\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/reddit.svg\" alt=\"Reddit\">Reddit</a></li>
    <li><a href=\"skype:akuma199?chat\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/skype.svg\" alt=\"Skype\">Skype</a></li>
    <li><a href=\"https://www.snapchat.com/add/simbiat199\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/snapchat.svg\" alt=\"Snapchat\">Snapchat</a></li>
    <li><a href=\"https://stackoverflow.com/users/2992851/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/stackoverflow.svg\" alt=\"Stack Overflow\">Stack Overflow</a></li>
    <li><a href=\"https://steamcommunity.com/id/Simbiat19\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/steam.svg\" alt=\"Steam\">Steam</a></li>
    <li><a href=\"tg://resolve?domain=SimbiatSoftware\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/telegram.svg\" alt=\"Telegram\">Telegram</a></li>
    <li><a href=\"https://www.tiktok.com/@simbiat19\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/tiktok.svg\" alt=\"TikTok\">TikTok</a></li>
    <li><a href=\"https://www.twitch.tv/simbiat19\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/twitch.svg\" alt=\"Twitch\">Twitch</a></li>
    <li><a href=\"https://twitter.com/simbiat199\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/twitter.svg\" alt=\"Twitter\">Twitter</a></li>
    <li><a href=\"https://invite.viber.com/?g2=AQBZepIl4sHyyE27AMP%2FJhSvTIhySSA5KWMcV5NczjT2EbanY0ZoNSndR3g0eJdk\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/viber.svg\" alt=\"Viber\">Viber</a></li>
    <li><a href=\"https://vk.com/simbiat19\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/VK.svg\" alt=\"VK\">VKontakte</a></li>
    <li><a href=\"https://wa.me/79057305159\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/whatsapp.svg\" alt=\"WhatsApp\">WhatsApp</a></li>
    <li><a href=\"https://www.youtube.com/channel/UCyzixPty8XEiUWC4c1jns_Q\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/youtube.svg\" alt=\"Youtube\">Youtube</a></li>
</ul>
";
    }

    public function getTemplateName()
    {
        return "about/contacts.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "about/contacts.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\about\\contacts.twig");
    }
}
