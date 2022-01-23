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

/* about/website.twig */
class __TwigTemplate_b6ff3c55c74ce59cfccf8d976766c386 extends Template
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
        echo "<p>This is <a href=\"/about/me/\">my</a> personal website, which is currently in a formless state.</p>
<p>If you've read the section about me, you know, that I want to make games. I think I can make a good game designer or, more specifically, narrative designer, but I do lack experience and some skills for this role.</p>
<p>The goal is to turn this website into something, that will serve me as a blog and a portfolio accompanying my <a href=\"/about/resume/\">resume</a>. I am planning on adding more features to it, that will allow me to streamline some of the activities, that I do and after some personal testing these features will also become available to other users.</p>
<p>One of the planned features is a blog, that will allow to post to several social networks as well as to the blog itself. After that blog part is done, I hope to utilize it to stimulate myself to write prose more often in order to level up that skill. And since the website is dynamic, it may be possible to even implement some stories as games.</p>
<p>That's not all, though, but at the moment I'd prefer not to disclose the ideas I have: let them be a surprise 😉. I can only say for sure, that some of the features may require payment, but on the other hand, I will be trying to avoid use of any kind of ads on the website for as long as I can.</p>
<p>If you want to learn what technology I am using for the website, please, check this <a href=\"/about/tech/\">section</a></p>
";
    }

    public function getTemplateName()
    {
        return "about/website.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "about/website.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\about\\website.twig");
    }
}
