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

/* usercontrol/usercontrol.twig */
class __TwigTemplate_339c668f5c5dae281e58e2af4c801d14 extends Template
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
        echo "<section>
    ";
        // line 2
        if (($context["subServiceName"] ?? null)) {
            // line 3
            echo "        <article>
            ";
            // line 4
            if ((($context["subServiceName"] ?? null) == "registration")) {
                // line 5
                echo "                ";
                echo twig_include($this->env, $context, "usercontrol/registration.twig");
                echo "
            ";
            } elseif ((            // line 6
($context["subServiceName"] ?? null) == "activation")) {
                // line 7
                echo "                ";
                echo twig_include($this->env, $context, "usercontrol/activation.twig");
                echo "
            ";
            } elseif ((            // line 8
($context["subServiceName"] ?? null) == "emails")) {
                // line 9
                echo "                ";
                echo twig_include($this->env, $context, "usercontrol/emails.twig");
                echo "
            ";
            } elseif ((            // line 10
($context["subServiceName"] ?? null) == "unsubscribe")) {
                // line 11
                echo "                ";
                echo twig_include($this->env, $context, "usercontrol/unsubscribe.twig");
                echo "
            ";
            }
            // line 13
            echo "        </article>
    ";
        }
        // line 15
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "usercontrol/usercontrol.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 15,  74 => 13,  68 => 11,  66 => 10,  61 => 9,  59 => 8,  54 => 7,  52 => 6,  47 => 5,  45 => 4,  42 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "usercontrol/usercontrol.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\usercontrol\\usercontrol.twig");
    }
}
