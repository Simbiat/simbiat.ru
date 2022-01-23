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

/* common/layout/footer.twig */
class __TwigTemplate_727e4d8145364614d86e0aa881dcfc62 extends Template
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
        echo "<div id=\"footerLinks\">
    <a href=\"/about/tos/\">Terms of Service</a>
    <a href=\"/about/privacy/\">Privacy Policy</a>
    <details id=\"footerSitemap\" class=\"popup\">
        <summary class=\"rightSummary\">Sitemap</summary>
        <span id=\"sitemapLinks\">
            <a href=\"/sitemap/xml/\" target=\"_blank\"><abbr data-tooltip=\"Extensible Markup Language\">XML</abbr></a>
            <a href=\"/sitemap/html/\" target=\"_blank\"><abbr data-tooltip=\"Hypertext Markup Language\">HTML</abbr></a>
            <a href=\"/sitemap/txt/\" target=\"_blank\">Text</a>
        </span>
    </details>
</div>
<div class=\"back-to-top hidden\" role=\"button\" id=\"leftBTT\">🢁</div>
<address id=\"footerContacts\">
    <a href=\"https://facebook.com/SimbiatSoftware/\" target=\"_blank\" data-tooltip=\"Facebook\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/facebook.svg\" alt=\"Facebook\"></a>
    <a href=\"https://vk.com/simbiat19\" target=\"_blank\" data-tooltip=\"VK\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/VK.svg\" alt=\"VK\"></a>
    <a href=\"https://www.instagram.com/simbiat19/\" target=\"_blank\" data-tooltip=\"Instagram\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/instagram.svg\" alt=\"Instagram\"></a>
    <a href=\"https://www.youtube.com/channel/UCyzixPty8XEiUWC4c1jns_Q\" target=\"_blank\" data-tooltip=\"Youtube\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/youtube.svg\" alt=\"Youtube\"></a>
    <a href=\"https://www.linkedin.com/in/simbiat19/\" target=\"_blank\" data-tooltip=\"LinkedIn\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/linkedin.svg\" alt=\"LinkedIn\"></a>
    <a href=\"https://github.com/Simbiat\" target=\"_blank\" data-tooltip=\"GitHub\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" src=\"/img/social/github.svg\" alt=\"GitHub\"></a>
</address>
<div class=\"back-to-top hidden\" role=\"button\" id=\"rightBTT\">🢁</div>
<div id=\"footerCopyright\">
    &copy; <time datetime=\"2006\">2006</time>-<time datetime=\"";
        // line 24
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "Y"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "Y"), "html", null, true);
        echo "</time>";
        echo twig_escape_filter($this->env, ($context["currentyear"] ?? null), "html", null, true);
        echo " Dmitry Kustov All Rights Reserved
</div>
<div id=\"snacksContainer\" role=\"dialog\"></div>
";
    }

    public function getTemplateName()
    {
        return "common/layout/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 24,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/footer.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\footer.twig");
    }
}
