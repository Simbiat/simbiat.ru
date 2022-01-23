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

/* fftracker/track.twig */
class __TwigTemplate_be6a0134f2e5df580afeb8ad277cb407 extends Template
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
        echo "<section>
    <form role=\"form\" id=\"ff_track_register\" name=\"ff_track_register\" autocomplete=\"off\">
        <div class=\"float_label_div\" data-tooltip=\"Entity ID as seen on Lodestone (in URL)\" id=\"ff_track_id_wrap\">
            <input autofocus form=\"ff_track_register\" type=\"text\" required aria-required=\"true\" name=\"ff_track_id\" id=\"ff_track_id\" placeholder=\"ID of the entity\" autocomplete=\"off\" inputmode=\"text\" minlength=\"1\" maxlength=\"40\" pattern=\"^\\d+\$\">
            <label for=\"ff_track_id\">Entity ID</label>
        </div>
        <div class=\"float_label_div\" id=\"ff_track_type_wrap\">
            <select form=\"ff_track_register\" required aria-required=\"true\" name=\"ff_track_type\" id=\"ff_track_type\">
                <option value=\"character\">Character</option>
                <option value=\"freecompany\">Free Company</option>
                <option value=\"pvpteam\">PvP Team</option>
                <option value=\"linkshell\">Linkshell</option>
                <option value=\"crossworld_linkshell\">Crossworld Linkshell</option>
            </select>
            <label for=\"ff_track_type\" data-tooltip=\"Type of the entity you want to register\">Entity type</label>
        </div>
        <input type=\"submit\" value=\"Track\" form=\"ff_track_register\" id=\"ff_track_submit\">
        <img id=\"ff_track_spinner\" class=\"hidden\" src=\"/img/spinner.svg\" alt=\"Registering entity...\">
    </form>
</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/track.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/track.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\track.twig");
    }
}
