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

/* usercontrol/emails.twig */
class __TwigTemplate_a7b8644b19d8087dea3490e7a9c33d59 extends Template
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
        echo "<ul>
    ";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["emails"] ?? null));
        foreach ($context['_seq'] as $context["email"] => $context["code"]) {
            // line 3
            echo "        ";
            if ($context["code"]) {
                // line 4
                echo "            <li class=\"mail_not_activated warning\"><span>";
                echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                echo "</span><input type=\"button\" value=\"Activate\" class=\"mail_activation\" data-email=\"";
                echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                echo "\"><img class=\"hidden spinner inline\" src=\"/img/spinner.svg\" alt=\"Activating ";
                echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                echo "...\"></li>
        ";
            } else {
                // line 6
                echo "            <li class=\"mail_activated nodecor success\" data-tooltip=\"Already activated\"><span>";
                echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                echo "</span></li>
        ";
            }
            // line 8
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['email'], $context['code'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 9
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "usercontrol/emails.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 9,  63 => 8,  57 => 6,  47 => 4,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "usercontrol/emails.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\usercontrol\\emails.twig");
    }
}
