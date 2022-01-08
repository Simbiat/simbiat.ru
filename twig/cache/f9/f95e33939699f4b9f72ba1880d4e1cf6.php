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

/* fftracker/character.twig */
class __TwigTemplate_11141f57050d37b113bfe64f4214d60a extends Template
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
        if (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 1), "gender", [], "any", false, false, false, 1) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 1), "race", [], "any", false, false, false, 1) == "Miqo'te"))) {
            // line 2
            echo "    ";
            $context["adjective"] = "sexy";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 3
($context["character"] ?? null), "biology", [], "any", false, false, false, 3), "gender", [], "any", false, false, false, 3) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 3), "race", [], "any", false, false, false, 3) == "Miqo'te"))) {
            // line 4
            echo "    ";
            $context["adjective"] = "haughty";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 5
($context["character"] ?? null), "biology", [], "any", false, false, false, 5), "gender", [], "any", false, false, false, 5) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 5), "race", [], "any", false, false, false, 5) == "Lalafell"))) {
            // line 6
            echo "    ";
            $context["adjective"] = "adorable";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 7
($context["character"] ?? null), "biology", [], "any", false, false, false, 7), "gender", [], "any", false, false, false, 7) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 7), "race", [], "any", false, false, false, 7) == "Lalafell"))) {
            // line 8
            echo "    ";
            $context["adjective"] = "tiny";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 9
($context["character"] ?? null), "biology", [], "any", false, false, false, 9), "gender", [], "any", false, false, false, 9) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 9), "race", [], "any", false, false, false, 9) == "Elezen"))) {
            // line 10
            echo "    ";
            $context["adjective"] = "tall";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 11
($context["character"] ?? null), "biology", [], "any", false, false, false, 11), "gender", [], "any", false, false, false, 11) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 11), "race", [], "any", false, false, false, 11) == "Elezen"))) {
            // line 12
            echo "    ";
            $context["adjective"] = "lofty";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 13
($context["character"] ?? null), "biology", [], "any", false, false, false, 13), "gender", [], "any", false, false, false, 13) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 13), "race", [], "any", false, false, false, 13) == "Roegadyn"))) {
            // line 14
            echo "    ";
            $context["adjective"] = "bodacious";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 15
($context["character"] ?? null), "biology", [], "any", false, false, false, 15), "gender", [], "any", false, false, false, 15) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 15), "race", [], "any", false, false, false, 15) == "Roegadyn"))) {
            // line 16
            echo "    ";
            $context["adjective"] = "bulky";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 17
($context["character"] ?? null), "biology", [], "any", false, false, false, 17), "gender", [], "any", false, false, false, 17) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 17), "race", [], "any", false, false, false, 17) == "Hrothgar"))) {
            // line 18
            echo "    ";
            $context["adjective"] = "inconspicious";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 19
($context["character"] ?? null), "biology", [], "any", false, false, false, 19), "gender", [], "any", false, false, false, 19) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 19), "race", [], "any", false, false, false, 19) == "Hrothgar"))) {
            // line 20
            echo "    ";
            $context["adjective"] = "hairy";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 21
($context["character"] ?? null), "biology", [], "any", false, false, false, 21), "gender", [], "any", false, false, false, 21) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 21), "race", [], "any", false, false, false, 21) == "Hyur"))) {
            // line 22
            echo "    ";
            $context["adjective"] = "flexible";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 23
($context["character"] ?? null), "biology", [], "any", false, false, false, 23), "gender", [], "any", false, false, false, 23) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 23), "race", [], "any", false, false, false, 23) == "Hyur"))) {
            // line 24
            echo "    ";
            $context["adjective"] = "handsome";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 25
($context["character"] ?? null), "biology", [], "any", false, false, false, 25), "gender", [], "any", false, false, false, 25) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 25), "race", [], "any", false, false, false, 25) == "Au Ra"))) {
            // line 26
            echo "    ";
            $context["adjective"] = "horny";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 27
($context["character"] ?? null), "biology", [], "any", false, false, false, 27), "gender", [], "any", false, false, false, 27) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 27), "race", [], "any", false, false, false, 27) == "Au Ra"))) {
            // line 28
            echo "    ";
            $context["adjective"] = "scaly";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 29
($context["character"] ?? null), "biology", [], "any", false, false, false, 29), "gender", [], "any", false, false, false, 29) == 0) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 29), "race", [], "any", false, false, false, 29) == "Viera"))) {
            // line 30
            echo "    ";
            $context["adjective"] = "fast";
        } elseif (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 31
($context["character"] ?? null), "biology", [], "any", false, false, false, 31), "gender", [], "any", false, false, false, 31) == 1) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 31), "race", [], "any", false, false, false, 31) == "Viera"))) {
            // line 32
            echo "    ";
            $context["adjective"] = "eared";
        }
        // line 34
        echo "<section class=\"ff_char_page\">
    <section class=\"ff_char_block\">
        ";
        // line 36
        if ( !twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "avatarID", [], "any", false, false, false, 36)) {
            // line 37
            echo "            <img loading=\"lazy\" decoding=\"async\" id=\"ff_portrait_img\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "name", [], "any", false, false, false, 37), "html", null, true);
            echo "\" src=\"/img/fftracker/silhouettes/";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 37), "gender", [], "any", false, false, false, 37) == 1)) {
                echo "male";
            } else {
                echo "female";
            }
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 37), "race", [], "any", false, false, false, 37), "html", null, true);
            echo ".png\">
        ";
        } else {
            // line 39
            echo "            <img loading=\"lazy\" decoding=\"async\" id=\"ff_portrait_img\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "name", [], "any", false, false, false, 39), "html", null, true);
            echo "\" src=\"/img/fftracker/avatars/640x873/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "id", [], "any", false, false, false, 39), "html", null, true);
            echo ".jpg\">
        ";
        }
        // line 41
        echo "    </section>
    <section class=\"ff_char_block\">
        <h2>General</h2>
        <p><b>";
        // line 44
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "name", [], "any", false, false, false, 44), "html", null, true);
        echo "</b>";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "title", [], "any", false, false, false, 44), "title", [], "any", false, false, false, 44)) {
            echo ", a.k.a <i>\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "title", [], "any", false, false, false, 44), "title", [], "any", false, false, false, 44), "html", null, true);
            echo "\"</i>,";
        }
        echo " is a ";
        echo twig_escape_filter($this->env, ($context["adjective"] ?? null), "html", null, true);
        echo " ";
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 44), "gender", [], "any", false, false, false, 44) == 1)) {
            echo "male";
        } else {
            echo "female";
        }
        echo " <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 44), "race", [], "any", false, false, false, 44), "html", null, true);
        echo "</i> of <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 44), "clan", [], "any", false, false, false, 44), "html", null, true);
        echo "</i> clan, registered in the database on <time datetime=\"";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 44), "registered", [], "any", false, false, false, 44), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 44), "registered", [], "any", false, false, false, 44), "d/m/Y"), "html", null, true);
        echo "</time> with <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "id", [], "any", false, false, false, 44), "html", null, true);
        echo "</i> for ID.";
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 44), "oldNames", [], "any", false, false, false, 44)) > 0)) {
            echo " Had also been known under <i>";
            echo twig_escape_filter($this->env, twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 44), "oldNames", [], "any", false, false, false, 44)), "html", null, true);
            echo "</i> other name";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 44), "oldNames", [], "any", false, false, false, 44)) > 1)) {
                echo "s";
            }
            echo ".";
        }
        echo "</p>
        <p>
            Born on <i>";
        // line 46
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "nameday", [], "any", false, false, false, 46), "html", null, true);
        echo "</i> under protection of <img loading=\"lazy\" decoding=\"async\" alt=\"";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "guardian", [], "any", false, false, false, 46), "html", null, true);
        echo "\" class=\"linkIcon\" src=\"/img/fftracker/guardians/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "guardianid", [], "any", false, false, false, 46), "html", null, true);
        echo ".png\"><i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "guardian", [], "any", false, false, false, 46), "html", null, true);
        echo "</i>.";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 46), "deleted", [], "any", false, false, false, 46)) {
            echo " ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "killedby", [], "any", false, false, false, 46)) {
                echo "&#9760; Killed";
            } else {
                echo "&#129702; Died";
            }
            echo " on <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 46), "deleted", [], "any", false, false, false, 46), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 46), "deleted", [], "any", false, false, false, 46), "d/m/Y"), "html", null, true);
            echo "</time> ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "killedby", [], "any", false, false, false, 46)) {
                echo "by ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 46), "killedby", [], "any", false, false, false, 46), "html", null, true);
                echo " &#9760;";
            } else {
                echo "from old age &#129702;";
            }
            echo ". ";
        }
        // line 47
        echo "        </p>
        <p>
            ";
        // line 49
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 49), "incarnations", [], "any", false, false, false, 49)) {
            // line 50
            echo "                Other known incarnations:
                ";
            // line 51
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 51), "incarnations", [], "any", false, false, false, 51));
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
            foreach ($context['_seq'] as $context["_key"] => $context["incarnation"]) {
                // line 52
                echo "                    ";
                if (twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 52)) {
                    echo "and ";
                }
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biology", [], "any", false, false, false, 52), "gender", [], "any", false, false, false, 52) == 1)) {
                    echo "male";
                } else {
                    echo "female";
                }
                echo " ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["incarnation"], "race", [], "any", false, false, false, 52), "html", null, true);
                echo " of ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["incarnation"], "clan", [], "any", false, false, false, 52), "html", null, true);
                if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 52)) {
                    echo ", ";
                } else {
                    echo ".";
                }
                // line 53
                echo "                ";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['incarnation'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            echo "                .
            ";
        }
        // line 56
        echo "        </p>
        <p>
            ";
        // line 58
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 58), "deleted", [], "any", false, false, false, 58)) {
            echo "Was";
        } else {
            echo "Currently is";
        }
        echo " resident of <img loading=\"lazy\" decoding=\"async\" alt=\"";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "city", [], "any", false, false, false, 58), "html", null, true);
        echo "\" class=\"linkIcon\" src=\"/img/fftracker/cities/";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "cityid", [], "any", false, false, false, 58), "html", null, true);
        echo ".png\"><i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "city", [], "any", false, false, false, 58), "html", null, true);
        echo "</i>, <i>";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "region", [], "any", false, false, false, 58), "html", null, true);
        echo "</i>";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "server", [], "any", false, false, false, 58)) {
            echo " on ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "server", [], "any", false, false, false, 58), "html", null, true);
            echo " of ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 58), "datacenter", [], "any", false, false, false, 58), "html", null, true);
        }
        echo ".
            ";
        // line 59
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 59), "previousServers", [], "any", false, false, false, 59)) {
            // line 60
            echo "                Has also been seen on
                ";
            // line 61
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "location", [], "any", false, false, false, 61), "previousServers", [], "any", false, false, false, 61));
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
            foreach ($context['_seq'] as $context["_key"] => $context["server"]) {
                // line 62
                echo "                    ";
                if (twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 62)) {
                    echo "and ";
                }
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "server", [], "any", false, false, false, 62), "html", null, true);
                echo " of ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "datacenter", [], "any", false, false, false, 62), "html", null, true);
                if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 62)) {
                    echo ", ";
                } else {
                    echo ".";
                }
                // line 63
                echo "                ";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['server'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 64
            echo "            ";
        }
        // line 65
        echo "        </p>
        ";
        // line 66
        if (twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "pvp", [], "any", false, false, false, 66)) {
            // line 67
            echo "            <p>Participated in <i class=\"failure\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "pvp", [], "any", false, false, false, 67), "html", null, true);
            echo "</i> &#9876; battles.</p>
        ";
        }
        // line 69
        echo "        ";
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "grandCompany", [], "any", false, false, false, 69), "name", [], "any", false, false, false, 69)) {
            // line 70
            echo "            <p>Reached rank of <img loading=\"lazy\" decoding=\"async\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "grandCompany", [], "any", false, false, false, 70), "rank", [], "any", false, false, false, 70), "html", null, true);
            echo "\" class=\"linkIcon\" src=\"/img/fftracker/grandcompany/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "grandCompany", [], "any", false, false, false, 70), "gcrankid", [], "any", false, false, false, 70), "html", null, true);
            echo ".png\"><i>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "grandCompany", [], "any", false, false, false, 70), "rank", [], "any", false, false, false, 70), "html", null, true);
            echo "</i> in <i>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "grandCompany", [], "any", false, false, false, 70), "name", [], "any", false, false, false, 70), "html", null, true);
            echo "</i> Grand company.</p>
        ";
        }
        // line 72
        echo "        <p>
            Last interview was conducted on <time datetime=\"";
        // line 73
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 73), "updated", [], "any", false, false, false, 73), "Y-m-d"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "dates", [], "any", false, false, false, 73), "updated", [], "any", false, false, false, 73), "d/m/Y"), "html", null, true);
        echo "</time>.
            ";
        // line 74
        if (twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biography", [], "any", false, false, false, 74)) {
            // line 75
            echo "                This is what adventurer had to say during it:
            ";
        }
        // line 77
        echo "        </p>
        ";
        // line 78
        if (twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biography", [], "any", false, false, false, 78)) {
            // line 79
            echo "            <blockquote>";
            echo twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "biography", [], "any", false, false, false, 79);
            echo "</blockquote>
        ";
        }
        // line 81
        echo "    </section>
    ";
        // line 82
        if (twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "groups", [], "any", false, false, false, 82)) {
            // line 83
            echo "        <section class=\"ff_char_block\">
            <h2>Affiliations</h2>
            ";
            // line 85
            if (twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "groups", [], "any", false, false, false, 85)) {
                // line 86
                echo "                <div class=\"ff_groups\">
                    ";
                // line 87
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "groups", [], "any", false, false, false, 87));
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
                    // line 88
                    echo "                        <div>
                            ";
                    // line 89
                    if (twig_get_attribute($this->env, $this->source, $context["entity"], "current", [], "any", false, false, false, 89)) {
                        echo "<span class=\"emojiGreen\">&#10004;</span>";
                    } else {
                        echo "<span class=\"emojiRed\">&#10060;</span>";
                    }
                    // line 90
                    echo "                            ";
                    echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["entity"]);
                    if (twig_get_attribute($this->env, $this->source, $context["entity"], "rankname", [], "any", false, false, false, 90)) {
                        echo " as <i>";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["entity"], "rankname", [], "any", false, false, false, 90), "html", null, true);
                        echo "</i>";
                    }
                    // line 91
                    echo "                        </div>
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
                // line 93
                echo "                </div>
            ";
            }
            // line 95
            echo "        </section>
    ";
        }
        // line 97
        echo "    ";
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "achievements", [], "any", false, false, false, 97)) > 0)) {
            // line 98
            echo "        <section class=\"ff_char_block\">
            <h2>Last achievements</h2>
            <div class=\"searchResults\">
                ";
            // line 101
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_slice($this->env, twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "achievements", [], "any", false, false, false, 101), 0, 10));
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
                // line 102
                echo "                    ";
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
            // line 104
            echo "            </div>
        </section>
    ";
        }
        // line 107
        echo "    <section class=\"ff_char_block\">
        <h2>Job affinities</h2>
        <table>
            <thead><tr><th>Job</th><th>Level</th></tr></thead>
            ";
        // line 111
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["character"] ?? null), "jobs", [], "any", false, false, false, 111));
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
        foreach ($context['_seq'] as $context["key"] => $context["job"]) {
            // line 112
            echo "                ";
            $context["sum"] = (twig_get_attribute($this->env, $this->source, $context["job"], "level", [], "any", false, false, false, 112) + (($context["sum"]) ?? (0)));
            // line 113
            echo "                <tr><td><img loading=\"lazy\" decoding=\"async\" class=\"linkIcon\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["job"], "name", [], "any", false, false, false, 113), "html", null, true);
            echo "\" src=\"/img/fftracker/jobs/";
            echo twig_escape_filter($this->env, $context["key"], "html", null, true);
            echo ".png\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["job"], "name", [], "any", false, false, false, 113), "html", null, true);
            echo "</td><td>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["job"], "level", [], "any", false, false, false, 113), "html", null, true);
            echo "</td></tr>
                ";
            // line 114
            if (twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 114)) {
                // line 115
                echo "                    <tfoot><tr><td>Total:</td><td>";
                echo twig_escape_filter($this->env, ($context["sum"] ?? null), "html", null, true);
                echo "</td></tr></tfoot>
                ";
            }
            // line 117
            echo "            ";
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
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['job'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 118
        echo "        </table>
    </section>
</section>
";
    }

    public function getTemplateName()
    {
        return "fftracker/character.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  590 => 118,  576 => 117,  570 => 115,  568 => 114,  557 => 113,  554 => 112,  537 => 111,  531 => 107,  526 => 104,  509 => 102,  492 => 101,  487 => 98,  484 => 97,  480 => 95,  476 => 93,  461 => 91,  453 => 90,  447 => 89,  444 => 88,  427 => 87,  424 => 86,  422 => 85,  418 => 83,  416 => 82,  413 => 81,  407 => 79,  405 => 78,  402 => 77,  398 => 75,  396 => 74,  390 => 73,  387 => 72,  375 => 70,  372 => 69,  366 => 67,  364 => 66,  361 => 65,  358 => 64,  344 => 63,  331 => 62,  314 => 61,  311 => 60,  309 => 59,  286 => 58,  282 => 56,  278 => 54,  264 => 53,  245 => 52,  228 => 51,  225 => 50,  223 => 49,  219 => 47,  189 => 46,  150 => 44,  145 => 41,  137 => 39,  124 => 37,  122 => 36,  118 => 34,  114 => 32,  112 => 31,  109 => 30,  107 => 29,  104 => 28,  102 => 27,  99 => 26,  97 => 25,  94 => 24,  92 => 23,  89 => 22,  87 => 21,  84 => 20,  82 => 19,  79 => 18,  77 => 17,  74 => 16,  72 => 15,  69 => 14,  67 => 13,  64 => 12,  62 => 11,  59 => 10,  57 => 9,  54 => 8,  52 => 7,  49 => 6,  47 => 5,  44 => 4,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "fftracker/character.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\fftracker\\character.twig");
    }
}
