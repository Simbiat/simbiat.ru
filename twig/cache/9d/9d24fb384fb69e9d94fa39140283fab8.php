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
class __TwigTemplate_009eb9563b966db22b8569747157e21c extends Template
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
        if (twig_get_attribute($this->env, $this->source, ($context["session_data"] ?? null), "username", [], "any", false, false, false, 1)) {
            // line 2
            echo "    <div>Hi, ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["session_data"] ?? null), "username", [], "any", false, false, false, 2), "html", null, true);
            echo "</div>
    <form role=\"form\" id=\"signinup\" name=\"signinup\" autocomplete=\"on\">
        <input role=\"button\" form=\"signinup\" type=\"submit\" name=\"signinup[submit]\" id=\"signinup_submit\" formaction=\"";
            // line 4
            echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\" formmethod=\"post\" formtarget=\"_self\" value=\"Logout\">
        <img id=\"singinup_spinner\" class=\"hidden spinner\" src=\"/img/spinner.svg\" alt=\"Logging out...\">
    </form>
";
        } else {
            // line 8
            echo "    <form role=\"form\" id=\"signinup\" name=\"signinup\" autocomplete=\"on\">
        ";
            // line 9
            if ((($context["registration"] ?? null) == 0)) {
                // line 10
                echo "            <div class=\"warning\">Registration is currently closed</div>
        ";
            }
            // line 12
            echo "        <div id=\"radio_signinup\" role=\"radiogroup\">
            <span>I am</span><br>
            <span class=\"radio_and_label\">
                <input type=\"radio\" id=\"radio_existuser\" name=\"signinup[type]\" value=\"login\" checked>
                <label for=\"radio_existuser\">member</label>
            </span>
            ";
            // line 18
            if ((($context["registration"] ?? null) == 1)) {
                // line 19
                echo "                <span class=\"radio_and_label\">
                    <input type=\"radio\" id=\"radio_newuser\" name=\"signinup[type]\" value=\"register\">
                    <label for=\"radio_newuser\">new</label>
                </span>
            ";
            }
            // line 24
            echo "            <span class=\"radio_and_label\">
                <input type=\"radio\" id=\"radio_forget\" name=\"signinup[type]\" value=\"remind\">
                <label for=\"radio_forget\">forgetful</label>
            </span>
        </div>
        <div class=\"float_label_div\">
            <input form=\"signinup\" type=\"text\" aria-required=\"false\" name=\"signinup[username]\" id=\"signinup_username\" placeholder=\"Username\" autocomplete=\"username\" inputmode=\"text\" minlength=\"1\" maxlength=\"64\" pattern=\"^[a-zA-Z0-9.!#\$%&\\'*+\\/=?^_`{|}~-]+\$\">
            <label for=\"signinup_username\">Username</label>
        </div>
        <div class=\"float_label_div\">
            <input form=\"signinup\" type=\"email\" required aria-required=\"true\" name=\"signinup[email]\" id=\"signinup_email\" placeholder=\"Email\" autocomplete=\"email\" inputmode=\"email\" minlength=\"1\" maxlength=\"320\" pattern=\"^[a-zA-Z0-9.!#\$%&\\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\$\">
            <label for=\"signinup_email\">Email</label>
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
            // line 50
            echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\" formmethod=\"post\" formtarget=\"_self\" value=\"Sign in/Join\">
        <img id=\"singinup_spinner\" class=\"hidden spinner\" src=\"/img/spinner.svg\" alt=\"Submitting form...\">
    </form>
";
        }
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
        return array (  106 => 50,  78 => 24,  71 => 19,  69 => 18,  61 => 12,  57 => 10,  55 => 9,  52 => 8,  45 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/signinup.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\signinup.twig");
    }
}
