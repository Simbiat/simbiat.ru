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

/* errors/500.twig */
class __TwigTemplate_8cfbc2b21674cbd033064eb0037dbea8d60f832cfcdfdcd051b834ece361916d extends Template
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
        echo "<p>Something went wrong, possibly a bug.</p>
<code>";
        // line 2
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "</code>
<img loading=\"lazy\" decoding=\"async\" alt=\"internal server error\" src=\"/img/errors/bug.svg\">
";
    }

    public function getTemplateName()
    {
        return "errors/500.twig";
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
        return new Source("", "errors/500.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\errors\\500.twig");
    }
}
