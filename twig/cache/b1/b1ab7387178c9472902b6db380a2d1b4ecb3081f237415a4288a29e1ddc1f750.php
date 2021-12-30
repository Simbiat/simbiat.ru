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

/* fftracker/linkshell.twig */
class __TwigTemplate_42f700d726833aa2f20cca2d036c4a738e530f45710368761088b7b1a756740b extends Template
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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "name", [], "any", false, false, false, 2), "html", null, true);
        echo "\" src=\"
        ";
        // line 3
        if (twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "crossworld", [], "any", false, false, false, 3)) {
            // line 4
            echo "            /img/fftracker/crossworld_linkshell.png
        ";
        } else {
            // line 6
            echo "            /img/fftracker/linkshell.png
        ";
        }
        // line 8
        echo "    \">
    <p>";
        // line 9
        if (twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "crossworld", [], "any", false, false, false, 9)) {
            echo "Crossworld ";
        }
        echo "Linkshell <b>\"";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "name", [], "any", false, false, false, 9), "html", null, true);
        echo "\"</b> was ";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "formed", [], "any", false, false, false, 9)) {
            echo "formed on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "formed", [], "any", false, false, false, 9), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "formed", [], "any", false, false, false, 9), "d/m/Y"), "html", null, true);
            echo "</time> and ";
        }
        echo "registered on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "registered", [], "any", false, false, false, 9), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "registered", [], "any", false, false, false, 9), "d/m/Y"), "html", null, true);
        echo "</time> with ID <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "id", [], "any", false, false, false, 9), "html", null, true);
        echo "</i>. Last set of interviews conducted on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "updated", [], "any", false, false, false, 9), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "updated", [], "any", false, false, false, 9), "d/m/Y"), "html", null, true);
        echo "</time>.";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "deleted", [], "any", false, false, false, 9)) {
            echo " Was disbanded on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "deleted", [], "any", false, false, false, 9), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 9), "deleted", [], "any", false, false, false, 9), "d/m/Y"), "html", null, true);
            echo "</time>.";
        }
        echo "</p>
    <p>Operate";
        // line 10
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 10), "deleted", [], "any", false, false, false, 10)) {
            echo "d";
        } else {
            echo "s";
        }
        echo " on <i>";
        echo twig_escape_filter($this->env, (((twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dataCenter", [], "any", true, true, false, 10) &&  !(null === twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dataCenter", [], "any", false, false, false, 10)))) ? (twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dataCenter", [], "any", false, false, false, 10)) : (twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "server", [], "any", false, false, false, 10))), "html", null, true);
        echo "</i>.";
        if ( !twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "dates", [], "any", false, false, false, 10), "deleted", [], "any", false, false, false, 10)) {
            if (twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "community", [], "any", false, false, false, 10)) {
                echo " Has an open <a href=\"https://eu.finalfantasyxiv.com/lodestone/community_finder/";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "community", [], "any", false, false, false, 10), "html", null, true);
                echo "\" target=\"_blank\">community</a>.";
            }
        }
        echo "</p>
    ";
        // line 11
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "oldNames", [], "any", false, false, false, 11)) > 0)) {
            // line 12
            echo "        <p>Had also been known under <i>";
            echo twig_escape_filter($this->env, twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "oldNames", [], "any", false, false, false, 12)), "html", null, true);
            echo "</i> other name";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "oldNames", [], "any", false, false, false, 12)) > 1)) {
                echo "s";
            }
            echo ":</p>
        <ul>
            ";
            // line 14
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "oldNames", [], "any", false, false, false, 14));
            foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                // line 15
                echo "                <li>";
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 17
            echo "        </ul>
    ";
        }
        // line 19
        echo "    <p>Current members:</p>
        <div class=\"searchResults\">
            ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["linkshell"] ?? null), "members", [], "any", false, false, false, 21));
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
            // line 22
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
        // line 24
        echo "        </div>

</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/linkshell.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  176 => 24,  159 => 22,  142 => 21,  138 => 19,  134 => 17,  125 => 15,  121 => 14,  111 => 12,  109 => 11,  91 => 10,  57 => 9,  54 => 8,  50 => 6,  46 => 4,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/linkshell.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\linkshell.twig");
    }
}
