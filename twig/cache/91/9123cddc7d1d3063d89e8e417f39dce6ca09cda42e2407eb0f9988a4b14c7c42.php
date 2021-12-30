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

/* errors/construction.twig */
class __TwigTemplate_24dde427a24933c2a7f9589ecedd3a1f5cd4751297c81af42ac2a4ab831803c4 extends Template
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
        echo "<p>The page is currently under (re)construction.</p>
<code>";
        // line 2
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "</code>
<img loading=\"lazy\" decoding=\"async\" alt=\"maintenance\" src=\"/img/errors/construction.svg\">
";
    }

    public function getTemplateName()
    {
        return "errors/construction.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "errors/construction.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\errors\\construction.twig");
    }
}
