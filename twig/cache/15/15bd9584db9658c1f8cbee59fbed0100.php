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

/* about/me.twig */
class __TwigTemplate_f6cf9c76b34d6c9bb635d00e411a2b90 extends Template
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
        echo "<div class=\"clear\">
    <img loading=\"lazy\" decoding=\"async\" class=\"avatar float_left\" id=\"me\" src=\"/static/resume/photo_square.jpg\" alt=\"Me\">
    <p>Yes, this mug is mine.</p>
    <p>Who I am, you ask?</p>
    <p>My name is Dmitry Kustov, sometimes known as Simbiat, and I am sole owner and developer of this website you are on.</p>
    <p>Born in 1989 I hail from a small town \"Moscow\" somewhere in lesser known country \"Russian Federation\".</p>
    <p>I code in PHP and JavaScript, watch anime and TV series and play computer games. I want to make games. Or rather I want to tell stories, but the way I \"see\" those stories requires a visual component and sometimes interactivity, if the story is to deliver more, than just words. I am interested in telling stories revolving around \"unique\" patterns of thinking (psychological component), morality and, generally, one's feelings.</p>
    <p>And... No idea what else you would want to know about me 😅</p>
    <p>Fun fact: my \"Simbiat\" alias turned out to be a female name commonly used in Africa and arabian countries. I derived it from \"symbiont\" (specifically those things from \"Spider-Man\" comics), so it felt weird, that it turned out to be a female name as well. Luckily enough it stands for <i>strong woman</i>, so I decided to keep it regardless.</p>
    <p>If you want to learn more about the website itself, check this <a href=\"/about/website/\">section</a>. If you want to check my resume, check this <a href=\"/about/resume/\">one</a>. If you suddenly want to know something else about me, let me know using my <a href=\"/about/contacts/\">contacts</a>.</p>
</div>
";
    }

    public function getTemplateName()
    {
        return "about/me.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "about/me.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\about\\me.twig");
    }
}
