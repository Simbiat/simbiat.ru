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

/* fftracker/freecompany.twig */
class __TwigTemplate_63745ace9e6fb6ed06d16832ae57cb6d9f24ea4a4531886badee2bee6d0d2bf8 extends Template
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
    <img loading=\"lazy\" decoding=\"async\" class=\"float_left ff_crest\" alt=\"";
        // line 2
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "name", [], "any", false, false, false, 2), "html", null, true);
        echo "\"
        ";
        // line 3
        if (twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "crest", [], "any", false, false, false, 3)) {
            // line 4
            echo "            src=\"/img/fftracker/merged-crests/";
            echo twig_escape_filter($this->env, twig_slice($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "crest", [], "any", false, false, false, 4), 0, 2), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, twig_slice($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "crest", [], "any", false, false, false, 4), 2, 2), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "crest", [], "any", false, false, false, 4), "html", null, true);
            echo ".png\"
        ";
        } else {
            // line 6
            echo "            ";
            if ((twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 6) == "Order of the Twin Adder")) {
                // line 7
                echo "                src=\"/img/fftracker/merged-crests/ba/e0/bae0ddccf1454af830b12a8b508197a6557863742df96ef0916c52725d714ab2.png\"
            ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 8
($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 8) == "Immortal Flames")) {
                // line 9
                echo "                src=\"/img/fftracker/merged-crests/b0/5d/b05d4cbd6375a90bf0f30c973d2e2c3beb8ebf72cece9b8dc5543ccb85d526d9.png\"
            ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 10
($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 10) == "Maelstrom")) {
                // line 11
                echo "                src=\"/img/fftracker/merged-crests/2c/0d/2c0d4fda793b510e521e5f85a7ee622d2489c4e05d4b4cd1543e10dbcb1e460d.png\"
            ";
            }
            // line 13
            echo "        ";
        }
        // line 14
        echo "    >
    <p>Rank <i>";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "rank", [], "any", false, false, false, 15), "html", null, true);
        echo "</i> Free Company <b>\"";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "name", [], "any", false, false, false, 15), "html", null, true);
        echo "\"</b> was formed on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "formed", [], "any", false, false, false, 15), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "formed", [], "any", false, false, false, 15), "d/m/Y"), "html", null, true);
        echo "</time> and registered on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "registered", [], "any", false, false, false, 15), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "registered", [], "any", false, false, false, 15), "d/m/Y"), "html", null, true);
        echo "</time> with ID <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "id", [], "any", false, false, false, 15), "html", null, true);
        echo "</i>. Last set of interviews conducted on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "updated", [], "any", false, false, false, 15), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "updated", [], "any", false, false, false, 15), "d/m/Y"), "html", null, true);
        echo "</time>.";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "deleted", [], "any", false, false, false, 15)) {
            echo " Was disbanded on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "deleted", [], "any", false, false, false, 15), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 15), "deleted", [], "any", false, false, false, 15), "d/m/Y"), "html", null, true);
            echo "</time>.";
        }
        echo "</p>
    <p>
        Operate";
        // line 17
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 17), "deleted", [], "any", false, false, false, 17)) {
            echo "d";
        } else {
            echo "s";
        }
        echo " on <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 17), "server", [], "any", false, false, false, 17), "html", null, true);
        echo "</i>, <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 17), "dataCenter", [], "any", false, false, false, 17), "html", null, true);
        echo "</i>, leaving tags <b>\"";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "tag", [], "any", false, false, false, 17), "html", null, true);
        echo "\"</b> all over the place and increasing glory of ";
        if (twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "crest", [], "any", false, false, false, 17)) {
            echo "<img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 17), "html", null, true);
            echo "\"
        ";
            // line 18
            if ((twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 18) == "Order of the Twin Adder")) {
                // line 19
                echo "            src=\"/img/fftracker/cities/2.png\"
        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 20
($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 20) == "Immortal Flames")) {
                // line 21
                echo "            src=\"/img/fftracker/cities/5.png\"
        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 22
($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 22) == "Maelstrom")) {
                // line 23
                echo "            src=\"/img/fftracker/cities/4.png\"
        ";
            }
            // line 25
            echo "        >";
        }
        echo "<i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "grandCompany", [], "any", false, false, false, 25), "html", null, true);
        echo "</i>.
        ";
        // line 26
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 26), "estate", [], "any", false, false, false, 26), "ward", [], "any", false, false, false, 26)) {
            // line 27
            echo "            ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 27), "deleted", [], "any", false, false, false, 27)) {
                echo "Had";
            } else {
                echo "Has";
            }
            echo " a
            ";
            // line 28
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 28), "estate", [], "any", false, false, false, 28), "size", [], "any", false, false, false, 28) == 1)) {
                // line 29
                echo "                small
            ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 30
($context["freecompany"] ?? null), "location", [], "any", false, false, false, 30), "estate", [], "any", false, false, false, 30), "size", [], "any", false, false, false, 30) == 2)) {
                // line 31
                echo "                medium
            ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 32
($context["freecompany"] ?? null), "location", [], "any", false, false, false, 32), "estate", [], "any", false, false, false, 32), "size", [], "any", false, false, false, 32) == 3)) {
                // line 33
                echo "                large
            ";
            }
            // line 35
            echo "            ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 35), "estate", [], "any", false, false, false, 35), "name", [], "any", false, false, false, 35)) {
                // line 36
                echo "                base named <i>\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 36), "estate", [], "any", false, false, false, 36), "name", [], "any", false, false, false, 36), "html", null, true);
                echo "\"</i>
            ";
            } else {
                // line 38
                echo "                land
            ";
            }
            // line 40
            echo "            on plot <i>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "plot", [], "any", false, false, false, 40), "html", null, true);
            echo "</i> in ward <i>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "ward", [], "any", false, false, false, 40), "html", null, true);
            echo "</i> of <i>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "area", [], "any", false, false, false, 40), "html", null, true);
            echo "</i>, ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "city", [], "any", false, false, false, 40), "html", null, true);
            echo ", ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "region", [], "any", false, false, false, 40), "html", null, true);
            echo " <a class=\"galleryZoom\" href=\"/img/fftracker/maps/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "area", [], "any", false, false, false, 40), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "plot", [], "any", false, false, false, 40), "html", null, true);
            echo ".jpg\" target=\"_blank\" data-tooltip=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "area", [], "any", false, false, false, 40), "html", null, true);
            echo ", plot ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 40), "estate", [], "any", false, false, false, 40), "plot", [], "any", false, false, false, 40), "html", null, true);
            echo "\"><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" alt=\"Show on map\" data-tooltip=\"Show on map\" src=\"/img/fftracker/maps/zoom.png\"></a>.
        ";
        }
        // line 42
        echo "    </p>
    ";
        // line 43
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 43), "estate", [], "any", false, false, false, 43), "message", [], "any", false, false, false, 43)) {
            // line 44
            echo "        <p>";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 44), "deleted", [], "any", false, false, false, 44)) {
                echo "Had";
            } else {
                echo "Has";
            }
            echo " this message on its plot placard:</p>
        <blockquote>";
            // line 45
            echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "location", [], "any", false, false, false, 45), "estate", [], "any", false, false, false, 45), "message", [], "any", false, false, false, 45);
            echo "</blockquote>
    ";
        }
        // line 47
        echo "    ";
        if ((((((((((($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["Role-playing"] ?? null) : null) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Leveling", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Casual", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Hardcore", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Dungeons", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Guildhests", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Trials", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "Raids", [], "any", false, false, false, 47)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 47), "PvP", [], "any", false, false, false, 47))) {
            // line 48
            echo "        <p>Participate";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 48), "deleted", [], "any", false, false, false, 48)) {
                echo "d";
            } else {
                echo "s";
            }
            echo " in:</p>
        <ul>
            ";
            // line 50
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "focus", [], "any", false, false, false, 50));
            foreach ($context['_seq'] as $context["focus"] => $context["value"]) {
                // line 51
                echo "                ";
                if ($context["value"]) {
                    echo "<li><img loading=\"lazy\" decoding=\"async\" alt=\"";
                    echo twig_escape_filter($this->env, $context["focus"], "html", null, true);
                    echo "\" class=\"linkIcon\" src=\"/img/fftracker/focus/";
                    echo twig_escape_filter($this->env, $context["focus"], "html", null, true);
                    echo ".png\">";
                    echo twig_escape_filter($this->env, $context["focus"], "html", null, true);
                    echo "</li>";
                }
                // line 52
                echo "            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['focus'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 53
            echo "        </ul>
    ";
        }
        // line 55
        echo "    ";
        if ( !twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "dates", [], "any", false, false, false, 55), "deleted", [], "any", false, false, false, 55)) {
            // line 56
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "community", [], "any", false, false, false, 56)) {
                echo " Has an open <a href=\"https://eu.finalfantasyxiv.com/lodestone/community_finder/";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "community", [], "any", false, false, false, 56), "html", null, true);
                echo "\" target=\"_blank\">community</a>.";
            }
            // line 57
            echo "        ";
            if ((twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "recruiting", [], "any", false, false, false, 57) == 1)) {
                // line 58
                echo "            <p>Is recruiting";
                if (((((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 58), "Tank", [], "any", false, false, false, 58) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 58), "Healer", [], "any", false, false, false, 58)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 58), "DPS", [], "any", false, false, false, 58)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 58), "Crafter", [], "any", false, false, false, 58)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 58), "Gatherer", [], "any", false, false, false, 58))) {
                    echo ":";
                } else {
                    echo ", but does not specify whom.";
                }
                echo "</p>
            ";
                // line 59
                if (((((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 59), "Tank", [], "any", false, false, false, 59) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 59), "Healer", [], "any", false, false, false, 59)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 59), "DPS", [], "any", false, false, false, 59)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 59), "Crafter", [], "any", false, false, false, 59)) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 59), "Gatherer", [], "any", false, false, false, 59))) {
                    // line 60
                    echo "                <ul>
                    ";
                    // line 61
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "seeking", [], "any", false, false, false, 61));
                    foreach ($context['_seq'] as $context["seeking"] => $context["value"]) {
                        // line 62
                        echo "                        ";
                        if ($context["value"]) {
                            echo "<li><img loading=\"lazy\" decoding=\"async\" alt=\"";
                            echo twig_escape_filter($this->env, $context["seeking"], "html", null, true);
                            echo "\" class=\"linkIcon\" src=\"/img/fftracker/roles/";
                            echo twig_escape_filter($this->env, $context["seeking"], "html", null, true);
                            echo ".png\">";
                            echo twig_escape_filter($this->env, $context["seeking"], "html", null, true);
                            echo "</li>";
                        }
                        // line 63
                        echo "                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['seeking'], $context['value'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 64
                    echo "                </ul>
            ";
                }
                // line 66
                echo "        ";
            }
            // line 67
            echo "    ";
        }
        // line 68
        echo "    ";
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "oldNames", [], "any", false, false, false, 68)) > 0)) {
            // line 69
            echo "        <p>Had also been known under <i>";
            echo twig_escape_filter($this->env, twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "oldNames", [], "any", false, false, false, 69)), "html", null, true);
            echo "</i> other name";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "oldNames", [], "any", false, false, false, 69)) > 1)) {
                echo "s";
            }
            echo ":</p>
        <ul>
            ";
            // line 71
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "oldNames", [], "any", false, false, false, 71));
            foreach ($context['_seq'] as $context["_key"] => $context["name"]) {
                // line 72
                echo "                <li>";
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 74
            echo "        </ul>
    ";
        }
        // line 76
        echo "    <p>Current members:</p>
        <div class=\"searchResults\">
            ";
        // line 78
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["freecompany"] ?? null), "members", [], "any", false, false, false, 78));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 79
            echo "                ";
            echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["entity"]);
            echo "
            ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 81
        echo "        </div>

</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/freecompany.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  394 => 81,  377 => 79,  360 => 78,  356 => 76,  352 => 74,  343 => 72,  339 => 71,  329 => 69,  326 => 68,  323 => 67,  320 => 66,  316 => 64,  310 => 63,  299 => 62,  295 => 61,  292 => 60,  290 => 59,  281 => 58,  278 => 57,  271 => 56,  268 => 55,  264 => 53,  258 => 52,  247 => 51,  243 => 50,  233 => 48,  230 => 47,  225 => 45,  216 => 44,  214 => 43,  211 => 42,  189 => 40,  185 => 38,  179 => 36,  176 => 35,  172 => 33,  170 => 32,  167 => 31,  165 => 30,  162 => 29,  160 => 28,  151 => 27,  149 => 26,  142 => 25,  138 => 23,  136 => 22,  133 => 21,  131 => 20,  128 => 19,  126 => 18,  108 => 17,  79 => 15,  76 => 14,  73 => 13,  69 => 11,  67 => 10,  64 => 9,  62 => 8,  59 => 7,  56 => 6,  46 => 4,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/freecompany.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\freecompany.twig");
    }
}
