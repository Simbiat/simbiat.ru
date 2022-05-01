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

/* common/layout/scripts.twig */
class __TwigTemplate_e3da4c2741c08acb0cde545a3edad8a8 extends Template
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
        echo "<!-- JavaScript -->
<script src=\"";
        // line 2
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/js/";
        echo twig_escape_filter($this->env, ($context["js_version"] ?? null), "html", null, true);
        echo ".js\"></script>

<!-- Google Charts -->
";
        // line 5
        if (($context["charts"] ?? null)) {
            // line 6
            echo "    <script src=\"https://www.gstatic.com/charts/loader.js\"></script>
    <script type=\"text/javascript\">google.charts.load('current', {packages: ['corechart', 'bar']});</script>
";
        }
        // line 9
        echo "
<!-- Google rich cards -->
<script type=\"application/ld+json\">
    {
        \"@context\": \"https://schema.org\",
        \"@type\": \"Organization\",
        \"url\": \"";
        // line 15
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "\",
        \"logo\": \"";
        // line 16
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/simbiat.png\",
        \"sameAs\": [
            \"https://facebook.com/SimbiatSoftware/\",
            \"https://facebook.com/simbiat19\",
            \"https://twitter.com/simbiat199\",
            \"https://www.linkedin.com/in/simbiat19/\",
            \"https://www.youtube.com/channel/UCyzixPty8XEiUWC4c1jns_Q\"
        ]
    }
</script>
";
    }

    public function getTemplateName()
    {
        return "common/layout/scripts.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  67 => 16,  63 => 15,  55 => 9,  50 => 6,  48 => 5,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/scripts.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\scripts.twig");
    }
}
