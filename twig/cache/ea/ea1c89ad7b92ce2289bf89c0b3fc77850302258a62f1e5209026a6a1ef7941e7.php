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

/* common/layout/gallery.twig */
class __TwigTemplate_3307a76d50e9f4920abf344c683f2a45ee9fe96aeb8508c1ef09886bd6b15d74 extends Template
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
        echo "<div id=\"galleryOverlay\" role=\"dialog\" aria-label=\"Gallery overlay\" class=\"hidden\">
    <div id=\"galleryClose\"><input id=\"closeGalleryIcon\" class=\"navIcon\" alt=\"Close gallery\" data-tooltip=\"Close gallery\" type=\"image\" src=\"/img/close.svg\"></div>
    <div id=\"galleryGrid\">
        <div id=\"galleryPrevious\">&#10096;</div>
        <div id=\"galleryNameBlock\"><span id=\"galleryName\">Name</span><span id=\"galleryNameLink\"></span></div>
        <div id=\"galleryImage\"></div>
        <div id=\"galleryCounter\"><span id=\"galleryCurrent\">0</span><span id=\"gallerySlash\">/</span><span id=\"galleryTotal\">0</span></div>
        <div id=\"galleryNext\">&#10097;</div>
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "common/layout/gallery.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/gallery.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\gallery.twig");
    }
}
