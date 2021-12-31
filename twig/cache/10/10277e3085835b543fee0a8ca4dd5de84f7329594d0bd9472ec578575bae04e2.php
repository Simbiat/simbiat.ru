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

/* common/layout/navigation.twig */
class __TwigTemplate_7129bd5d58ffc5899fe456644bb264dfa7fb96f8a9c3d9aa442694a5416c1c8f extends Template
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
        // line 2
        if (($context["http_error"] ?? null)) {
            // line 3
            echo "    ";
            $context["serviceName"] = "";
            // line 4
            echo "    ";
            $context["subServiceName"] = "";
            // line 5
            echo "    ";
            $context["breadcrumbs"] = "";
        }
        // line 7
        echo "<div id=\"hideNav\"><input id=\"hideNavIcon\" class=\"navIcon\" alt=\"Close navigation\" type=\"image\" src=\"/img/close.svg\"></div>
<div class=\"navElement\">
    <div class=\"navLine\" id=\"showSidebar\">
        <a class=\"navItem\" href=\"#\" target=\"_self\" itemprop=\"url\"><img loading=\"lazy\" decoding=\"async\" class=\"navIcon\" alt=\"Login\" data-tooltip=\"Login\" src=\"/img/navigation/login.svg\"><span itemprop=\"name\">Login</span></a>
    </div>
</div>
<ul id=\"navList\">
    <li class=\"navElement\">
        <div class=\"navLine\">
            <a class=\"navItem\" href=\"/\" target=\"_self\" itemprop=\"url\"><img loading=\"lazy\" decoding=\"async\" class=\"navIcon\" alt=\"Home Page\" data-tooltip=\"Home Page\" src=\"/img/navigation/home.svg\"><span itemprop=\"name\">Home Page</span></a>
        </div>
    </li>
    ";
        // line 19
        if ((($context["breadcrumbs"] ?? null) && (($context["breadcrumbsLevels"] ?? null) > 1))) {
            // line 20
            echo "        <li class=\"navElement liBread\">
            <div class=\"navLine navBread\">
                ";
            // line 22
            echo ($context["breadcrumbs"] ?? null);
            echo "
            </div>
        </li>
    ";
        }
        // line 26
        echo "    <!--
    <li class=\"navElement\">
        <div class=\"navLine\">
            <details class=\"navCat\" ";
        // line 29
        if (((($context["serviceName"] ?? null) == "blog") || (($context["serviceName"] ?? null) == "forum"))) {
            echo "open";
        }
        echo ">
                <summary class=\"rightSummary\"><img loading=\"lazy\" decoding=\"async\" class=\"navIcon\" alt=\"Blog\" src=\"/img/navigation/blog.svg\"><span itemprop=\"name\">Blog</span></summary>
                <ul>
                    <li><a class=\"navItem\" href=\"/blog/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">All entries</span></a></li>
                    <li><a class=\"navItem\" href=\"/blog/review/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Reviews</span></a></li>
                    <li><a class=\"navItem\" href=\"/blog/prose/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Prose</span></a></li>
                    <li><a class=\"navItem\" href=\"/blog/poem/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Poems</span></a></li>
                </ul>
            </details>
        </div>
    </li>
    -->
    <li class=\"navElement\">
        <div class=\"navLine\">
            <details class=\"navCat\" ";
        // line 43
        if ((($context["serviceName"] ?? null) == "fftracker")) {
            echo "open";
        }
        echo ">
                <summary class=\"rightSummary\"><img loading=\"lazy\" decoding=\"async\" class=\"navIcon\" alt=\"FFXIV Tracker\" data-tooltip=\"FFXIV Tracker\" src=\"/img/navigation/fftracker.svg\"><span itemprop=\"name\">FFXIV Tracker</span></summary>
                <ul>
                    <li";
        // line 46
        if ((($context["subServiceName"] ?? null) == "statistics")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/statistics/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Statistics</span></a></li>
                    <li";
        // line 47
        if (((($context["serviceName"] ?? null) == "fftracker") && ((($context["subServiceName"] ?? null) == "search") || array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/search/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Search</span></a></li>
                    <li";
        // line 48
        if (((($context["subServiceName"] ?? null) == "character") || ((($context["subServiceName"] ?? null) == "characters") &&  !array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/characters/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Characters</span></a></li>
                    <li";
        // line 49
        if (((($context["subServiceName"] ?? null) == "freecompany") || ((($context["subServiceName"] ?? null) == "freecompanies") &&  !array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/freecompanies/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Free Companies</span></a></li>
                    <li";
        // line 50
        if (((($context["subServiceName"] ?? null) == "pvpteam") || ((($context["subServiceName"] ?? null) == "pvpteams") &&  !array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/pvpteams/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">PvP Teams</span></a></li>
                    <li";
        // line 51
        if (((($context["subServiceName"] ?? null) == "linkshell") || ((($context["subServiceName"] ?? null) == "linkshells") &&  !array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/linkshells/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Linkshells</span></a></li>
                    <li";
        // line 52
        if (((($context["subServiceName"] ?? null) == "achievement") || ((($context["subServiceName"] ?? null) == "achievements") &&  !array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/achievements/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Achievements</span></a></li>
                    <li";
        // line 53
        if ((($context["subServiceName"] ?? null) == "track")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/fftracker/track/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Track entity</span></a></li>
                </ul>
            </details>
        </div>
    </li>
    <li class=\"navElement\" lang=\"ru-RU\">
        <div class=\"navLine\">
            <details class=\"navCat\" ";
        // line 60
        if ((($context["serviceName"] ?? null) == "bictracker")) {
            echo "open";
        }
        echo ">
                <summary class=\"rightSummary\"><img loading=\"lazy\" decoding=\"async\" class=\"navIcon\" alt=\"БИК Трекер\" data-tooltip=\"БИК Трекер\" src=\"/img/navigation/bictracker.svg\"><span itemprop=\"name\">БИК Трекер</span></summary>
                <ul>
                    <li";
        // line 63
        if (((($context["serviceName"] ?? null) == "bictracker") && ((($context["subServiceName"] ?? null) == "search") || array_key_exists("searchvalue", $context)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/bictracker/search/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Поиск</span></a></li>
                    <li";
        // line 64
        if ((((($context["subServiceName"] ?? null) == "openbics") &&  !array_key_exists("searchvalue", $context)) || ((($context["subServiceName"] ?? null) == "bic") &&  !twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateOut", [], "any", false, false, false, 64)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/bictracker/openbics/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Открытые БИК</span></a></li>
                    <li";
        // line 65
        if ((((($context["subServiceName"] ?? null) == "closedbics") &&  !array_key_exists("searchvalue", $context)) || ((($context["subServiceName"] ?? null) == "bic") && twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateOut", [], "any", false, false, false, 65)))) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/bictracker/closedbics/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Закрытые БИК</span></a></li>
                    <li";
        // line 66
        if ((($context["subServiceName"] ?? null) == "keying")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/bictracker/keying/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Ключевание</span></a></li>
                </ul>
            </details>
        </div>
    </li>
    <li class=\"navElement\">
        <div class=\"navLine\">
            <details class=\"navCat\" ";
        // line 73
        if ((($context["serviceName"] ?? null) == "about")) {
            echo "open";
        }
        echo ">
                <summary class=\"rightSummary\"><img loading=\"lazy\" decoding=\"async\" class=\"navIcon\" alt=\"About\" data-tooltip=\"About\" src=\"/img/navigation/about.svg\"><span itemprop=\"name\">About</span></summary>
                <ul>
                    <li";
        // line 76
        if ((($context["subServiceName"] ?? null) == "me")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/me/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Me</span></a></li>
                    <li";
        // line 77
        if ((($context["subServiceName"] ?? null) == "website")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/website/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Website</span></a></li>
                    <li";
        // line 78
        if ((($context["subServiceName"] ?? null) == "tech")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/tech/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Technology</span></a></li>
                    <li";
        // line 79
        if ((($context["subServiceName"] ?? null) == "resume")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/resume/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Resume</span></a></li>
                    <li";
        // line 80
        if ((($context["subServiceName"] ?? null) == "contacts")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/contacts/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Contacts</span></a></li>
                    <li";
        // line 81
        if ((($context["subServiceName"] ?? null) == "tos")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/tos/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Terms of Service</span></a></li>
                    <li";
        // line 82
        if ((($context["subServiceName"] ?? null) == "privacy")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/privacy/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Privacy Policy</span></a></li>
                    <li";
        // line 83
        if ((($context["subServiceName"] ?? null) == "security")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/security/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Security Policy</span></a></li>
                    <li";
        // line 84
        if ((($context["subServiceName"] ?? null) == "changelog")) {
            echo " class=\"current\"";
        }
        echo "><a class=\"navItem\" href=\"/about/changelog/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Changelog</span></a></li>
                </ul>
            </details>
        </div>
    </li>
</ul>
";
    }

    public function getTemplateName()
    {
        return "common/layout/navigation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  256 => 84,  250 => 83,  244 => 82,  238 => 81,  232 => 80,  226 => 79,  220 => 78,  214 => 77,  208 => 76,  200 => 73,  188 => 66,  182 => 65,  176 => 64,  170 => 63,  162 => 60,  150 => 53,  144 => 52,  138 => 51,  132 => 50,  126 => 49,  120 => 48,  114 => 47,  108 => 46,  100 => 43,  81 => 29,  76 => 26,  69 => 22,  65 => 20,  63 => 19,  49 => 7,  45 => 5,  42 => 4,  39 => 3,  37 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/navigation.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\navigation.twig");
    }
}
