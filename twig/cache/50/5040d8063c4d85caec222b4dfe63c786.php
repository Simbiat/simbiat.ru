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
            echo "    <div class=\"success\">Mail ";
            echo twig_escape_filter($this->env, ($context["email"] ?? null), "html", null, true);
            echo " confirmed";
            if (($context["activation"] ?? null)) {
                echo " and account activated";
            }
            echo ".</div>
";
        } else {
            // line 4
            echo "    ";
            if (($context["emails"] ?? null)) {
                // line 5
                echo "        <ul>
            ";
                // line 6
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["emails"] ?? null));
                foreach ($context['_seq'] as $context["email"] => $context["code"]) {
                    // line 7
                    echo "                <li class=";
                    if ( !$context["code"]) {
                        echo "\"mail_activated nodecor success\" data-tooltip=\"Already activated\"";
                    } else {
                        echo "\"mail_not_activated warning\"";
                    }
                    echo "><span>";
                    echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                    echo "</span>";
                    if ($context["code"]) {
                        echo "<input type=\"button\" value=\"Activate\" class=\"mail_activation\" data-email=\"";
                        echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                        echo "\"><img class=\"hidden spinner inline\" src=\"/img/spinner.svg\" alt=\"Activating ";
                        echo twig_escape_filter($this->env, $context["email"], "html", null, true);
                        echo "...\">";
                    }
                    echo "</li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['email'], $context['code'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 9
                echo "        </ul>
    ";
            } else {
                // line 11
                echo "        <div class=\"error\">Failed to confirm any emails.</div>
    ";
            }
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
        return array (  86 => 11,  82 => 9,  59 => 7,  55 => 6,  52 => 5,  49 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "usercontrol/activation.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\usercontrol\\activation.twig");
    }
}
