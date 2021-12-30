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

/* common/pages/search.twig */
class __TwigTemplate_bd876f576dc1290f05347d6ecb158d94a3f398eaf7bfe41c3757b40c7af0be45 extends Template
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
        if ((($context["serviceName"] ?? null) == "bictracker")) {
            // line 3
            echo "    ";
            $context["actionURL"] = "/bictracker/search/";
        } elseif ((        // line 4
($context["serviceName"] ?? null) == "fftracker")) {
            // line 5
            echo "        ";
            $context["actionURL"] = "/fftracker/search/";
        } else {
            // line 7
            echo "    ";
            $context["actionURL"] = "/search/";
        }
        // line 10
        echo "<section>
    ";
        // line 11
        if ((($context["serviceName"] ?? null) == "bictracker")) {
            // line 12
            echo "        <p>Введите БИК, SWIFT, название банка, его адрес, регистрационный номер или номер счёта.</p>
    ";
        } elseif ((        // line 13
($context["serviceName"] ?? null) == "fftracker")) {
            // line 14
            echo "        <p>Enter character, achievement or group ID or part of the name, biography, estate message or other description.</p>
    ";
        }
        // line 16
        echo "    <form role=\"search\" class=\"searchForm\" data-baseURL=\"";
        echo twig_escape_filter($this->env, ($context["actionURL"] ?? null), "html", null, true);
        echo "\" action=\"\">
        <span class=\"float_label_div\" id=\"searchLabel\"><input id=\"searchField\" role=\"searchbox\" type=\"search\" inputmode=\"search\" autocomplete=\"on\" maxlength=\"100\"";
        // line 17
        if (array_key_exists("searchvalue", $context)) {
            echo " value=\"";
            echo twig_escape_filter($this->env, ($context["searchvalue"] ?? null), "html", null, true);
            echo "\"";
        } else {
            echo " autofocus";
        }
        echo "><label for=\"searchField\">";
        if ((($context["serviceName"] ?? null) == "bictracker")) {
            echo "Термин для поиска";
        } else {
            echo "Search term";
        }
        echo "</label></span>
        <input role=\"button\" type=\"submit\" id=\"search\" value=\"";
        // line 18
        if ((($context["serviceName"] ?? null) == "bictracker")) {
            echo "Поиск";
        } else {
            echo "Search";
        }
        echo "\">
    </form>
</section>
<section>
    ";
        // line 22
        if ((($context["serviceName"] ?? null) == "bictracker")) {
            // line 23
            echo "        <p class=\"relative\">Библиотека обновлена <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, ($context["bicDate"] ?? null), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, ($context["bicDate"] ?? null), "d.m.Y"), "html", null, true);
            echo "</time>.<input class=\"refresh\" id=\"bicRefresh\" alt=\"Обновить библиотеку\" data-tooltip=\"Обновить библиотеку\" type=\"image\" src=\"/img/refresh.svg\"></p>
    ";
        }
        // line 25
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["searchresult"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["type"] => $context["subResult"]) {
            // line 26
            echo "        <p>
            ";
            // line 27
            if (($context["type"] == "openBics")) {
                // line 28
                echo "                Открытых БИК:
            ";
            } elseif ((            // line 29
$context["type"] == "closedBics")) {
                // line 30
                echo "                Закрытых БИК:
            ";
            } elseif ((            // line 31
$context["type"] == "achievements")) {
                // line 32
                echo "                Achievements:
            ";
            } elseif ((            // line 33
$context["type"] == "characters")) {
                // line 34
                echo "                Characters:
            ";
            } elseif ((            // line 35
$context["type"] == "freecompanies")) {
                // line 36
                echo "                Free Companies:
            ";
            } elseif ((            // line 37
$context["type"] == "pvpteams")) {
                // line 38
                echo "                PvP Teams:
            ";
            } elseif ((            // line 39
$context["type"] == "linkshells")) {
                // line 40
                echo "                Linkshells:
            ";
            }
            // line 42
            echo "            ";
            if ((twig_get_attribute($this->env, $this->source, $context["subResult"], "count", [], "any", false, false, false, 42) > twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["subResult"], "results", [], "any", false, false, false, 42)))) {
                // line 43
                echo "                ";
                // line 44
                echo "                ";
                if (($context["type"] == "openBics")) {
                    // line 45
                    echo "                    ";
                    $context["listURL"] = "/bictracker/open/";
                    // line 46
                    echo "                ";
                } elseif (($context["type"] == "closedBics")) {
                    // line 47
                    echo "                    ";
                    $context["listURL"] = "/bictracker/closed/";
                    // line 48
                    echo "                ";
                } elseif (($context["type"] == "achievements")) {
                    // line 49
                    echo "                    ";
                    $context["listURL"] = "/fftracker/achievements/";
                    // line 50
                    echo "                ";
                } elseif (($context["type"] == "characters")) {
                    // line 51
                    echo "                    ";
                    $context["listURL"] = "/fftracker/characters/";
                    // line 52
                    echo "                ";
                } elseif (($context["type"] == "freecompanies")) {
                    // line 53
                    echo "                    ";
                    $context["listURL"] = "/fftracker/freecompanies/";
                    // line 54
                    echo "                ";
                } elseif (($context["type"] == "pvpteams")) {
                    // line 55
                    echo "                    ";
                    $context["listURL"] = "/fftracker/pvpteams/";
                    // line 56
                    echo "                ";
                } elseif (($context["type"] == "linkshells")) {
                    // line 57
                    echo "                    ";
                    $context["listURL"] = "/fftracker/linkshells/";
                    // line 58
                    echo "                ";
                }
                // line 59
                echo "                ";
                // line 60
                echo "                <a href=\"";
                echo twig_escape_filter($this->env, ($context["listURL"] ?? null), "html", null, true);
                echo twig_escape_filter($this->env, twig_urlencode_filter(($context["searchvalue"] ?? null)), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["subResult"], "count", [], "any", false, false, false, 60), "html", null, true);
                echo "</a>
            ";
            } else {
                // line 62
                echo "                ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["subResult"], "count", [], "any", false, false, false, 62), "html", null, true);
                echo "
            ";
            }
            // line 64
            echo "        </p>
        <div class=\"searchResults\">
            ";
            // line 66
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["subResult"], "results", [], "any", false, false, false, 66));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
                // line 67
                echo "                ";
                echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["entity"]);
                echo "
            ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 69
            echo "        </div>
    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['type'], $context['subResult'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 71
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "common/pages/search.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  285 => 71,  270 => 69,  253 => 67,  236 => 66,  232 => 64,  226 => 62,  217 => 60,  215 => 59,  212 => 58,  209 => 57,  206 => 56,  203 => 55,  200 => 54,  197 => 53,  194 => 52,  191 => 51,  188 => 50,  185 => 49,  182 => 48,  179 => 47,  176 => 46,  173 => 45,  170 => 44,  168 => 43,  165 => 42,  161 => 40,  159 => 39,  156 => 38,  154 => 37,  151 => 36,  149 => 35,  146 => 34,  144 => 33,  141 => 32,  139 => 31,  136 => 30,  134 => 29,  131 => 28,  129 => 27,  126 => 26,  108 => 25,  100 => 23,  98 => 22,  87 => 18,  71 => 17,  66 => 16,  62 => 14,  60 => 13,  57 => 12,  55 => 11,  52 => 10,  48 => 7,  44 => 5,  42 => 4,  39 => 3,  37 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/pages/search.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\pages\\search.twig");
    }
}
