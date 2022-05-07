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

/* mail/activation.twig */
class __TwigTemplate_29eaeaf1dfb0543bbb3c0aff943e8e5d extends Template
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
        echo "<p>You have recently registered on <a href=\"";
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "\"></a> and we need to activate your account by confirming your e-mail address.</p>
<p>To do that, just click 👉 <a href=\"";
        // line 2
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/uc/activate/";
        echo twig_escape_filter($this->env, ($context["userid"] ?? null), "html", null, true);
        echo "/";
        echo twig_escape_filter($this->env, ($context["activation"] ?? null), "html", null, true);
        echo "\">here</a> 👈</p>
";
    }

    public function getTemplateName()
    {
        return "mail/activation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "mail/activation.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\mail\\activation.twig");
    }
}
