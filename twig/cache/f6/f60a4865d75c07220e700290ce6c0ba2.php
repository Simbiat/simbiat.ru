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

/* fftracker/achievement.twig */
class __TwigTemplate_b081b1c14373529b6aed0540cb046e60 extends Template
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
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "name", [], "any", false, false, false, 2), "html", null, true);
        echo "\" src=\"/img/fftracker/icons/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "icon", [], "any", false, false, false, 2), "html", null, true);
        echo "\">
    <p><b>\"";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "name", [], "any", false, false, false, 3), "html", null, true);
        echo "\"</b> is a <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "subcategory", [], "any", false, false, false, 3), "html", null, true);
        echo "</i> achievement in <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "category", [], "any", false, false, false, 3), "html", null, true);
        echo "</i> category. It was added to the roaster on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "registered", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "registered", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
        echo "</time> with ID <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "id", [], "any", false, false, false, 3), "html", null, true);
        echo "</i>";
        if ((twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "registered", [], "any", false, false, false, 3), "Y-m-d") != twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "updated", [], "any", false, false, false, 3), "Y-m-d"))) {
            echo " and revised on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "updated", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "updated", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
            echo "</time>";
        }
        echo ".</p>
    <p>By achieving it, one can receive:</p>
    <ul>
        ";
        // line 6
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 6), "points", [], "any", false, false, false, 6)) {
            // line 7
            echo "            <li><i class=\"gold\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 7), "points", [], "any", false, false, false, 7), "html", null, true);
            echo "</i> points</li>
        ";
        }
        // line 9
        echo "        ";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 9), "title", [], "any", false, false, false, 9)) {
            // line 10
            echo "            <li><i class=\"gold\">\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 10), "title", [], "any", false, false, false, 10), "html", null, true);
            echo "\"</i> title</li>
        ";
        }
        // line 12
        echo "        ";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 12), "item", [], "any", false, false, false, 12), "name", [], "any", false, false, false, 12)) {
            // line 13
            echo "            <li><a class=\"gold\" href=\"https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 13), "item", [], "any", false, false, false, 13), "id", [], "any", false, false, false, 13), "html", null, true);
            echo "/\" target=\"_blank\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/fftracker/icons/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 13), "item", [], "any", false, false, false, 13), "icon", [], "any", false, false, false, 13), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 13), "item", [], "any", false, false, false, 13), "name", [], "any", false, false, false, 13), "html", null, true);
            echo "\"><i>\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "rewards", [], "any", false, false, false, 13), "item", [], "any", false, false, false, 13), "name", [], "any", false, false, false, 13), "html", null, true);
            echo "\"</i></a> as a trophy</li>
        ";
        }
        // line 15
        echo "    </ul>
    ";
        // line 16
        if (twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "howto", [], "any", false, false, false, 16)) {
            // line 17
            echo "        <p>But it won't be easy to reap these rewards. As per established rules, one is required to fulfill these conditions:</p>
        <blockquote>";
            // line 18
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "howto", [], "any", false, false, false, 18), "html", null, true);
            echo "</blockquote>
    ";
        } else {
            // line 20
            echo "        <p>But it won't be easy to reap these rewards. What makes that even harder, is that it is still unknown, what the requirements are.</p>
    ";
        }
        // line 22
        echo "    ";
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "characters", [], "any", false, false, false, 22), "total", [], "any", false, false, false, 22) > 0)) {
            // line 23
            echo "        <p>Achievement has been earned by <i class=\"success\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "characters", [], "any", false, false, false, 23), "total", [], "any", false, false, false, 23), "html", null, true);
            echo "</i> ";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "characters", [], "any", false, false, false, 23), "total", [], "any", false, false, false, 23) > 1)) {
                echo "people";
            } else {
                echo "person";
            }
            echo ".</p>
        <p>";
            // line 24
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "characters", [], "any", false, false, false, 24), "total", [], "any", false, false, false, 24) > 1)) {
                echo "Last <i>";
                echo twig_escape_filter($this->env, twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "characters", [], "any", false, false, false, 24), "last", [], "any", false, false, false, 24)), "html", null, true);
                echo "</i> people";
            } else {
                echo "The only person";
            }
            echo ", who received the achievement:</p>
        <div class=\"searchResults\">
            ";
            // line 26
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["achievement"] ?? null), "characters", [], "any", false, false, false, 26), "last", [], "any", false, false, false, 26));
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
                // line 27
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
            // line 29
            echo "        </div>
    ";
        }
        // line 31
        echo "
</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/achievement.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  182 => 31,  178 => 29,  161 => 27,  144 => 26,  133 => 24,  122 => 23,  119 => 22,  115 => 20,  110 => 18,  107 => 17,  105 => 16,  102 => 15,  90 => 13,  87 => 12,  81 => 10,  78 => 9,  72 => 7,  70 => 6,  46 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/achievement.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\achievement.twig");
    }
}
