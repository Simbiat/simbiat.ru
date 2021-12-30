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
class __TwigTemplate_4e1084c05467a784a430ed29d6c3526028e4ba54bfaffa613b011b1f92bba4e9 extends Template
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
        echo ($context["link_tags"] ?? null);
        echo "
        ";
        // line 8
        echo ($context["link_extra"] ?? null);
        echo "

        <title>";
        // line 10
        echo ($context["title"] ?? null);
        echo "</title>

        ";
        // line 12
        echo twig_include($this->env, $context, "common/layout/scripts.twig");
        echo "
        ";
        // line 13
        if (($context["http_error"] ?? null)) {
            // line 14
            echo "            ";
            if ((((($context["http_error"] ?? null) != 400) && (($context["http_error"] ?? null) != 403)) && (($context["http_error"] ?? null) != 404))) {
                // line 15
                echo "                <meta http-equiv=\"refresh\" content=\"60\"/>
            ";
            }
            // line 17
            echo "        ";
        }
        // line 18
        echo "    </head>
    ";
        // line 19
        if (($context["unsupported"] ?? null)) {
            // line 20
            echo "        ";
            echo twig_include($this->env, $context, "errors/teapot.twig");
            echo "
    ";
        } else {
            // line 22
            echo "        <body>
            <header>
                ";
            // line 24
            echo twig_include($this->env, $context, "common/layout/header.twig");
            echo "
            </header>
            <nav id=\"navigation\" aria-label=\"Primary\" role=\"navigation\" itemscope=\"\" itemtype=\"https://schema.org/SiteNavigationElement\">
                ";
            // line 27
            echo twig_include($this->env, $context, "common/layout/navigation.twig");
            echo "
            </nav>
            <main id=\"content\" role=\"main\">
                ";
            // line 30
            if ((($context["http_error"] ?? null) && (($context["static_page"] ?? null) == false))) {
                // line 31
                echo "                    <article id=\"http_error\">
                        ";
                // line 32
                if ((($context["construction"] ?? null) == false)) {
                    // line 33
                    echo "                            ";
                    echo twig_include($this->env, $context, (("errors/" . ($context["http_error"] ?? null)) . ".twig"));
                    echo "
                        ";
                } else {
                    // line 35
                    echo "                            ";
                    echo twig_include($this->env, $context, "errors/construction.twig");
                    echo "
                        ";
                }
                // line 37
                echo "                        ";
                if (((( !($context["error_page"] ?? null) && (($context["http_error"] ?? null) != 400)) && (($context["http_error"] ?? null) != 403)) && (($context["http_error"] ?? null) != 404))) {
                    // line 38
                    echo "                            <p>Page will refresh in <span id=\"refresh_timer\">60</span> seconds.</p>
                        ";
                }
                // line 40
                echo "                    </article>
                ";
            } else {
                // line 42
                echo "                    ";
                if ((($context["serviceName"] ?? null) == "about")) {
                    // line 43
                    echo "                        ";
                    echo twig_include($this->env, $context, "about/about.twig");
                    echo "
                    ";
                } elseif ((                // line 44
($context["serviceName"] ?? null) == "fftracker")) {
                    // line 45
                    echo "                        ";
                    echo twig_include($this->env, $context, "fftracker/fftracker.twig");
                    echo "
                    ";
                } elseif ((                // line 46
($context["serviceName"] ?? null) == "bictracker")) {
                    // line 47
                    echo "                        ";
                    echo twig_include($this->env, $context, "bictracker/bictracker.twig");
                    echo "
                    ";
                } elseif ((                // line 48
($context["serviceName"] ?? null) == "landing")) {
                    // line 49
                    echo "                        ";
                    echo twig_include($this->env, $context, "landing.twig");
                    echo "
                    ";
                } else {
                    // line 51
                    echo "                        ";
                    echo twig_include($this->env, $context, "stylingTest.twig");
                    echo "
                    ";
                }
                // line 53
                echo "                ";
            }
            // line 54
            echo "            </main>
            <aside id=\"sidebar\" aria-label=\"Sidebar\">
                <div id=\"hideSidebar\"><input id=\"hideSidebarIcon\" class=\"navIcon\" alt=\"Close sidebar\" data-tooltip=\"Close sidebar\" type=\"image\" src=\"/img/close.svg\"></div>
                    ";
            // line 57
            if (((($context["http_error"] ?? null) != "database") && (($context["maintenance"] ?? null) == 0))) {
                // line 58
                echo "                        <section id=\"loginForm\">
                            ";
                // line 59
                echo twig_include($this->env, $context, "common/layout/noscript.twig");
                echo "
                            ";
                // line 60
                echo twig_include($this->env, $context, "common/layout/signinup.twig");
                echo "
                        </section>
                    ";
            } else {
                // line 63
                echo "                        <section id=\"sidebar_for_static\">
                            <div class=\"warning\">Database is currently unavailable, and you are seeing a static page, that is not dependent on it. Sidebar elements suppressed.</div>
                        </section>
                    ";
            }
            // line 67
            echo "            </aside>
            <footer>
                ";
            // line 69
            echo twig_include($this->env, $context, "common/layout/footer.twig");
            echo "
            </footer>
            <div id=\"tooltip\" role=\"tooltip\" aria-label=\"Tooltip\"></div>
            ";
            // line 72
            echo twig_include($this->env, $context, "common/layout/gallery.twig");
            echo "
        </body>
    ";
        }
        // line 75
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
        return array (  216 => 75,  210 => 72,  204 => 69,  200 => 67,  194 => 63,  188 => 60,  184 => 59,  181 => 58,  179 => 57,  174 => 54,  171 => 53,  165 => 51,  159 => 49,  157 => 48,  152 => 47,  150 => 46,  145 => 45,  143 => 44,  138 => 43,  135 => 42,  131 => 40,  127 => 38,  124 => 37,  118 => 35,  112 => 33,  110 => 32,  107 => 31,  105 => 30,  99 => 27,  93 => 24,  89 => 22,  83 => 20,  81 => 19,  78 => 18,  75 => 17,  71 => 15,  68 => 14,  66 => 13,  62 => 12,  57 => 10,  52 => 8,  48 => 7,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "index.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\index.twig");
    }
}
