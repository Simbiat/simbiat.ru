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

/* about/security.txt.twig */
class __TwigTemplate_7d531a6fa8fdbe4ba6b77f0c17e33b66 extends Template
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
        echo "Contact: https://www.simbiat.ru/about/contacts/
Expires: ";
        // line 2
        echo twig_escape_filter($this->env, ($context["expires"] ?? null), "html", null, true);
        echo "
Acknowledgments: https://www.simbiat.ru/about/tech/
Preferred-Languages: en, ru
Canonical: https://www.simbiat.ru/.well-known/security.txt
Policy: https://www.simbiat.ru/about/security/
";
    }

    public function getTemplateName()
    {
        return "about/security.txt.twig";
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
        return new Source("", "about/security.txt.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\about\\security.txt.twig");
    }
}
