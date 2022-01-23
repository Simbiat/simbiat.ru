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

/* about/resume.twig */
class __TwigTemplate_68b7fc13d69ce1e0e81e8887ad27689a extends Template
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
        echo "<section>
    <h2>Informal introduction</h2>
    <p>While my \"on paper\" experience is mainly application support in a bank, in reality I had to take on [portions] of other roles like developer, tester, application manager (akin to product owner), project manager, business/system analyst, security specialist, technical writer, processes architect, supplier manager and \"coordinator\" for different types of activities related to the above, including multi-department, multi-business and multi-country ones.</p>
    <p>I code for a hobby while posting most of my code on <a href=\"https://github.com/Simbiat\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/github.svg\" alt=\"GitHub\">GitHub</a> and <a href=\"https://codepen.io/Simbiat\" target=\"_blank\"><img loading=\"lazy\" class=\"linkIcon\" src=\"/img/icons/codepen.svg\" alt=\"Codepen\">Codepen</a>. It is mainly PHP, but you can find some code in JavaScript and Visual Basic (.NET, script and for applications).</p>
    <p>I am empathetic and analytical, which helps with understanding of the feelings of others, where they come from and what they may, potentially, want or, at least, expect. Among other things the combination of these two has found its ways into my prose (and some poems) both in Russian and English. I also have some experience in \"level design\", which included rooms/caves for <i>The Elder Scrolls III: Morrowind</i> and our guild house in <i>Final Fantasy XIV</i>.</p>
    <p>Besides that I got some level of skill with graphics and video editing, although it's a far cry from what professional editors have.</p>
    <p>Below you can find a more \"formal\" resume tailored with the help of <a href=\"https://www.topresume.com/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/TopResume.svg\" alt=\"TopResume\">TopResume</a>.</p>
</section>
<section>
    <h2>Profile</h2>
    <p>Diligent, client-oriented individual equipped with strong skills in analytics, problem-solving, coding, and programming. Experienced senior-level technical support professional with a record of achievements across project management, application, security, and business analytics. Strong negotiator, adept mentor, and reputable independent worker.</p>
    <h2>Languages</h2>
    <ul class=\"zeroMargin tagList\">
        <li><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/flags/Russia.svg\" alt=\"Russian\">Russian (native)</li>
        <li><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/flags/United-Kingdom.svg\" alt=\"English\">English (fluent)</li>
        <li><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/flags/Germany.svg\" alt=\"German\">German (basic at most)</li>
    </ul>
    <h2>Core competencies</h2>
    <ul class=\"zeroMargin tagList\">
        <li>Technical Support</li>
        <li>Data & Analytics</li>
        <li>Programming</li>
        <li>Fullstack Development</li>
        <li>Project Management</li>
        <li>Application Management</li>
        <li>Business Analytics</li>
        <li>System Integration</li>
    </ul>
    <h2>Technical proficiencies</h2>
    <ul class=\"zeroMargin tagList\">
        <li>PHP</li>
        <li>SQL</li>
        <li>HTML5</li>
        <li>CSS3</li>
        <li>JavaScript</li>
        <li>Visual Basic</li>
        <li>CMD</li>
        <li>Adobe Photoshop</li>
        <li>Adobe Premiere</li>
        <li>IBM ConnectDirect</li>
        <li>IBM MQ</li>
        <li>SAP Business Objects</li>
        <li>MS Office</li>
    </ul>
    <p>More on <a href=\"https://app.pluralsight.com/profile/Simbiat\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/Pluralsight.svg\" alt=\"PluralSight\">PluralSight</a>.</p>
    <h2>Experience timeline</h2>
    ";
        // line 47
        echo call_user_func_array($this->env->getFunction('timeline')->getCallable(), [($context["timeline"] ?? null), "Y-m-d", false, "en", 6]);
        echo "
</section>
<section>
    <h2>Alternative links</h2>
    <p>My resume can be alternatively be checked on website below, but it can be outdated there.</p>
    <ul>
        <li><a href=\"https://www.linkedin.com/in/simbiat19/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/linkedin.svg\" alt=\"LinkedIn\">LinkedIn</a></li>
        <li><a href=\"https://career.habr.com/simbiat/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/habr.svg\" alt=\"Habr\">Habr</a></li>
        <li><a href=\"https://stackoverflow.com/users/story/2992851/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/stackoverflow.svg\" alt=\"Stack Overflow\">Stack Overflow</a></li>
        <li>
            <p><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/hh.ru.svg\" alt=\"HeadHunter\">hh.ru:</p>
            <ul>
                <li><a href=\"https://hh.ru/resume/dcd2c6feff08f4b82d0039ed1f726b55337633/\" target=\"_blank\">Middle PHP-разработчик</a></li>
                <li><a href=\"https://hh.ru/resume/f5d8e190ff02e025be0039ed1f306a6e614258/\" target=\"_blank\">Системный аналитик</a></li>
                <li><a href=\"https://hh.ru/resume/0de6b45fff082667550039ed1f72586d6a5257/\" target=\"_blank\">Технический писатель</a></li>
                <li><a href=\"https://hh.ru/resume/818946e1ff08febbd10039ed1f58754a354a4b/\" target=\"_blank\">Тестировщик ПО</a></li>
                <li><a href=\"https://hh.ru/resume/0e5c3b94ff08fa6f8c0039ed1f453131586e59/\" target=\"_blank\">Специалист технической поддержки</a></li>
                <li><a href=\"https://hh.ru/resume/a7178fb0ff050e2e320039ed1f6b4547384572/\" target=\"_blank\">Systems analyst</a></li>
            </ul>
        </li>
    </ul>
</section>
<section>
    <h2>Downloads</h2>
    <p>My resume can be also be downloaded as:</p>
    <ul>
        <li><a href=\"/static/resume/Resume.pdf\" download><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/PDF.svg\" alt=\"PDF\">PDF</a></li>
        <li><a href=\"/static/resume/Resume.docx\" download><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/DOCX.svg\" alt=\"DOCX\">DOCX</a></li>
    </ul>
</section>
";
    }

    public function getTemplateName()
    {
        return "about/resume.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 47,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "about/resume.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\about\\resume.twig");
    }
}
