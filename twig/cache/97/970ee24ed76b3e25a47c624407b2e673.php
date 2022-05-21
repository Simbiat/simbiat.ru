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

/* about/about.twig */
class __TwigTemplate_ca7f96aa53fdde96ef4d2cb7b62665b4 extends Template
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
    <article>
        <details ";
        // line 3
        if ( !($context["subServiceName"] ?? null)) {
            echo "class=\"persistent\" open";
        }
        echo ">
            <summary class=\"rightSummary aboutSection\">About</summary>
            <p>This is the section, where you can learn about ";
        // line 5
        echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo ".</p>
            ";
        // line 6
        if ( !($context["subServiceName"] ?? null)) {
            // line 7
            echo "                <p>Select one of the links below to learn more about:</p>
                <ul>
                    <li><a href=\"/about/me/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Me</span></a></li>
                    <li><a href=\"/about/website/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Website</span></a></li>
                    <li><a href=\"/about/tech/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Technology</span></a></li>
                    <li><a href=\"/about/resume/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Resume</span></a></li>
                    <li><a href=\"/about/contacts/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Contacts</span></a></li>
                    <li><a href=\"/about/tos/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Terms of Service</span></a></li>
                    <li><a href=\"/about/privacy/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Privacy Policy</span></a></li>
                    <li><a href=\"/about/security/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Security Policy</span></a></li>
                    <li><a href=\"/about/changelog/\" target=\"_self\" itemprop=\"url\"><span itemprop=\"name\">Changelog</span></a></li>
                </ul>
            ";
        }
        // line 20
        echo "        </details>
    </article>
    ";
        // line 22
        if (($context["subServiceName"] ?? null)) {
            // line 23
            echo "        <article>
            ";
            // line 24
            if ((($context["subServiceName"] ?? null) == "me")) {
                // line 25
                echo "                ";
                echo twig_include($this->env, $context, "about/me.twig");
                echo "
            ";
            } elseif ((            // line 26
($context["subServiceName"] ?? null) == "website")) {
                // line 27
                echo "                ";
                echo twig_include($this->env, $context, "about/website.twig");
                echo "
            ";
            } elseif ((            // line 28
($context["subServiceName"] ?? null) == "tech")) {
                // line 29
                echo "                ";
                echo twig_include($this->env, $context, "about/tech.twig");
                echo "
            ";
            } elseif ((            // line 30
($context["subServiceName"] ?? null) == "resume")) {
                // line 31
                echo "                ";
                echo twig_include($this->env, $context, "about/resume.twig");
                echo "
            ";
            } elseif ((            // line 32
($context["subServiceName"] ?? null) == "contacts")) {
                // line 33
                echo "                ";
                echo twig_include($this->env, $context, "about/contacts.twig");
                echo "
            ";
            } elseif ((            // line 34
($context["subServiceName"] ?? null) == "tos")) {
                // line 35
                echo "                ";
                echo twig_include($this->env, $context, "about/tos.twig");
                echo "
            ";
            } elseif ((            // line 36
($context["subServiceName"] ?? null) == "privacy")) {
                // line 37
                echo "                ";
                echo twig_include($this->env, $context, "about/privacy.twig");
                echo "
            ";
            } elseif ((            // line 38
($context["subServiceName"] ?? null) == "security")) {
                // line 39
                echo "                ";
                echo twig_include($this->env, $context, "about/security.twig");
                echo "
            ";
            } elseif ((            // line 40
($context["subServiceName"] ?? null) == "changelog")) {
                // line 41
                echo "                ";
                echo twig_include($this->env, $context, "about/changelog.twig");
                echo "
            ";
            }
            // line 43
            echo "        </article>
    ";
        }
        // line 45
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "about/about.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  146 => 45,  142 => 43,  136 => 41,  134 => 40,  129 => 39,  127 => 38,  122 => 37,  120 => 36,  115 => 35,  113 => 34,  108 => 33,  106 => 32,  101 => 31,  99 => 30,  94 => 29,  92 => 28,  87 => 27,  85 => 26,  80 => 25,  78 => 24,  75 => 23,  73 => 22,  69 => 20,  54 => 7,  52 => 6,  48 => 5,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "about/about.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\about\\about.twig");
    }
}
