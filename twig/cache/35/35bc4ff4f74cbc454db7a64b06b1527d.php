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

/* mail/index.twig */
class __TwigTemplate_48ef3bfb983b5c6370d2f03c04af3aa6 extends Template
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
        echo "<html lang=\"en\">
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>";
        // line 5
        echo twig_escape_filter($this->env, ($context["subject"] ?? null), "html", null, true);
        echo "</title>
    </head>
    <body style=\"margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #17141f;color: #f5f0f0\">
        <div style=\"width: 100%; background-color: #231f2e; text-align: center; border-bottom-color: #266373;border-bottom-width: 2px;border-bottom-style: solid;border-radius: 0 0 12px 12px;padding-bottom: 3px;\">
            <a href=\"";
        // line 9
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "\" style=\"font-family: Georgia,Cambria,&quot;Times New Roman&quot;,Times,serif;font-size: 32px; letter-spacing: 4px; font-weight: 700; color: #266373; line-height: 32px; height: 32px;text-decoration: none;\">
                    <span style=\"text-decoration: none;\">Simbiat</span>
                    <span style=\"display:inline-block\"><img alt=\"logo\" id=\"logoIcon\" style=\"height: 32px;width: 32px;\" width=\"32\" height=\"32\" src=\"";
        // line 11
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/favicon-32x32.png\"></span>
                    <span style=\"text-decoration: none;\">Software</span>
            </a>
        </div>
        <div style=\"height: calc(100% - 72px);\">
            <div style=\"background-color: #2e293d; width: 90%; margin:8px auto;padding: 8px 8px;border-width: 2px;border-radius: 12px;border-style: solid;border-color: #1a424d #266373 #266373 #1a424d;\">
                <h1 style=\"width: 100%; font-size: 20px; text-align: center\">";
        // line 17
        echo twig_escape_filter($this->env, ($context["subject"] ?? null), "html", null, true);
        echo "</h1>
                <div>Hi, ";
        // line 18
        echo twig_escape_filter($this->env, ($context["username"] ?? null), "html", null, true);
        echo "!</div>

            </div>
        </div>
        <div style=\"width: 100%; background-color: #231f2e; text-align: center; border-top-color: #1a424d;border-top-width: 2px;border-top-style: solid;border-radius: 12px 12px 0 0;padding-top: 3px;height: 20px;\">
            <div>
                <a href=\"";
        // line 24
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/about/contacts/\" style=\"color:#9ad4ea\">contact us</a> or <a href=\"";
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/uc/unsubscribe/?token=";
        echo twig_escape_filter($this->env, ($context["unsubscribe"] ?? null), "html", null, true);
        echo "\" style=\"color:#9ad4ea\">unsubscribe</a>
            </div>
        </div>
    </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "mail/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 24,  68 => 18,  64 => 17,  55 => 11,  50 => 9,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "mail/index.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\mail\\index.twig");
    }
}
