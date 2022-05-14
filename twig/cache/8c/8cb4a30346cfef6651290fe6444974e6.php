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

/* errors/400.twig */
class __TwigTemplate_14a76e8d753781f910894877c72bee94 extends Template
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
        echo "<p>I do not get what you're saying.</p>
";
        // line 2
        if (($context["reason"] ?? null)) {
            echo "<samp class=\"failure\">";
            echo twig_escape_filter($this->env, ($context["reason"] ?? null), "html", null, true);
            echo "</samp>";
        }
        // line 3
        echo "<code>";
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "</code>
<img loading=\"lazy\" decoding=\"async\" alt=\"do not understand\" src=\"/img/errors/incomprehension.svg\">
";
    }

    public function getTemplateName()
    {
        return "errors/400.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "errors/400.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\errors\\400.twig");
    }
}
