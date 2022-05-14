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

/* landing.twig */
class __TwigTemplate_94c570cab6a0688012319e64d280f5c8 extends Template
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
        echo "<!-- Landing placeholder -->
<article>
    <p>";
        // line 3
        echo twig_escape_filter($this->env, ($context["client"] ?? null), "html", null, true);
        echo "</p>
    <p>Due to release of <i>Endwalker</i> for <i>Final Fantasy XIV</i> I wanted to publish the updated version of the website on December 7th. But I did not realize that the release is on December 3rd...</p>
    <p>So... I am still releasing the update, as you can see, but some pages are not ready yet.</p>
    <p>On the bright side, besides the redesign, BIC Tracker is now properly functional again!</p>
    <p>I will restore other FFTracker pages over the weekend, and the rest (including proper landing page) will come later on.</p>
    <p>At least, that's the plan.</p>
    <br>
    <p>UPDATE as of 05.12.2021: most of FFTracker pages are now working. Exceptions are entity registration and statistics (including those on Free Companies' pages). The latter are planned to be transferred to images, instead of Google Charts, so may take a bit more time to be restored.</p>
    <br>
    <p>UPDATE as of 08.12.2021: FF entity registration page is now up.</p>
    <p>During first days of running I noticed that some images are not being loaded on the pages. Those are images which are \"proxied\" from Lodestone since they do not comply with Crossorigin (which I pointed to them like half a year ago). In order to maintain high level of security of the website I will start saving images for characters and achievements locally on every update. Unfortunately, considering the amount of files, that will result in, I will hit limits of current hosting. As such I made decision to finally migrate to a VPS instead of shared hosting, which will remove limit of files storage and help me provide even higher level of security and performance. At least theoretically. As such, it is possible to see some disruption of the service during migration in near future (hopefully by the end of the week). I apologize for any possible inconveniences caused by this.</p>
    <br>
    <p>UPDATE as of 10.12.2021: Moved to VPS. If you are reading this, means that DNS was updated as well, so everything should be working as expected.</p>
    <p>I also noticed that TTFB timings from VPS seem to be extremely long (23 seconds!), so am looking into what may be the cause of that.</p>
    <br>
    <p>UPDATE as of 12.12.2021: Significant improvement in TTFB already (so far 5-7 seconds instead of 20-60), but still have some ideas on potential further improvements.</p>
</article>
";
    }

    public function getTemplateName()
    {
        return "landing.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "landing.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\landing.twig");
    }
}
