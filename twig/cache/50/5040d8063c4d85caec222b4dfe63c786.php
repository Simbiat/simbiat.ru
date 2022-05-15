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

/* usercontrol/activation.twig */
class __TwigTemplate_c116c3b39ff5e4d71e7bb90e655dd40f extends Template
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
        if (($context["activated"] ?? null)) {
            // line 2
            echo "    <p class=\"success\">Mail ";
            echo twig_escape_filter($this->env, ($context["email"] ?? null), "html", null, true);
            echo " confirmed";
            if (($context["activation"] ?? null)) {
                echo " and account activated";
            }
            echo ".</p>
";
        } else {
            // line 4
            echo "    <p class=\"error\">Failed to confirm any emails.</p>
";
        }
        // line 6
        if (twig_get_attribute($this->env, $this->source, ($context["session_data"] ?? null), "username", [], "any", false, false, false, 6)) {
            // line 7
            echo "    <p>You can check list of all your registered mails <a href=\"\">here</a>.</p>
";
        }
    }

    public function getTemplateName()
    {
        return "usercontrol/activation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 7,  53 => 6,  49 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "usercontrol/activation.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\usercontrol\\activation.twig");
    }
}
