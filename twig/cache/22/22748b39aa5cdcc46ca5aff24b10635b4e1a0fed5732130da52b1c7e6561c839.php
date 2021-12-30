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

/* bictracker/keying.twig */
class __TwigTemplate_24fa319d6c0d7130d3ce748d884f0cf9ed83b2b787d238d83960d07f5aa0fc0d extends Template
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
        echo "<section class=\"bottomMargin\">
    <details>
        <summary class=\"rightSummary\">Технические детали</summary>
        <p>Функционал доступен в виде API (JSON), для доступа к которому используйте следующую ссылку (обновляются динамически): <code>";
        // line 4
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/api/bictracker/keying/<span id=\"bic_key_sample\" class=\"warning\">";
        if (($context["bic_value"] ?? null)) {
            echo twig_escape_filter($this->env, ($context["bic_value"] ?? null), "html", null, true);
        } else {
            echo "БИК";
        }
        echo "</span>/<span id=\"account_key_sample\" class=\"warning\">";
        if (($context["acc_value"] ?? null)) {
            echo twig_escape_filter($this->env, ($context["acc_value"] ?? null), "html", null, true);
        } else {
            echo "СЧЁТ";
        }
        echo "</span>/</code></p>
    </details>
</section>
<section>
    <p>Используйте форму ниже для проверки контрольного символа номера счёта или проверки <em>возможной</em> принадлежности счёта указанному БИК согласно алгоритму 1997ого года (до постановления <cite>№732-П</cite>):</p>
    <form role=\"form\" action=\"\">
        <span class=\"float_label_div\"><input id=\"bic_key\" type=\"text\" inputmode=\"decimal\" autocomplete=\"on\" required maxlength=\"9\" size=\"9\" pattern=\"^[0-9]{9}\$\"";
        // line 10
        if (($context["bic_value"] ?? null)) {
            echo " value=\"";
            echo twig_escape_filter($this->env, ($context["bic_value"] ?? null), "html", null, true);
            echo "\"";
        }
        echo "><label for=\"bic_key\">БИК</label></span>
        <span class=\"float_label_div\"><input id=\"account_key\" type=\"text\" inputmode=\"text\" autocomplete=\"on\" required maxlength=\"20\" size=\"20\" pattern=\"^[0-9]{5}[0-9АВСЕНКМРТХавсенкмртх]{1}[0-9]{14}\$\"";
        // line 11
        if (($context["acc_value"] ?? null)) {
            echo " value=\"";
            echo twig_escape_filter($this->env, ($context["acc_value"] ?? null), "html", null, true);
            echo "\"";
        }
        echo "><label for=\"account_key\">Номер счёта</label></span>
        <img id=\"bic_spinner\" class=\"hidden\" src=\"/img/spinner.svg\" alt=\"Проверяем ключевание...\">
    </form>
    <p class=\"noLetter\">
        ";
        // line 15
        if ((($context["checkResult"] ?? null) === null)) {
            // line 16
            echo "            <span id=\"accCheckResult\"></span>
        ";
        } elseif ((        // line 17
($context["checkResult"] ?? null) === false)) {
            // line 18
            echo "            <span id=\"accCheckResult\" class=\"failure\">Неверный формат БИКа или счёта</span>
        ";
        } elseif ((        // line 19
($context["checkResult"] ?? null) === true)) {
            // line 20
            echo "            <span id=\"accCheckResult\" class=\"success\">Правильное ключевание</span>
        ";
        } else {
            // line 22
            echo "            <span id=\"accCheckResult\" class=\"failure\">Неверное ключевание. Ожидаемый ключ: ";
            echo twig_escape_filter($this->env, ($context["checkResult"] ?? null), "html", null, true);
            echo " (";
            echo ($context["properKey"] ?? null);
            echo ")</span>
        ";
        }
        // line 24
        echo "    </p>
</section>
";
    }

    public function getTemplateName()
    {
        return "bictracker/keying.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  106 => 24,  98 => 22,  94 => 20,  92 => 19,  89 => 18,  87 => 17,  84 => 16,  82 => 15,  71 => 11,  63 => 10,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "bictracker/keying.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\bictracker\\keying.twig");
    }
}
