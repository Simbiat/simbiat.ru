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
class __TwigTemplate_3ca0a67a390d6f2111906533c624b156eddec0c99bb4ce1b92db841c6257ef50 extends Template
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
            echo ($context["pagination_top"] ?? null);
            echo "
        <article>
            ";
            // line 19
            if ((($context["subServiceName"] ?? null) == "keying")) {
                // line 20
                echo "                ";
                echo twig_include($this->env, $context, "bictracker/keying.twig");
                echo "
            ";
            } elseif ((            // line 21
($context["subServiceName"] ?? null) == "search")) {
                // line 22
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/search.twig");
                echo "
            ";
            } elseif ((            // line 23
($context["subServiceName"] ?? null) == "bic")) {
                // line 24
                echo "                ";
                echo twig_include($this->env, $context, "bictracker/bic.twig");
                echo "
            ";
            } elseif ((            // line 25
($context["subServiceName"] ?? null) == "open")) {
                // line 26
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            } elseif ((            // line 27
($context["subServiceName"] ?? null) == "closed")) {
                // line 28
                echo "                ";
                echo twig_include($this->env, $context, "common/pages/list.twig");
                echo "
            ";
            }
            // line 30
            echo "        </article>
        ";
            // line 31
            echo ($context["pagination_bottom"] ?? null);
            echo "
    ";
        }
        // line 33
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
        return array (  122 => 33,  117 => 31,  114 => 30,  108 => 28,  106 => 27,  101 => 26,  99 => 25,  94 => 24,  92 => 23,  87 => 22,  85 => 21,  80 => 20,  78 => 19,  72 => 17,  69 => 16,  63 => 14,  61 => 13,  57 => 11,  53 => 9,  51 => 8,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "bictracker/bictracker.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\bictracker\\bictracker.twig");
    }
}
