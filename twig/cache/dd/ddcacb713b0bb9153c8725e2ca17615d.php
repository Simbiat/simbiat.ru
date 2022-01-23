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

/* errors/teapot.twig */
class __TwigTemplate_b995f8086779c052f3d9cdad1c11ce92 extends Template
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
        echo "<body style=\"background-color: hsl(256, 20%, 10%); color: hsl(0, 20%, 95%)\">
    <header style=\"background-color: hsl(256, 20%, 15%); border-color: hsl(192, 50%, 20%)\">
        ";
        // line 3
        echo twig_include($this->env, $context, "common/layout/header.twig");
        echo "
    </header>
    <main id=\"content\" role=\"main\">
        <article style=\"background-color: hsl(256, 20%, 20%); color: hsl(43, 77%, 57%); border-color: hsl(192, 50%, 20%); text-align: center\">
            This website uses features aimed at improving security of content delivery and information exchange.<br>
            Unfortunately, these features are not supported by <b>";
        // line 8
        echo twig_escape_filter($this->env, ($context["client"] ?? null), "html", null, true);
        echo "</b> <i>teapot</i>.<br>
            Since we value security, we are unable to serve any content to you.<br>
            We are sorry for inconvenience, but, please, try with a different browser.
        </article>
        <article id=\"http_error\" style=\"background-color: hsl(256, 20%, 20%); border-color: hsl(192, 50%, 20%); text-align: center\">
            ";
        // line 13
        echo twig_include($this->env, $context, "errors/418.twig");
        echo "
        </article>
    </main>
</body>
";
    }

    public function getTemplateName()
    {
        return "errors/teapot.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 13,  49 => 8,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "errors/teapot.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\errors\\teapot.twig");
    }
}
