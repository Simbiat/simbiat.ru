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

/* common/elements/carousel.twig */
class __TwigTemplate_bb9d71490a9edb79dc707d622accd5e6 extends Template
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
        echo "<div class=\"imageCarousel\">
    <div class=\"imageCarouselPrev\">❰</div>
    <ul>
        ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["images"] ?? null));
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
        foreach ($context['_seq'] as $context["_key"] => $context["image"]) {
            // line 5
            echo "            ";
            if (twig_get_attribute($this->env, $this->source, $context["image"], "href", [], "any", false, false, false, 5)) {
                // line 6
                echo "                <li>
                    <figure>
                        <a class=\"galleryZoom\" href=\"";
                // line 8
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["image"], "href", [], "any", false, false, false, 8), "html", null, true);
                echo "\" target=\"_blank\">
                            <img loading=\"lazy\" decoding=\"async\" src=\"";
                // line 9
                if (twig_get_attribute($this->env, $this->source, $context["image"], "thumb", [], "any", false, false, false, 9)) {
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["image"], "thumb", [], "any", false, false, false, 9), "html", null, true);
                } else {
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["image"], "href", [], "any", false, false, false, 9), "html", null, true);
                }
                echo "\" alt=\"";
                if (twig_get_attribute($this->env, $this->source, $context["image"], "alt", [], "any", false, false, false, 9)) {
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["image"], "alt", [], "any", false, false, false, 9), "html", null, true);
                } else {
                    echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('basename')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["image"], "href", [], "any", false, false, false, 9)]), "html", null, true);
                }
                echo "\">
                            <div>";
                // line 10
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, false, 10), "html", null, true);
                echo "/";
                echo twig_escape_filter($this->env, twig_length_filter($this->env, ($context["images"] ?? null)), "html", null, true);
                echo "</div>
                        </a>
                        <figcaption>";
                // line 12
                if (twig_get_attribute($this->env, $this->source, $context["image"], "caption", [], "any", false, false, false, 12)) {
                    echo twig_get_attribute($this->env, $this->source, $context["image"], "caption", [], "any", false, false, false, 12);
                } else {
                    if (twig_get_attribute($this->env, $this->source, $context["image"], "alt", [], "any", false, false, false, 12)) {
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["image"], "alt", [], "any", false, false, false, 12), "html", null, true);
                    } else {
                        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('basename')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["image"], "href", [], "any", false, false, false, 12)]), "html", null, true);
                    }
                }
                echo "</figcaption>
                    </figure>
                </li>
            ";
            }
            // line 16
            echo "        ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['image'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "    </ul>
    <div class=\"imageCarouselNext\">❱</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "common/elements/carousel.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  120 => 17,  106 => 16,  91 => 12,  84 => 10,  70 => 9,  66 => 8,  62 => 6,  59 => 5,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/elements/carousel.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\elements\\carousel.twig");
    }
}
