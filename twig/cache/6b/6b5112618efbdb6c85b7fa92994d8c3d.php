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
        echo "<table>
    <thead><tr><th>Email</th><th>Confirmed</th><th>Notifications</th><th>Delete</th></tr></thead>
    ";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["emails"] ?? null));
        foreach ($context['_seq'] as $context["number"] => $context["email"]) {
            // line 4
            echo "        <tr class=\"middle\">
            <td>";
            // line 5
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 5), "html", null, true);
            echo "</td>
            <td>";
            // line 6
            if (twig_get_attribute($this->env, $this->source, $context["email"], "activation", [], "any", false, false, false, 6)) {
                echo "<input type=\"button\" value=\"Confirm\" class=\"mail_activation\" data-email=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 6), "html", null, true);
                echo "\"><img class=\"hidden spinner inline\" src=\"/img/spinner.svg\" alt=\"Activating ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 6), "html", null, true);
                echo "...\">";
            } else {
                echo "✅";
            }
            echo "</td>
            <td";
            // line 7
            if (twig_get_attribute($this->env, $this->source, $context["email"], "activation", [], "any", false, false, false, 7)) {
                echo " class=\"warning\"";
            }
            echo ">";
            if (twig_get_attribute($this->env, $this->source, $context["email"], "activation", [], "any", false, false, false, 7)) {
                echo "Confirm address to change setting";
            } else {
                echo "<span class=\"radio_and_label\"><input id=\"subscription_checkbox_";
                echo twig_escape_filter($this->env, $context["number"], "html", null, true);
                echo "\" class=\"mail_subscription\" data-email=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 7), "html", null, true);
                echo "\" type=\"checkbox\"";
                if (twig_get_attribute($this->env, $this->source, $context["email"], "subscribed", [], "any", false, false, false, 7)) {
                    echo " checked";
                }
                echo "><label for=\"subscription_checkbox_";
                echo twig_escape_filter($this->env, $context["number"], "html", null, true);
                echo "\">";
                if (twig_get_attribute($this->env, $this->source, $context["email"], "subscribed", [], "any", false, false, false, 7)) {
                    echo "Unsubscribe";
                } else {
                    echo "Subscribe";
                }
                echo "</label></span><img class=\"hidden spinner inline\" src=\"/img/spinner.svg\" alt=\"Changing subscription for ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 7), "html", null, true);
                echo "...\">";
            }
            echo "</td>
            <td><input class=\"mail_deletion\" data-email=\"";
            // line 8
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 8), "html", null, true);
            echo "\" type=\"image\" src=\"/img/close.svg\" ";
            if (((twig_get_attribute($this->env, $this->source, $context["email"], "activation", [], "any", false, false, false, 8) && (($context["countActivated"] ?? null) == 0)) || ( !twig_get_attribute($this->env, $this->source, $context["email"], "activation", [], "any", false, false, false, 8) && ((twig_length_filter($this->env, ($context["emails"] ?? null)) < 2) || (($context["countActivated"] ?? null) < 2))))) {
                echo "disabled alt=\"Can't delete\"";
            } else {
                echo "alt=\"Delete ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["email"], "email", [], "any", false, false, false, 8), "html", null, true);
                echo "\"";
            }
            echo "></td>
        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['number'], $context['email'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 11
        echo "</table>
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
        return array (  111 => 11,  94 => 8,  64 => 7,  52 => 6,  48 => 5,  45 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "usercontrol/emails.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\usercontrol\\emails.twig");
    }
}
