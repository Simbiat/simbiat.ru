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

/* common/elements/entitycard.twig */
class __TwigTemplate_09631d2d784fcbc0177870e51b3b0a6c extends Template
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
        // line 2
        if ((($context["type"] ?? null) == "bic")) {
            // line 3
            echo "    ";
            $context["url"] = "bictracker/bic";
        } elseif ((        // line 4
($context["type"] ?? null) == "achievement")) {
            // line 5
            echo "    ";
            $context["url"] = "fftracker/achievement";
        } elseif ((        // line 6
($context["type"] ?? null) == "character")) {
            // line 7
            echo "    ";
            $context["url"] = "fftracker/character";
        } elseif ((        // line 8
($context["type"] ?? null) == "freecompany")) {
            // line 9
            echo "    ";
            $context["url"] = "fftracker/freecompany";
        } elseif ((        // line 10
($context["type"] ?? null) == "pvpteam")) {
            // line 11
            echo "    ";
            $context["url"] = "fftracker/pvpteam";
        } elseif ((        // line 12
($context["type"] ?? null) == "linkshell")) {
            // line 13
            echo "    ";
            if (($context["crossworld"] ?? null)) {
                // line 14
                echo "        ";
                $context["url"] = "fftracker/crossworld_linkshell";
                // line 15
                echo "    ";
            } else {
                // line 16
                echo "        ";
                $context["url"] = "fftracker/linkshell";
                // line 17
                echo "    ";
            }
        }
        // line 20
        echo "<a class=\"entityCard\" href=\"/";
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "/";
        echo twig_escape_filter($this->env, twig_urlencode_filter(($context["id"] ?? null)), "html", null, true);
        echo "/";
        echo twig_escape_filter($this->env, twig_urlencode_filter(($context["name"] ?? null)), "html", null, true);
        echo "/\" data-tooltip=\"";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\">
    <span class=\"entityIcon\">
        ";
        // line 22
        if ((($context["type"] ?? null) == "bic")) {
            // line 23
            echo "            ";
            if (($context["DateOut"] ?? null)) {
                // line 24
                echo "                <span class=\"emojiRed\">&#10060;</span>
            ";
            } else {
                // line 26
                echo "                <span class=\"emojiGreen\">&#10004;</span>
            ";
            }
            // line 28
            echo "        ";
        } elseif ((($context["type"] ?? null) == "achievement")) {
            // line 29
            echo "            <img loading=\"lazy\" decoding=\"async\" alt=\"";
            echo ($context["name"] ?? null);
            echo "\" src=\"/img/fftracker/icons/";
            echo twig_escape_filter($this->env, ($context["icon"] ?? null), "html", null, true);
            echo "\">
        ";
        } elseif ((        // line 30
($context["type"] ?? null) == "character")) {
            // line 31
            echo "            <img loading=\"lazy\" decoding=\"async\" alt=\"";
            echo ($context["name"] ?? null);
            echo "\" src=\"/img/fftracker/avatars/96x96/";
            echo twig_escape_filter($this->env, ($context["icon"] ?? null), "html", null, true);
            echo ".jpg\">
        ";
        } elseif ((        // line 32
($context["type"] ?? null) == "freecompany")) {
            // line 33
            echo "            ";
            if ((($context["icon"] ?? null) == "1")) {
                // line 34
                echo "                <img loading=\"lazy\" decoding=\"async\" alt=\"";
                echo ($context["name"] ?? null);
                echo "\" src=\"/2c/0d/2c0d4fda793b510e521e5f85a7ee622d2489c4e05d4b4cd1543e10dbcb1e460d.png\">
            ";
            } elseif ((            // line 35
($context["icon"] ?? null) == "2")) {
                // line 36
                echo "                <img loading=\"lazy\" decoding=\"async\" alt=\"";
                echo ($context["name"] ?? null);
                echo "\" src=\"/ba/e0/bae0ddccf1454af830b12a8b508197a6557863742df96ef0916c52725d714ab2.png\">
            ";
            } elseif ((            // line 37
($context["icon"] ?? null) == "3")) {
                // line 38
                echo "                <img loading=\"lazy\" decoding=\"async\" alt=\"";
                echo ($context["name"] ?? null);
                echo "\" src=\"/b0/5d/b05d4cbd6375a90bf0f30c973d2e2c3beb8ebf72cece9b8dc5543ccb85d526d9.png\">
            ";
            } else {
                // line 40
                echo "                <img loading=\"lazy\" decoding=\"async\" alt=\"";
                echo ($context["name"] ?? null);
                echo "\" src=\"/img/fftracker/merged-crests/";
                echo twig_escape_filter($this->env, twig_slice($this->env, ($context["icon"] ?? null), 0, 2), "html", null, true);
                echo "/";
                echo twig_escape_filter($this->env, twig_slice($this->env, ($context["icon"] ?? null), 2, 2), "html", null, true);
                echo "/";
                echo twig_escape_filter($this->env, ($context["icon"] ?? null), "html", null, true);
                echo ".png\">
            ";
            }
            // line 42
            echo "        ";
        } elseif ((($context["type"] ?? null) == "pvpteam")) {
            // line 43
            echo "            <img loading=\"lazy\" decoding=\"async\" alt=\"";
            echo ($context["name"] ?? null);
            echo "\" src=\"/img/fftracker/merged-crests/";
            echo twig_escape_filter($this->env, twig_slice($this->env, ($context["icon"] ?? null), 0, 2), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, twig_slice($this->env, ($context["icon"] ?? null), 2, 2), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, ($context["icon"] ?? null), "html", null, true);
            echo ".png\">
        ";
        } elseif ((        // line 44
($context["type"] ?? null) == "linkshell")) {
            // line 45
            echo "            <img loading=\"lazy\" decoding=\"async\" alt=\"";
            echo ($context["name"] ?? null);
            echo "\" src=\"/img/fftracker/";
            if (($context["crossworld"] ?? null)) {
                echo "crossworld_";
            }
            echo "linkshell.png\">
        ";
        }
        // line 47
        echo "    </span>
    <span class=\"entityName\">
        ";
        // line 49
        if ((($context["lsrankid"] ?? null) && (($context["lsrankid"] ?? null) != 3))) {
            // line 50
            echo "            <img class=\"entityRank\" loading=\"lazy\" decoding=\"async\" alt=\"";
            echo twig_escape_filter($this->env, ($context["rank"] ?? null), "html", null, true);
            echo "\" data-tooltip=\"";
            echo twig_escape_filter($this->env, ($context["rank"] ?? null), "html", null, true);
            echo "\" src=\"/img/fftracker/lsranks/";
            echo twig_escape_filter($this->env, ($context["lsrankid"] ?? null), "html", null, true);
            echo ".png\">
        ";
        }
        // line 52
        echo "        ";
        if ((($context["pvprankid"] ?? null) && (($context["pvprankid"] ?? null) != 3))) {
            // line 53
            echo "            <img class=\"entityRank\" loading=\"lazy\" decoding=\"async\" alt=\"";
            echo twig_escape_filter($this->env, ($context["rank"] ?? null), "html", null, true);
            echo "\" data-tooltip=\"";
            echo twig_escape_filter($this->env, ($context["rank"] ?? null), "html", null, true);
            echo "\" src=\"/img/fftracker/pvpranks/";
            echo twig_escape_filter($this->env, ($context["pvprankid"] ?? null), "html", null, true);
            echo ".png\">
        ";
        }
        // line 55
        echo "        ";
        if (preg_match("/^\\d+\$/", ($context["rankid"] ?? null))) {
            // line 56
            echo "            <img class=\"entityRank\" loading=\"lazy\" decoding=\"async\" alt=\"";
            echo twig_escape_filter($this->env, ($context["rank"] ?? null), "html", null, true);
            echo "\" data-tooltip=\"";
            echo twig_escape_filter($this->env, ($context["rank"] ?? null), "html", null, true);
            echo "\" src=\"/img/fftracker/fcranks/";
            echo twig_escape_filter($this->env, ($context["rankid"] ?? null), "html", null, true);
            echo ".png\">
        ";
        }
        // line 58
        echo "        ";
        echo ($context["name"] ?? null);
        echo "
    </span>
    <span class=\"entityArrow\">&#10151;</span>
</a>
";
    }

    public function getTemplateName()
    {
        return "common/elements/entitycard.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  229 => 58,  219 => 56,  216 => 55,  206 => 53,  203 => 52,  193 => 50,  191 => 49,  187 => 47,  177 => 45,  175 => 44,  164 => 43,  161 => 42,  149 => 40,  143 => 38,  141 => 37,  136 => 36,  134 => 35,  129 => 34,  126 => 33,  124 => 32,  117 => 31,  115 => 30,  108 => 29,  105 => 28,  101 => 26,  97 => 24,  94 => 23,  92 => 22,  80 => 20,  76 => 17,  73 => 16,  70 => 15,  67 => 14,  64 => 13,  62 => 12,  59 => 11,  57 => 10,  54 => 9,  52 => 8,  49 => 7,  47 => 6,  44 => 5,  42 => 4,  39 => 3,  37 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/elements/entitycard.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\elements\\entitycard.twig");
    }
}
