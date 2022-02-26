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

/* sitemap.twig */
class __TwigTemplate_b0469ef5499a8ee07e797f7b6d2b3981 extends Template
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
        if ((($context["format"] ?? null) == "html")) {
            // line 2
            echo "    <section>
        <article>
            <ul>
                ";
            // line 5
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["sitemap_links"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["entity"]) {
                // line 6
                echo "                    <li>
                        <a class=\"sitemaplink\" id=\"sitemaplink_";
                // line 7
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "\" href=\"";
                echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
                echo "/";
                if (($context["index"] ?? null)) {
                    echo "sitemap/";
                    echo twig_escape_filter($this->env, ($context["format"] ?? null), "html", null, true);
                    echo "/";
                }
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "loc", [], "any", false, false, false, 7), "html", null, true);
                echo "\" target=\"_blank\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "name", [], "any", false, false, false, 7), "html", null, true);
                echo "</a>
                    </li>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['entity'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 10
            echo "            </ul>
        </article>
    </section>
";
        } elseif ((        // line 13
($context["format"] ?? null) == "xml")) {
            // line 14
            if (($context["index"] ?? null)) {
                // line 15
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<sitemapindex xmlns=\"https://www.sitemaps.org/schemas/sitemap/0.9\">
";
                // line 17
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["sitemap_links"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
                    // line 18
                    echo "    <sitemap>
        <loc>";
                    // line 19
                    echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
                    echo "/sitemap/";
                    echo twig_escape_filter($this->env, ($context["format"] ?? null), "html", null, true);
                    echo "/";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "loc", [], "any", false, false, false, 19), "html", null, true);
                    echo "</loc>
    </sitemap>
";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 22
                echo "</sitemapindex>
";
            } else {
                // line 24
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"https://www.sitemaps.org/schemas/sitemap/0.9\">
";
                // line 26
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["sitemap_links"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
                    // line 27
                    echo "    <url>
        <loc>";
                    // line 28
                    echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
                    echo "/";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "loc", [], "any", false, false, false, 28), "html", null, true);
                    echo "</loc>
";
                    // line 29
                    if (twig_get_attribute($this->env, $this->source, $context["entity"], "lastmod", [], "any", false, false, false, 29)) {
                        // line 30
                        echo "    <lastmod>";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "lastmod", [], "any", false, false, false, 30), "c"), "html", null, true);
                        echo "</lastmod>
";
                    }
                    // line 32
                    if (twig_get_attribute($this->env, $this->source, $context["entity"], "changefreq", [], "any", false, false, false, 32)) {
                        // line 33
                        echo "        <changefreq>";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "changefreq", [], "any", false, false, false, 33), "html", null, true);
                        echo "</changefreq>
";
                    }
                    // line 35
                    echo "    </url>
";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 37
                echo "</urlset>
";
            }
        } elseif ((        // line 39
($context["format"] ?? null) == "txt")) {
            // line 40
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["sitemap_links"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
                // line 41
                echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
                echo "/";
                if (($context["index"] ?? null)) {
                    echo "sitemap/";
                    echo twig_escape_filter($this->env, ($context["format"] ?? null), "html", null, true);
                    echo "/";
                }
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "loc", [], "any", false, false, false, 41), "html", null, true);
                echo "
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    public function getTemplateName()
    {
        return "sitemap.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  158 => 41,  154 => 40,  152 => 39,  148 => 37,  141 => 35,  135 => 33,  133 => 32,  127 => 30,  125 => 29,  119 => 28,  116 => 27,  112 => 26,  108 => 24,  104 => 22,  91 => 19,  88 => 18,  84 => 17,  80 => 15,  78 => 14,  76 => 13,  71 => 10,  51 => 7,  48 => 6,  44 => 5,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "sitemap.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\sitemap.twig");
    }
}
