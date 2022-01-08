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

/* common/layout/signinup.twig */
class __TwigTemplate_8066621111bc7d761c4e4a01c6015209 extends Template
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
        echo "<form role=\"form\" id=\"signinup\" name=\"signinup\" autocomplete=\"on\">
    ";
        // line 2
        if ((($context["registration"] ?? null) == 0)) {
            // line 3
            echo "        <div class=\"warning\">Registration is currently closed</div>
    ";
        }
        // line 5
        echo "    <div id=\"radio_signinup\" role=\"radiogroup\">
        <span>I am</span><br>
        <span class=\"radio_and_label\">
            <input type=\"radio\" id=\"radio_existuser\" name=\"signinup[type]\" value=\"member\" checked>
            <label for=\"radio_existuser\">member</label>
        </span>
        ";
        // line 11
        if ((($context["registration"] ?? null) == 1)) {
            // line 12
            echo "            <span class=\"radio_and_label\">
                <input type=\"radio\" id=\"radio_newuser\" name=\"signinup[type]\" value=\"newuser\">
                <label for=\"radio_newuser\">new</label>
            </span>
        ";
        }
        // line 17
        echo "        <span class=\"radio_and_label\">
            <input type=\"radio\" id=\"radio_forget\" name=\"signinup[type]\" value=\"forget\">
            <label for=\"radio_forget\">forgetful</label>
        </span>
    </div>
    <div class=\"float_label_div\">
        <input form=\"signinup\" type=\"email\" required aria-required=\"true\" name=\"signinup[email]\" id=\"signinup_email\" placeholder=\"Email or name\" autocomplete=\"username\" inputmode=\"email\" minlength=\"1\" maxlength=\"320\" pattern=\"^[a-zA-Z0-9.!#\$%&\\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\$\">
        <label for=\"signinup_email\">Email or name</label>
    </div>
    <div class=\"float_label_div\">
        <input form=\"signinup\" type=\"password\" required aria-required=\"true\" name=\"signinup[password]\" id=\"signinup_password\" placeholder=\"Password\" autocomplete=\"current-password\" inputmode=\"text\" minlength=\"8\" pattern=\".{8,}\">
        <label for=\"signinup_password\">Password</label>
        <div class=\"showpassword\"></div>
        <div id=\"password_req\">Only password requirement: at least 8 symbols</div>
        <div class=\"pass_str_div\">Password strength:
            <span class=\"password_strength\">weak</span>
        </div>
    </div>
    <div class=\"radio_and_label\" id=\"rememberme_div\">
        <input role=\"checkbox\" aria-checked=\"false\" form=\"signinup\" type=\"checkbox\" name=\"signinup[rememberme]\" id=\"rememberme\">
        <label for=\"rememberme\">Remember me</label>
    </div>
    <input role=\"button\" form=\"signinup\" type=\"submit\" name=\"signinup[submit]\" id=\"signinup_submit\" formaction=\"";
        // line 39
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "\" formmethod=\"post\" formtarget=\"_self\" value=\"Sign in/Join\">
</form>
";
    }

    public function getTemplateName()
    {
        return "common/layout/signinup.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 39,  63 => 17,  56 => 12,  54 => 11,  46 => 5,  42 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/signinup.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\signinup.twig");
    }
}
