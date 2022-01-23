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

/* fftracker/fftracker.twig */
class __TwigTemplate_bd4300936493dad0e32fedc66d2c1075 extends Template
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
    <article>
        <details ";
        // line 3
        if ( !($context["subServiceName"] ?? null)) {
            echo "class=\"persistent\" open";
        }
        echo ">
            <summary class=\"rightSummary aboutSection\">About</summary>
            <p>Service to track different information for Free Companies (guilds), PvP Teams, Linkshells (chat groups) and individual characters for <b>Final Fantasy XIV</b> online game developed and published by <a href=\"https://www.square-enix.com\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/SquareEnix.png\" alt=\"Square Enix\">Square Enix</a>. Utilizes data grabbed from official <a href=\"https://eu.finalfantasyxiv.com/lodestone\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/lodestone.png\" alt=\"Lodestone\">Lodestone</a> with special <a href=\"https://github.com/Simbiat/lodestone-php\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/github.svg\" alt=\"Github\">parser</a>.</p>
            <p>Service has an official <a href=\"https://forum.square-enix.com/ffxiv/threads/304191-Free-Company-Tracker-Page\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/lodestone.png\" alt=\"Lodestone Forum\">thread</a> on Lodestone forum.</p>
            <div id=\"se_copyright\">\"(C) SQUARE ENIX CO., LTD. All Rights Reserved. FINAL FANTASY is a registered trademark of Square Enix Holdings Co., Ltd. All material used under license.\"</div>
            ";
        // line 8
        if ( !($context["subServiceName"] ?? null)) {
            // line 9
            echo "                <p>Check statistics <a href=\"/fftracker/statistics/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">here</span></a>.</p>
                <p>Browse different entities using links below:</p>
                <ul>
                    <li><a href=\"/fftracker/characters/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Characters</span></a></li>
                    <li><a href=\"/fftracker/freecompanies/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Free Companies</span></a></li>
                    <li><a href=\"/fftracker/pvpteams/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">PvP Teams</span></a></li>
                    <li><a href=\"/fftracker/linkshells/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Linkshells</span></a></li>
                    <li><a href=\"/fftracker/achievements/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Achievements</span></a></li>
                </ul>
                <p>Use <a href=\"/fftracker/search/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">search</span></a> to find something specific or <a href=\"/fftracker/track/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">register</span></a> a new entity, if we do not have it.</p>
            ";
        }
        // line 20
        echo "        </details>
    </article>
    ";
        // line 22
        if ((($context["subServiceName"] ?? null) == "search")) {
            // line 23
            echo "        ";
            echo twig_include($this->env, $context, "common/elements/search.twig");
            echo "
    ";
        }
        // line 25
        echo "    ";
        if (($context["subServiceName"] ?? null)) {
            // line 26
            echo "        ";
            if (($context["pagination"] ?? null)) {
                // line 27
                echo "            ";
                echo call_user_func_array($this->env->getFunction('pagination')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "current", [], "any", false, false, false, 27), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "total", [], "any", false, false, false, 27), 5, array("first" => "<<", "prev" => "<", "next" => ">", "last" => ">>", "first_text" => "First page", "prev_text" => "Previous page", "next_text" => "Next page", "last_text" => "Last page", "page_text" => "Page "), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "prefix", [], "any", false, false, false, 27)]);
                echo "
        ";
            }
            // line 29
            echo "        <article>
            ";
            // line 30
            if ((($context["subServiceName"] ?? null) == "search")) {
                // line 31
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/search.twig");
                echo "
            ";
            } elseif ((            // line 32
($context["subServiceName"] ?? null) == "characters")) {
                // line 33
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 34
($context["subServiceName"] ?? null) == "freecompanies")) {
                // line 35
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 36
($context["subServiceName"] ?? null) == "pvpteams")) {
                // line 37
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 38
($context["subServiceName"] ?? null) == "linkshells")) {
                // line 39
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 40
($context["subServiceName"] ?? null) == "achievements")) {
                // line 41
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 42
($context["subServiceName"] ?? null) == "character")) {
                // line 43
                echo "                ";
                echo twig_include($this->env, $context, "fftracker/character.twig");
                echo "
            ";
            } elseif ((            // line 44
($context["subServiceName"] ?? null) == "linkshell")) {
                // line 45
                echo "                ";
                echo twig_include($this->env, $context, "fftracker/linkshell.twig");
                echo "
            ";
            } elseif ((            // line 46
($context["subServiceName"] ?? null) == "pvpteam")) {
                // line 47
                echo "                ";
                echo twig_include($this->env, $context, "fftracker/pvpteam.twig");
                echo "
            ";
            } elseif ((            // line 48
($context["subServiceName"] ?? null) == "achievement")) {
                // line 49
                echo "                ";
                echo twig_include($this->env, $context, "fftracker/achievement.twig");
                echo "
            ";
            } elseif ((            // line 50
($context["subServiceName"] ?? null) == "freecompany")) {
                // line 51
                echo "                ";
                echo twig_include($this->env, $context, "fftracker/freecompany.twig");
                echo "
            ";
            } elseif ((            // line 52
($context["subServiceName"] ?? null) == "track")) {
                // line 53
                echo "                ";
                echo twig_include($this->env, $context, "fftracker/track.twig");
                echo "
            ";
            }
            // line 55
            echo "        </article>
        ";
            // line 56
            if (($context["pagination"] ?? null)) {
                // line 57
                echo "            ";
                echo call_user_func_array($this->env->getFunction('pagination')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "current", [], "any", false, false, false, 57), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "total", [], "any", false, false, false, 57), 5, array("first" => "<<", "prev" => "<", "next" => ">", "last" => ">>", "first_text" => "First page", "prev_text" => "Previous page", "next_text" => "Next page", "last_text" => "Last page", "page_text" => "Page "), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "prefix", [], "any", false, false, false, 57)]);
                echo "
        ";
            }
            // line 59
            echo "    ";
        }
        // line 60
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/fftracker.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  192 => 60,  189 => 59,  183 => 57,  181 => 56,  178 => 55,  172 => 53,  170 => 52,  165 => 51,  163 => 50,  158 => 49,  156 => 48,  151 => 47,  149 => 46,  144 => 45,  142 => 44,  137 => 43,  135 => 42,  130 => 41,  128 => 40,  123 => 39,  121 => 38,  116 => 37,  114 => 36,  109 => 35,  107 => 34,  102 => 33,  100 => 32,  95 => 31,  93 => 30,  90 => 29,  84 => 27,  81 => 26,  78 => 25,  72 => 23,  70 => 22,  66 => 20,  53 => 9,  51 => 8,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/fftracker.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\fftracker.twig");
    }
}
