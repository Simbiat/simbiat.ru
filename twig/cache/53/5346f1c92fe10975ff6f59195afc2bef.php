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
class __TwigTemplate_6427224035a4d0c5886f1fa8542580b8 extends Template
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
            echo " on ";
            echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
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
                } elseif ((                // line 52
($context["serviceName"] ?? null) == "sitemap")) {
                    // line 53
                    echo "                        ";
                    echo twig_include($this->env, $context, "common/pages/sitemap.twig");
                    echo "
                    ";
                } elseif ((                // line 54
($context["serviceName"] ?? null) == "uc")) {
                    // line 55
                    echo "                        ";
                    echo twig_include($this->env, $context, "usercontrol/usercontrol.twig");
                    echo "
                    ";
                } elseif ((                // line 56
($context["serviceName"] ?? null) == "stylingTest")) {
                    // line 57
                    echo "                        ";
                    echo twig_include($this->env, $context, "stylingTest.twig");
                    echo "
                    ";
                } else {
                    // line 59
                    echo "                        <article id=\"http_error\">
                            ";
                    // line 60
                    echo twig_include($this->env, $context, "errors/404.twig");
                    echo "
                        </article>
                    ";
                }
                // line 63
                echo "                ";
            }
            // line 64
            echo "            </main>
            <aside id=\"sidebar\" aria-label=\"Sidebar\">
                <div id=\"hideSidebar\"><input id=\"hideSidebarIcon\" class=\"navIcon\" alt=\"Close sidebar\" data-tooltip=\"Close sidebar\" type=\"image\" src=\"/img/close.svg\"></div>
                    ";
            // line 67
            if (((($context["http_error"] ?? null) != "database") && (($context["http_error"] ?? null) != "maintenance"))) {
                // line 68
                echo "                        <section id=\"loginForm\">
                            ";
                // line 69
                echo twig_include($this->env, $context, "common/layout/noscript.twig");
                echo "
                            ";
                // line 70
                echo twig_include($this->env, $context, "common/layout/signinup.twig");
                echo "
                        </section>
                    ";
            } else {
                // line 73
                echo "                        <section id=\"sidebar_for_static\">
                            <div class=\"warning\">Database is currently unavailable, and you are seeing a static or cached page, that is not dependent on it. Sidebar elements suppressed.</div>
                        </section>
                    ";
            }
            // line 77
            echo "            </aside>
            <footer>
                ";
            // line 79
            echo twig_include($this->env, $context, "common/layout/footer.twig");
            echo "
            </footer>
            <div id=\"tooltip\" role=\"tooltip\" aria-label=\"Tooltip\"></div>
            ";
            // line 82
            echo twig_include($this->env, $context, "common/layout/gallery.twig");
            echo "
        </body>
    ";
        }
        // line 85
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
        return array (  252 => 85,  246 => 82,  240 => 79,  236 => 77,  230 => 73,  224 => 70,  220 => 69,  217 => 68,  215 => 67,  210 => 64,  207 => 63,  201 => 60,  198 => 59,  192 => 57,  190 => 56,  185 => 55,  183 => 54,  178 => 53,  176 => 52,  171 => 51,  169 => 50,  164 => 49,  162 => 48,  157 => 47,  155 => 46,  150 => 45,  147 => 44,  143 => 42,  139 => 40,  136 => 39,  130 => 37,  124 => 35,  122 => 34,  119 => 33,  117 => 32,  111 => 29,  105 => 26,  101 => 24,  95 => 22,  93 => 21,  90 => 20,  87 => 19,  83 => 17,  80 => 16,  78 => 15,  74 => 14,  63 => 12,  60 => 11,  54 => 9,  52 => 8,  48 => 7,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "index.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\index.twig");
    }
}
