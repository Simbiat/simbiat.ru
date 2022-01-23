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

/* common/layout/header.twig */
class __TwigTemplate_c1223311141c1cc8cf8ab9656fb0cb63 extends Template
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
        echo "<div id=\"header_logo\">
    <span>Simbiat</span>
    <div id=\"logoNav\"><input id=\"showNav\" class=\"navIcon\" alt=\"Show navigation\" data-tooltip=\"Show navigation\" type=\"image\" src=\"/img/menu.svg\"><img loading=\"lazy\" decoding=\"async\" alt=\"logo\" id=\"logoIcon\" data-tooltip=\"Simbiat Software\" src=\"/img/logo.svg\"></div>
    <span>Software</span>
</div>
<div id=\"h1div\">
    <h1 id=\"h1title\">";
        // line 7
        if (($context["cacheReset"] ?? null)) {
            echo "<a href=\"";
            echo twig_escape_filter($this->env, ($context["cacheReset"] ?? null), "html", null, true);
            echo "\" target=\"_self\" data-tooltip=\"Click to attempt to reset page's cache\"><img class=\"linkIcon\" src=\"/img/refresh.svg\" alt=\"Reset cache\">";
        }
        echo _twig_default_filter(((array_key_exists("h1", $context)) ? (_twig_default_filter(($context["h1"] ?? null), ($context["title"] ?? null))) : (($context["title"] ?? null))), ($context["site_name"] ?? null));
        if (($context["cacheReset"] ?? null)) {
            echo "</a>";
        }
        echo "</h1><img loading=\"lazy\" decoding=\"async\" id=\"shareButton\" class=\"hidden\" alt=\"Share page\" data-tooltip=\"Share page\" src=\"/img/share.svg\">
</div>
";
    }

    public function getTemplateName()
    {
        return "common/layout/header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/header.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\header.twig");
    }
}
