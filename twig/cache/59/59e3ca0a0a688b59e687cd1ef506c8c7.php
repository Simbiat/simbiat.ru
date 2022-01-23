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

/* fftracker/pvpteam.twig */
class __TwigTemplate_73332d7eb94cd9b02557a9e91af8d077 extends Template
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
    <img loading=\"lazy\" decoding=\"async\" class=\"float_left ff_crest\" alt=\"";
        // line 2
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "name", [], "any", false, false, false, 2), "html", null, true);
        echo "\" src=\"/img/fftracker/merged-crests/";
        echo twig_escape_filter($this->env, twig_slice($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "crest", [], "any", false, false, false, 2), 0, 2), "html", null, true);
        echo "/";
        echo twig_escape_filter($this->env, twig_slice($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "crest", [], "any", false, false, false, 2), 2, 2), "html", null, true);
        echo "/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "crest", [], "any", false, false, false, 2), "html", null, true);
        echo ".png\">
    <p>PvP Team <b>\"";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "name", [], "any", false, false, false, 3), "html", null, true);
        echo "\"</b> was ";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "formed", [], "any", false, false, false, 3)) {
            echo "formed on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "formed", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "formed", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
            echo "</time> and ";
        }
        echo "registered on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "registered", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "registered", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
        echo "</time> with ID <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "id", [], "any", false, false, false, 3), "html", null, true);
        echo "</i>. Last set of interviews conducted on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "updated", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "updated", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
        echo "</time>.";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "deleted", [], "any", false, false, false, 3)) {
            echo " Was disbanded on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "deleted", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 3), "deleted", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
            echo "</time>.";
        }
        echo "</p>
    <p>Operate";
        // line 4
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 4), "deleted", [], "any", false, false, false, 4)) {
            echo "d";
        } else {
            echo "s";
        }
        echo " on <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dataCenter", [], "any", false, false, false, 4), "html", null, true);
        echo "</i>.";
        if ( !twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "dates", [], "any", false, false, false, 4), "deleted", [], "any", false, false, false, 4)) {
            if (twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "community", [], "any", false, false, false, 4)) {
                echo " Has an open <a href=\"https://eu.finalfantasyxiv.com/lodestone/community_finder/";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "community", [], "any", false, false, false, 4), "html", null, true);
                echo "\" target=\"_blank\">community</a>.";
            }
        }
        echo "</p>
    ";
        // line 5
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "oldNames", [], "any", false, false, false, 5)) > 0)) {
            // line 6
            echo "        <p>Had also been known under <i>";
            echo twig_escape_filter($this->env, twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "oldNames", [], "any", false, false, false, 6)), "html", null, true);
            echo "</i> other name";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "oldNames", [], "any", false, false, false, 6)) > 1)) {
                echo "s";
            }
            echo ":</p>
        <ul>
            ";
            // line 8
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "oldNames", [], "any", false, false, false, 8));
            foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                // line 9
                echo "                <li>";
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            echo "        </ul>
    ";
        }
        // line 13
        echo "    <p>Current members:</p>
        <div class=\"searchResults\">
            ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["pvpteam"] ?? null), "members", [], "any", false, false, false, 15));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 16
            echo "                ";
            echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["entity"]);
            echo "
            ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        echo "        </div>

</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/pvpteam.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  165 => 18,  148 => 16,  131 => 15,  127 => 13,  123 => 11,  114 => 9,  110 => 8,  100 => 6,  98 => 5,  80 => 4,  50 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/pvpteam.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\pvpteam.twig");
    }
}
