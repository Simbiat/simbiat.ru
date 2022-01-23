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

/* bictracker/bictracker.twig */
class __TwigTemplate_a0c04f9b4913700725973f8d6f319bc2 extends Template
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
        echo "<section lang=\"ru-RU\">
    <article>
        <details ";
        // line 3
        if ( !($context["subServiceName"] ?? null)) {
            echo "class=\"persistent\" open";
        }
        echo ">
            <summary class=\"rightSummary aboutSection\">О сервисе</summary>
            <p>Это БИК Трекер, предназначенный для отслеживания изменений Банковских Идентификационных Кодов, предоставляемых <a href=\"https://cbr.ru/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/tech/CBR.svg\" alt=\"Центральный Банк Российской Федерации\">Центральным Банком Российской Федерации</a>.</p>
            <p>Библиотека содержит различную информацию, использующейся в банковских расчётах. Трекер предназначен для визуального отображения этой информации, а также отслеживания некоторых изменений.</p>
            <p>У сервиса есть \"официальная\" страница на форуме <a href=\"https://www.banki.ru/forum/?PAGE_NAME=read&FID=29&TID=324456\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/icons/banki.ru.svg\" alt=\"banki.ru\">banki.ru</a></p>
            ";
        // line 8
        if ( !($context["subServiceName"] ?? null)) {
            // line 9
            echo "                <p>Вы находитесь на главной странице сервиса. Для поиска банков используйте <a href=\"/bictracker/search/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">поиск</span></a>. Если вы хотите проверить ключевание счёта, то можете использовать эту <a href=\"/bictracker/keying/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">страницу</span></a>.</p>
            ";
        }
        // line 11
        echo "        </details>
    </article>
    ";
        // line 13
        if ((($context["subServiceName"] ?? null) == "search")) {
            // line 14
            echo "        ";
            echo twig_include($this->env, $context, "common/elements/search.twig");
            echo "
    ";
        }
        // line 16
        echo "    ";
        if (($context["subServiceName"] ?? null)) {
            // line 17
            echo "        ";
            if (($context["pagination"] ?? null)) {
                // line 18
                echo "            ";
                echo call_user_func_array($this->env->getFunction('pagination')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "current", [], "any", false, false, false, 18), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "total", [], "any", false, false, false, 18), 5, array("first" => "<<", "prev" => "<", "next" => ">", "last" => ">>", "first_text" => "First page", "prev_text" => "Previous page", "next_text" => "Next page", "last_text" => "Last page", "page_text" => "Page "), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "prefix", [], "any", false, false, false, 18)]);
                echo "
        ";
            }
            // line 20
            echo "        <article>
            ";
            // line 21
            if ((($context["subServiceName"] ?? null) == "keying")) {
                // line 22
                echo "                ";
                echo twig_include($this->env, $context, "bictracker/keying.twig");
                echo "
            ";
            } elseif ((            // line 23
($context["subServiceName"] ?? null) == "search")) {
                // line 24
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/search.twig");
                echo "
            ";
            } elseif ((            // line 25
($context["subServiceName"] ?? null) == "bic")) {
                // line 26
                echo "                ";
                echo twig_include($this->env, $context, "bictracker/bic.twig");
                echo "
            ";
            } elseif ((            // line 27
($context["subServiceName"] ?? null) == "openbics")) {
                // line 28
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 29
($context["subServiceName"] ?? null) == "closedbics")) {
                // line 30
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            }
            // line 32
            echo "        </article>
        ";
            // line 33
            if (($context["pagination"] ?? null)) {
                // line 34
                echo "            ";
                echo call_user_func_array($this->env->getFunction('pagination')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "current", [], "any", false, false, false, 34), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "total", [], "any", false, false, false, 34), 5, array("first" => "<<", "prev" => "<", "next" => ">", "last" => ">>", "first_text" => "First page", "prev_text" => "Previous page", "next_text" => "Next page", "last_text" => "Last page", "page_text" => "Page "), twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "prefix", [], "any", false, false, false, 34)]);
                echo "
        ";
            }
            // line 36
            echo "    ";
        }
        // line 37
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "bictracker/bictracker.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  134 => 37,  131 => 36,  125 => 34,  123 => 33,  120 => 32,  114 => 30,  112 => 29,  107 => 28,  105 => 27,  100 => 26,  98 => 25,  93 => 24,  91 => 23,  86 => 22,  84 => 21,  81 => 20,  75 => 18,  72 => 17,  69 => 16,  63 => 14,  61 => 13,  57 => 11,  53 => 9,  51 => 8,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "bictracker/bictracker.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\bictracker\\bictracker.twig");
    }
}
