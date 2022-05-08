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

/* errors/403.twig */
class __TwigTemplate_0ae1d643616dd97cfbff984506ff8f83 extends Template
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
        echo "<p>Access prohibited. ";
        if ( !twig_get_attribute($this->env, $this->source, ($context["session_data"] ?? null), "username", [], "any", false, false, false, 1)) {
            echo "Login or contact";
        } else {
            echo "Contact";
        }
        echo " administration, if you think this is a mistake.</p>
<code>";
        // line 2
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "</code>
<img loading=\"lazy\" decoding=\"async\" alt=\"no access\" src=\"/img/errors/denied.svg\">
";
    }

    public function getTemplateName()
    {
        return "errors/403.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "errors/403.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\errors\\403.twig");
    }
}
