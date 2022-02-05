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

/* index.twig */
class __TwigTemplate_a9309cadf4274b8ee6e1d31a35972c7c extends Template
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
        echo "<!DOCTYPE html>
<html lang=\"en\" prefix=\"og: https://ogp.me/ns# fb: https://ogp.me/ns/fb# profile: https://ogp.me/ns/profile# article: https://ogp.me/ns/article# book: https://ogp.me/ns/book# website: https://ogp.me/ns/website#\" itemscope itemtype=\"https://schema.org/Article\">
    <head>
        ";
        // line 4
        echo twig_include($this->env, $context, "common/layout/metatags.twig");
        echo "

        <!-- Generated links -->
        ";
        // line 7
        echo call_user_func_array($this->env->getFunction('linkTags')->getCallable(), [($context["link_tags"] ?? null), "head"]);
        echo "
        ";
        // line 8
        if (($context["link_extra"] ?? null)) {
            // line 9
            echo "            ";
            echo call_user_func_array($this->env->getFunction('linkTags')->getCallable(), [($context["link_extra"] ?? null), "head"]);
            echo "
        ";
        }
        // line 11
        echo "
        <title>";
        // line 12
        if (($context["title"] ?? null)) {
            echo ($context["title"] ?? null);
            if ( !array_key_exists("http_error", $context)) {
                echo " on ";
                echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
            }
        } else {
            echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        }
        echo "</title>

        ";
        // line 14
        echo twig_include($this->env, $context, "common/layout/scripts.twig");
        echo "
        ";
        // line 15
        if (($context["http_error"] ?? null)) {
            // line 16
            echo "            ";
            if (((((($context["http_error"] ?? null) != 400) && (($context["http_error"] ?? null) != 403)) && (($context["http_error"] ?? null) != 404)) && (($context["http_error"] ?? null) != 405))) {
                // line 17
                echo "                <meta http-equiv=\"refresh\" content=\"60\"/>
            ";
            }
            // line 19
            echo "        ";
        }
        // line 20
        echo "    </head>
    ";
        // line 21
        if (($context["unsupported"] ?? null)) {
            // line 22
            echo "        ";
            echo twig_include($this->env, $context, "errors/teapot.twig");
            echo "
    ";
        } else {
            // line 24
            echo "        <body>
            <header>
                ";
            // line 26
            echo twig_include($this->env, $context, "common/layout/header.twig");
            echo "
            </header>
            <nav id=\"navigation\" aria-label=\"Primary\" role=\"navigation\" itemscope=\"\" itemtype=\"https://schema.org/SiteNavigationElement\">
                ";
            // line 29
            echo twig_include($this->env, $context, "common/layout/navigation.twig");
            echo "
            </nav>
            <main id=\"content\" role=\"main\">
                ";
            // line 32
            if (((($context["http_error"] ?? null) && (($context["static_page"] ?? null) == false)) && (($context["cached_page"] ?? null) == false))) {
                // line 33
                echo "                    <article id=\"http_error\">
                        ";
                // line 34
                if ((($context["construction"] ?? null) == false)) {
                    // line 35
                    echo "                            ";
                    echo twig_include($this->env, $context, (("errors/" . ($context["http_error"] ?? null)) . ".twig"));
                    echo "
                        ";
                } else {
                    // line 37
                    echo "                            ";
                    echo twig_include($this->env, $context, "errors/construction.twig");
                    echo "
                        ";
                }
                // line 39
                echo "                        ";
                if ((((( !($context["error_page"] ?? null) && (($context["http_error"] ?? null) != 400)) && (($context["http_error"] ?? null) != 403)) && (($context["http_error"] ?? null) != 404)) && (($context["http_error"] ?? null) != 405))) {
                    // line 40
                    echo "                            <p>Page will refresh in <span id=\"refresh_timer\">60</span> seconds.</p>
                        ";
                }
                // line 42
                echo "                    </article>
                ";
            } else {
                // line 44
                echo "                    ";
                if ((($context["serviceName"] ?? null) == "about")) {
                    // line 45
                    echo "                        ";
                    echo twig_include($this->env, $context, "about/about.twig");
                    echo "
                    ";
                } elseif ((                // line 46
($context["serviceName"] ?? null) == "fftracker")) {
                    // line 47
                    echo "                        ";
                    echo twig_include($this->env, $context, "fftracker/fftracker.twig");
                    echo "
                    ";
                } elseif ((                // line 48
($context["serviceName"] ?? null) == "bictracker")) {
                    // line 49
                    echo "                        ";
                    echo twig_include($this->env, $context, "bictracker/bictracker.twig");
                    echo "
                    ";
                } elseif ((                // line 50
($context["serviceName"] ?? null) == "landing")) {
                    // line 51
                    echo "                        ";
                    echo twig_include($this->env, $context, "landing.twig");
                    echo "
                    ";
                } else {
                    // line 53
                    echo "                        ";
                    echo twig_include($this->env, $context, "stylingTest.twig");
                    echo "
                    ";
                }
                // line 55
                echo "                ";
            }
            // line 56
            echo "            </main>
            <aside id=\"sidebar\" aria-label=\"Sidebar\">
                <div id=\"hideSidebar\"><input id=\"hideSidebarIcon\" class=\"navIcon\" alt=\"Close sidebar\" data-tooltip=\"Close sidebar\" type=\"image\" src=\"/img/close.svg\"></div>
                    ";
            // line 59
            if (((($context["http_error"] ?? null) != "database") && (($context["http_error"] ?? null) != "maintenance"))) {
                // line 60
                echo "                        <section id=\"loginForm\">
                            ";
                // line 61
                echo twig_include($this->env, $context, "common/layout/noscript.twig");
                echo "
                            ";
                // line 62
                echo twig_include($this->env, $context, "common/layout/signinup.twig");
                echo "
                        </section>
                    ";
            } else {
                // line 65
                echo "                        <section id=\"sidebar_for_static\">
                            <div class=\"warning\">Database is currently unavailable, and you are seeing a static or cached page, that is not dependent on it. Sidebar elements suppressed.</div>
                        </section>
                    ";
            }
            // line 69
            echo "            </aside>
            <footer>
                ";
            // line 71
            echo twig_include($this->env, $context, "common/layout/footer.twig");
            echo "
            </footer>
            <div id=\"tooltip\" role=\"tooltip\" aria-label=\"Tooltip\"></div>
            ";
            // line 74
            echo twig_include($this->env, $context, "common/layout/gallery.twig");
            echo "
        </body>
    ";
        }
        // line 77
        echo "</html>
";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  230 => 77,  224 => 74,  218 => 71,  214 => 69,  208 => 65,  202 => 62,  198 => 61,  195 => 60,  193 => 59,  188 => 56,  185 => 55,  179 => 53,  173 => 51,  171 => 50,  166 => 49,  164 => 48,  159 => 47,  157 => 46,  152 => 45,  149 => 44,  145 => 42,  141 => 40,  138 => 39,  132 => 37,  126 => 35,  124 => 34,  121 => 33,  119 => 32,  113 => 29,  107 => 26,  103 => 24,  97 => 22,  95 => 21,  92 => 20,  89 => 19,  85 => 17,  82 => 16,  80 => 15,  76 => 14,  63 => 12,  60 => 11,  54 => 9,  52 => 8,  48 => 7,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "index.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\index.twig");
    }
}
