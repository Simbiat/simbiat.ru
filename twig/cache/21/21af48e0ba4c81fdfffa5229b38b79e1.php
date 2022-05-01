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

/* usercontrol/registration.twig */
class __TwigTemplate_5a74ec8305cb4eb5a0caa468a6d50989 extends Template
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
        if (twig_get_attribute($this->env, $this->source, ($context["session_data"] ?? null), "username", [], "any", false, false, false, 1)) {
            // line 2
            echo "    <div class=\"success\">Hey, ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["session_data"] ?? null), "username", [], "any", false, false, false, 2), "html", null, true);
            echo "! You are already registered and logged in, nothing else is required.</div>
";
        } else {
            // line 4
            echo "    ";
            if ((($context["registration"] ?? null) == 0)) {
                // line 5
                echo "        <div class=\"warning\">Registration is currently closed.</div>
    ";
            } else {
                // line 7
                echo "        <div>Please, use the login/registration form in the sidebar. If you're using a smaller screen, you may need to open navigation bar first using the hamburger button at the top left.</div>
    ";
            }
        }
    }

    public function getTemplateName()
    {
        return "usercontrol/registration.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 7,  48 => 5,  45 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "usercontrol/registration.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\usercontrol\\registration.twig");
    }
}
