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

/* bictracker/bic.twig */
class __TwigTemplate_75cc2eeb2ab2de6e13a499d8c3349d80 extends Template
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
    ";
        // line 2
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateOut", [], "any", false, false, false, 2)) {
            // line 3
            echo "        <p class=\"noLetter failure\"><span class=\"emojiRed\">&#10060;</span>Организация закрыта <time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateOut", [], "any", false, false, false, 3), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateOut", [], "any", false, false, false, 3), "d/m/Y"), "html", null, true);
            echo "</time><span class=\"emojiRed\">&#10060;</span></p>
    ";
        }
        // line 5
        echo "    <table>
        <caption><h2 class=\"zeroMargin\">Информация для расчётов</h2></caption>
        <tbody>
            <tr>
                <td>Наименование:</td><td>";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "NameP", [], "any", false, false, false, 9), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Наименование на английском языке:</td><td>";
        // line 12
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "EnglName", [], "any", true, true, false, 12)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "EnglName", [], "any", false, false, false, 12), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Банковский идентификационный код:</td><td>";
        // line 15
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "OLD_NEWNUM", [], "any", false, false, false, 15)) {
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "OLD_NEWNUM", [], "any", false, false, false, 15), "html", null, true);
        } else {
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "BIC", [], "any", true, true, false, 15)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "BIC", [], "any", false, false, false, 15), "-")) : ("-")), "html", null, true);
        }
        echo "</td>
            </tr>
            <tr>
                <td>Уникальный идентификатор составителя:</td><td>";
        // line 18
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "UID", [], "any", true, true, false, 18)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "UID", [], "any", false, false, false, 18), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            ";
        // line 20
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 20)) == 1)) {
            // line 21
            echo "                <tr>
                    <td><abbr data-tooltip=\"Банковский идентификационный код\">БИК</abbr> системы <abbr data-tooltip=\"Society for Worldwide Interbank Financial Telecommunications\">SWIFT</abbr>:</td>
                    <td>";
            // line 23
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 23), 0, [], "any", false, false, false, 23), "SWBIC", [], "any", false, false, false, 23), "html", null, true);
            echo "
                        ";
            // line 24
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 24), 0, [], "any", false, false, false, 24), "DateIn", [], "any", false, false, false, 24)) {
                // line 25
                echo "                            с <time datetime=\"";
                echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 25), 0, [], "any", false, false, false, 25), "DateIn", [], "any", false, false, false, 25), "Y-m-d"), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 25), 0, [], "any", false, false, false, 25), "DateIn", [], "any", false, false, false, 25), "d/m/Y"), "html", null, true);
                echo "</time>
                        ";
            }
            // line 27
            echo "                        ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 27), 0, [], "any", false, false, false, 27), "DateOut", [], "any", false, false, false, 27)) {
                // line 28
                echo "                            до <time datetime=\"";
                echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 28), 0, [], "any", false, false, false, 28), "DateOut", [], "any", false, false, false, 28), "Y-m-d"), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 28), 0, [], "any", false, false, false, 28), "DateOut", [], "any", false, false, false, 28), "d/m/Y"), "html", null, true);
                echo "</time>
                        ";
            }
            // line 30
            echo "                    </td>
                </tr>
            ";
        }
        // line 33
        echo "        </tbody>
    </table>
    ";
        // line 35
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 35)) > 1)) {
            // line 36
            echo "        <table>
            <caption><h2 class=\"zeroMargin\">SWIFT коды</h2></caption>
            <tbody>
                <tr>
                    <th><abbr data-tooltip=\"Банковский идентификационный код\">БИК</abbr> системы <abbr data-tooltip=\"Society for Worldwide Interbank Financial Telecommunications\">SWIFT</abbr></th><th>По умолчанию</th><th>Начало действия</th><th>Конец действия</th>
                </tr>
                ";
            // line 42
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 42));
            foreach ($context['_seq'] as $context["_key"] => $context["code"]) {
                // line 43
                echo "                    ";
                if ( !twig_get_attribute($this->env, $this->source, $context["code"], "DateOut", [], "any", false, false, false, 43)) {
                    // line 44
                    echo "                        <tr>
                            <td class=\"success\">";
                    // line 45
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "SWBIC", [], "any", false, false, false, 45), "html", null, true);
                    echo "</td><td>";
                    if ((twig_get_attribute($this->env, $this->source, $context["code"], "DefaultSWBIC", [], "any", false, false, false, 45) == 1)) {
                        echo "<span class=\"success bold\">Да</span>";
                    } else {
                        echo "Нет";
                    }
                    echo "</td><td>";
                    if (twig_get_attribute($this->env, $this->source, $context["code"], "DateIn", [], "any", false, false, false, 45)) {
                        echo "<time datetime=\"";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "DateIn", [], "any", false, false, false, 45), "Y-m-d"), "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "DateIn", [], "any", false, false, false, 45), "d/m/Y"), "html", null, true);
                        echo "</time>";
                    } else {
                        echo "-";
                    }
                    echo "</td><td>-</td>
                        </tr>
                    ";
                }
                // line 48
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['code'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 49
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "SWIFTs", [], "any", false, false, false, 49));
            foreach ($context['_seq'] as $context["_key"] => $context["code"]) {
                // line 50
                echo "                    ";
                if (twig_get_attribute($this->env, $this->source, $context["code"], "DateOut", [], "any", false, false, false, 50)) {
                    // line 51
                    echo "                        <tr>
                            <td>";
                    // line 52
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "SWBIC", [], "any", false, false, false, 52), "html", null, true);
                    echo "</td><td>";
                    if ((twig_get_attribute($this->env, $this->source, $context["code"], "DefaultSWBIC", [], "any", false, false, false, 52) == 1)) {
                        echo "<span class=\"success bold\">Да</span>";
                    } else {
                        echo "Нет";
                    }
                    echo "</td><td>";
                    if (twig_get_attribute($this->env, $this->source, $context["code"], "DateIn", [], "any", false, false, false, 52)) {
                        echo "<time datetime=\"";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "DateIn", [], "any", false, false, false, 52), "Y-m-d"), "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "DateIn", [], "any", false, false, false, 52), "d/m/Y"), "html", null, true);
                        echo "</time>";
                    } else {
                        echo "-";
                    }
                    echo "</td><td><time class=\"failure\" datetime=\"";
                    echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "DateOut", [], "any", false, false, false, 52), "Y-m-d"), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["code"], "DateOut", [], "any", false, false, false, 52), "d/m/Y"), "html", null, true);
                    echo "</time></td>
                        </tr>
                    ";
                }
                // line 55
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['code'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 56
            echo "            </tbody>
        </table>
    ";
        }
        // line 59
        echo "    ";
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "accounts", [], "any", false, false, false, 59)) > 0)) {
            // line 60
            echo "        <table>
            <caption><h2 class=\"zeroMargin\">Счета</h2></caption>
            <tbody>
                <tr>
                    <th>Счёт</th><th>Тип счёта</th><th>Контрольный ключ</th><th>Обслуживание</th><th>Начало действия</th><th>Конец действия</th>
                </tr>
                ";
            // line 66
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "accounts", [], "any", false, false, false, 66));
            foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
                // line 67
                echo "                    ";
                if ( !twig_get_attribute($this->env, $this->source, $context["account"], "DateOut", [], "any", false, false, false, 67)) {
                    // line 68
                    echo "                        <tr>
                            <td class=\"success\">";
                    // line 69
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "Account", [], "any", false, false, false, 69), "html", null, true);
                    echo "</td><td>";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "AccountType", [], "any", false, false, false, 69), "html", null, true);
                    echo "</td><td>";
                    echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["account"], "CK", [], "any", true, true, false, 69)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, $context["account"], "CK", [], "any", false, false, false, 69), "-")) : ("-")), "html", null, true);
                    echo "</td><td>";
                    if (twig_get_attribute($this->env, $this->source, $context["account"], "AccountCBRBIC", [], "any", false, false, false, 69)) {
                        echo "<a href=\"/bictracker/bic/";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "AccountCBRBIC", [], "any", false, false, false, 69), "html", null, true);
                        echo "/\">";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "AccountCBRBIC", [], "any", false, false, false, 69), "html", null, true);
                        echo "</a>";
                    } else {
                        echo "-";
                    }
                    echo "</td><td>";
                    if (twig_get_attribute($this->env, $this->source, $context["account"], "DateIn", [], "any", false, false, false, 69)) {
                        echo "<time datetime=\"";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "DateIn", [], "any", false, false, false, 69), "Y-m-d"), "html", null, true);
                        echo "\"";
                        if ((twig_date_converter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 69)) == twig_date_converter($this->env, "1996-07-10"))) {
                            echo " data-tooltip=\"Точная дата добавления неизвестна, используется дата внедрения библиотеки БИК\"";
                        }
                        echo ">";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "DateIn", [], "any", false, false, false, 69), "d/m/Y"), "html", null, true);
                        echo "</time>";
                    } else {
                        echo "-";
                    }
                    echo "</td><td>-</td>
                        </tr>
                    ";
                }
                // line 72
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 73
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "accounts", [], "any", false, false, false, 73));
            foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
                // line 74
                echo "                    ";
                if (twig_get_attribute($this->env, $this->source, $context["account"], "DateOut", [], "any", false, false, false, 74)) {
                    // line 75
                    echo "                        <tr>
                            <td>";
                    // line 76
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "Account", [], "any", false, false, false, 76), "html", null, true);
                    echo "</td><td>";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "AccountType", [], "any", false, false, false, 76), "html", null, true);
                    echo "</td><td>";
                    echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, $context["account"], "CK", [], "any", true, true, false, 76)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, $context["account"], "CK", [], "any", false, false, false, 76), "-")) : ("-")), "html", null, true);
                    echo "</td><td>";
                    if (twig_get_attribute($this->env, $this->source, $context["account"], "AccountCBRBIC", [], "any", false, false, false, 76)) {
                        echo "<a href=\"/bictracker/bic/";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "AccountCBRBIC", [], "any", false, false, false, 76), "html", null, true);
                        echo "/\">";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "AccountCBRBIC", [], "any", false, false, false, 76), "html", null, true);
                        echo "</a>";
                    } else {
                        echo "-";
                    }
                    echo "</td><td>";
                    if (twig_get_attribute($this->env, $this->source, $context["account"], "DateIn", [], "any", false, false, false, 76)) {
                        echo "<time datetime=\"";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "DateIn", [], "any", false, false, false, 76), "Y-m-d"), "html", null, true);
                        echo "\"";
                        if ((twig_date_converter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 76)) == twig_date_converter($this->env, "1996-07-10"))) {
                            echo " data-tooltip=\"Точная дата добавления неизвестна, используется дата внедрения библиотеки БИК\"";
                        }
                        echo ">";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "DateIn", [], "any", false, false, false, 76), "d/m/Y"), "html", null, true);
                        echo "</time>";
                    } else {
                        echo "-";
                    }
                    echo "</td><td><time class=\"failure\" datetime=\"";
                    echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "DateOut", [], "any", false, false, false, 76), "Y-m-d"), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "DateOut", [], "any", false, false, false, 76), "d/m/Y"), "html", null, true);
                    echo "</time></td>
                        </tr>
                    ";
                }
                // line 79
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 80
            echo "            </tbody>
        </table>
    ";
        }
        // line 83
        echo "    <table>
        <caption><h2 class=\"zeroMargin\">Контакты</h2></caption>
        <tbody>
            <tr>
                <td>Код страны:</td><td>";
        // line 87
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "CntrCd", [], "any", true, true, false, 87)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "CntrCd", [], "any", false, false, false, 87), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Регион:</td><td>";
        // line 90
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Rgn", [], "any", true, true, false, 90)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Rgn", [], "any", false, false, false, 90), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Административный центр:</td><td>";
        // line 93
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "CENTER", [], "any", true, true, false, 93)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "CENTER", [], "any", false, false, false, 93), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Адрес:</td><td>";
        // line 96
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Adr", [], "any", false, false, false, 96)) {
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Ind", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Ind", [], "any", false, false, false, 96), "html", null, true);
                echo ", ";
            }
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Tnp", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Tnp", [], "any", false, false, false, 96), "html", null, true);
                echo " ";
            }
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Nnp", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Nnp", [], "any", false, false, false, 96), "html", null, true);
                echo ", ";
            }
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Adr", [], "any", false, false, false, 96), "html", null, true);
            echo " <a target=\"_blank\" href=\"https://yandex.ru/maps/?mode=search&amp;text=";
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Ind", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Ind", [], "any", false, false, false, 96)), "html", null, true);
                echo "%2C%20";
            }
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Tnp", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Tnp", [], "any", false, false, false, 96)), "html", null, true);
                echo "%20";
            }
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Nnp", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Nnp", [], "any", false, false, false, 96)), "html", null, true);
                echo "%2C%20";
            }
            echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Adr", [], "any", false, false, false, 96)), "html", null, true);
            echo "\"><img src=\"/img/icons/YandexMaps.svg\" class=\"linkIcon\" alt=\"Яндекс Карты\"></a><a target=\"_blank\" href=\"https://www.google.com/maps/search/";
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Ind", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Ind", [], "any", false, false, false, 96)), "html", null, true);
                echo "%2C%20";
            }
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Tnp", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Tnp", [], "any", false, false, false, 96)), "html", null, true);
                echo "%20";
            }
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Nnp", [], "any", false, false, false, 96)) {
                echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Nnp", [], "any", false, false, false, 96)), "html", null, true);
                echo "%2C%20";
            }
            echo twig_escape_filter($this->env, twig_urlencode_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Adr", [], "any", false, false, false, 96)), "html", null, true);
            echo "\"><img src=\"/img/icons/GoogleMaps.svg\" class=\"linkIcon\" alt=\"Google Maps\"></a>";
        } else {
            echo "-";
        }
        echo "</td>
            </tr>
        </tbody>
    </table>
    <table>
        <caption><h2 class=\"zeroMargin\">Служебная информация</h2></caption>
        <tbody>
            <tr>
                <td>Участник обмена:</td><td>";
        // line 104
        if ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "XchType", [], "any", false, false, false, 104) == 1)) {
            echo "Да";
        } else {
            echo "Нет";
        }
        echo "</td>
            </tr>
            <tr>
                <td>Доступные сервисы переводов:</td><td>";
        // line 107
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Srvcs", [], "any", true, true, false, 107)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Srvcs", [], "any", false, false, false, 107), "Нет")) : ("Нет")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Тип участника расчётов:</td><td>";
        // line 110
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PtType", [], "any", true, true, false, 110)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PtType", [], "any", false, false, false, 110), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Регистрационный номер:</td><td>";
        // line 113
        echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "RegN", [], "any", true, true, false, 113)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "RegN", [], "any", false, false, false, 113), "-")) : ("-")), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Обслуживает организаций:</td><td>";
        // line 116
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "serviceFor", [], "any", false, false, false, 116), "html", null, true);
        echo "</td>
            </tr>
            <tr>
                <td>Дата включения в справочник:</td><td>";
        // line 119
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 119)) {
            echo "<time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 119), "Y-m-d"), "html", null, true);
            echo "\"";
            if ((twig_date_converter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 119)) == twig_date_converter($this->env, "1996-07-10"))) {
                echo " data-tooltip=\"Точная дата добавления неизвестна, используется дата внедрения библиотеки БИК\"";
            }
            echo ">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 119), "d/m/Y"), "html", null, true);
            echo "</time>";
        } else {
            echo "-";
        }
        echo "</td>
            </tr>
            <tr>
                <td>Дата последнего изменения:</td><td>";
        // line 122
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Updated", [], "any", false, false, false, 122)) {
            echo "<time datetime=\"";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Updated", [], "any", false, false, false, 122), "Y-m-d"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "Updated", [], "any", false, false, false, 122), "d/m/Y"), "html", null, true);
            echo "</time>";
        } else {
            echo "-";
        }
        echo "</td>
            </tr>
        </tbody>
    </table>
    ";
        // line 126
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "restrictions", [], "any", false, false, false, 126)) {
            // line 127
            echo "        <h2>История ограничений</h2>
        ";
            // line 128
            echo call_user_func_array($this->env->getFunction('timeline')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "restrictions", [], "any", false, false, false, 128)]);
            echo "
    ";
        }
        // line 130
        echo "    ";
        if ((twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PrntBIC", [], "any", false, false, false, 130) || twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "branches", [], "any", false, false, false, 130))) {
            echo "<h2>Родственные организации</h2>";
        }
        // line 131
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PrntBIC", [], "any", false, false, false, 131)) {
            // line 132
            echo "        <h3>Головн";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PrntBIC", [], "any", false, false, false, 132)) > 1)) {
                echo "ые";
            } else {
                echo "ая";
            }
            echo " организаци";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PrntBIC", [], "any", false, false, false, 132)) > 1)) {
                echo "и";
            } else {
                echo "я";
            }
            echo "</h3>
        <div class=\"searchResults bicChain\">
            ";
            // line 134
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "PrntBIC", [], "any", false, false, false, 134));
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
            foreach ($context['_seq'] as $context["_key"] => $context["bank"]) {
                // line 135
                echo "                ";
                echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["bank"]);
                if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 135)) {
                    echo " >>> ";
                }
                // line 136
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bank'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 137
            echo "        </div>
    ";
        }
        // line 139
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "branches", [], "any", false, false, false, 139)) {
            // line 140
            echo "        <h3>Дочерн";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "branches", [], "any", false, false, false, 140)) > 1)) {
                echo "ие";
            } else {
                echo "яя";
            }
            echo " организаци";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "branches", [], "any", false, false, false, 140)) > 1)) {
                echo "и";
            } else {
                echo "я";
            }
            echo "</h3>
        <div class=\"searchResults\">
            ";
            // line 142
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "branches", [], "any", false, false, false, 142));
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
            foreach ($context['_seq'] as $context["_key"] => $context["branch"]) {
                // line 143
                echo "                ";
                if ( !twig_get_attribute($this->env, $this->source, $context["branch"], "DateOut", [], "any", false, false, false, 143)) {
                    // line 144
                    echo "                    ";
                    echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["branch"]);
                    echo "
                ";
                }
                // line 146
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['branch'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 147
            echo "        </div>
        <div class=\"searchResults\">
            ";
            // line 149
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "branches", [], "any", false, false, false, 149));
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
            foreach ($context['_seq'] as $context["_key"] => $context["branch"]) {
                // line 150
                echo "                ";
                if (twig_get_attribute($this->env, $this->source, $context["branch"], "DateOut", [], "any", false, false, false, 150)) {
                    // line 151
                    echo "                    ";
                    echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["branch"]);
                    echo "
                ";
                }
                // line 153
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['branch'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 154
            echo "        </div>
    ";
        }
        // line 156
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "sameBIC", [], "any", false, false, false, 156)) {
            // line 157
            echo "        <h3>Организаци";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "sameBIC", [], "any", false, false, false, 157)) > 1)) {
                echo "и";
            } else {
                echo "я";
            }
            echo " с тем же <abbr data-tooltip=\"Банковский идентификационный код\">БИК</abbr></h3>
        <div class=\"searchResults\">
            ";
            // line 159
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "sameBIC", [], "any", false, false, false, 159));
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
            foreach ($context['_seq'] as $context["_key"] => $context["bank"]) {
                // line 160
                echo "                ";
                echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["bank"]);
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bank'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 162
            echo "        </div>
    ";
        }
        // line 164
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "sameAddress", [], "any", false, false, false, 164)) {
            // line 165
            echo "        <h3>Организаци";
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "sameAddress", [], "any", false, false, false, 165)) > 1)) {
                echo "и";
            } else {
                echo "я";
            }
            echo " с тем же адресом</h3>
        <div class=\"searchResults\">
            ";
            // line 167
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "sameAddress", [], "any", false, false, false, 167));
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
            foreach ($context['_seq'] as $context["_key"] => $context["bank"]) {
                // line 168
                echo "                ";
                echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["bank"]);
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bank'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 170
            echo "        </div>
    ";
        }
        // line 172
        echo "    ";
        // line 173
        echo "    ";
        if (((null === twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 173)) || (twig_date_converter($this->env, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateIn", [], "any", false, false, false, 173)) <= twig_date_converter($this->env, "2018-12-30")))) {
            // line 174
            echo "        <br><br><br>
        <table>
            <caption><h2 class=\"zeroMargin\">Старые наименования из DBF</h2></caption>
            <tbody>
            <tr>
                <td>Фирменное (полное официальное) наименование кредитной организации:</td><td>";
            // line 179
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 179), "names", [], "any", false, true, false, 179), "NAMEMAXB", [], "any", true, true, false, 179)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 179), "names", [], "any", false, true, false, 179), "NAMEMAXB", [], "any", false, false, false, 179), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Наименование участника расчетов для поиска в <abbr data-tooltip=\"электронной базе данных\">ЭБД</abbr>:</td><td>";
            // line 182
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 182), "names", [], "any", false, true, false, 182), "NAMEN", [], "any", true, true, false, 182)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 182), "names", [], "any", false, true, false, 182), "NAMEN", [], "any", false, false, false, 182), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Наименование в <abbr data-tooltip=\"Society for Worldwide Interbank Financial Telecommunications\">SWIFT</abbr>:</td><td>";
            // line 185
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 185), "names", [], "any", false, true, false, 185), "SWIFT_NAME", [], "any", true, true, false, 185)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 185), "names", [], "any", false, true, false, 185), "SWIFT_NAME", [], "any", false, false, false, 185), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            </tbody>
        </table>
        <table>
            <caption><h2 class=\"zeroMargin\">Старые контакты из DBF</h2></caption>
            <tbody>
            <tr>
                <td>Абонентский телеграф 1:</td><td>";
            // line 193
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 193), "contacts", [], "any", false, true, false, 193), "AT1", [], "any", true, true, false, 193)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 193), "contacts", [], "any", false, true, false, 193), "AT1", [], "any", false, false, false, 193), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Абонентский телеграф 2:</td><td>";
            // line 196
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 196), "contacts", [], "any", false, true, false, 196), "AT2", [], "any", true, true, false, 196)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 196), "contacts", [], "any", false, true, false, 196), "AT2", [], "any", false, false, false, 196), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Телефон";
            // line 199
            if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 199), "contacts", [], "any", false, false, false, 199), "TELEF", [], "any", false, false, false, 199), "phones", [], "any", false, false, false, 199)) > 1)) {
                echo "ы";
            }
            echo ":</td>
                <td>";
            // line 200
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 200), "contacts", [], "any", false, false, false, 200), "TELEF", [], "any", false, false, false, 200)) {
                // line 201
                echo "                        ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 201), "contacts", [], "any", false, false, false, 201), "TELEF", [], "any", false, false, false, 201), "phones", [], "any", false, false, false, 201));
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
                foreach ($context['_seq'] as $context["_key"] => $context["tel"]) {
                    // line 202
                    echo "                            &#128222;<a target=\"_blank\" href=\"tel://";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["tel"], "url", [], "any", false, false, false, 202), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["tel"], "phone", [], "any", false, false, false, 202), "html", null, true);
                    echo "</a>";
                    if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 202)) {
                        echo ",<br>";
                    }
                    // line 203
                    echo "                        ";
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tel'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 204
                echo "                        ";
                if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 204), "contacts", [], "any", false, false, false, 204), "TELEF", [], "any", false, false, false, 204), "dob", [], "any", false, false, false, 204)) {
                    // line 205
                    echo "                            (доб. ";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 205), "contacts", [], "any", false, false, false, 205), "TELEF", [], "any", false, false, false, 205), "dob", [], "any", false, false, false, 205), "html", null, true);
                    echo ")
                        ";
                }
                // line 207
                echo "                    ";
            }
            echo "</td>
            </tr>
            <tr>
                <td>Центр коммутации сообщений:</td><td>";
            // line 210
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 210), "contacts", [], "any", false, true, false, 210), "CKS", [], "any", true, true, false, 210)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 210), "contacts", [], "any", false, true, false, 210), "CKS", [], "any", false, false, false, 210), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            </tbody>
        </table>
        <table>
            <caption><h2 class=\"zeroMargin\">Потенциально неактуальная информация из DBF</h2></caption>
            <tbody>
            <tr>
                <td>Дата контроля:</td><td>";
            // line 218
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 218), "misc", [], "any", false, false, false, 218), "DATE_CH", [], "any", false, false, false, 218)) {
                echo "<time datetime=\"";
                echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 218), "misc", [], "any", false, false, false, 218), "DATE_CH", [], "any", false, false, false, 218), "Y-m-d"), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 218), "misc", [], "any", false, false, false, 218), "DATE_CH", [], "any", false, false, false, 218), "d/m/Y"), "html", null, true);
                echo "</time>";
            } else {
                echo "-";
            }
            echo "</td>
            </tr>
            <tr>
                <td>Внутренний код участника расчетов по <abbr data-tooltip=\"электронной базе данных\">ЭБД</abbr> Книги <abbr data-tooltip=\"государственной регистрации кредитных организаций\">ГРКО</abbr>:</td><td>";
            // line 221
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 221), "misc", [], "any", false, true, false, 221), "BVKEY", [], "any", true, true, false, 221)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 221), "misc", [], "any", false, true, false, 221), "BVKEY", [], "any", false, false, false, 221), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Внутренний код участника расчетов по <abbr data-tooltip=\"электронной базе данных\">ЭБД</abbr> Книги <abbr data-tooltip=\"государственной регистрации кредитных организаций\">ГРКО</abbr>:</td><td>";
            // line 224
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 224), "misc", [], "any", false, true, false, 224), "FVKEY", [], "any", true, true, false, 224)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 224), "misc", [], "any", false, true, false, 224), "FVKEY", [], "any", false, false, false, 224), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Срок прохождения документов:</td><td>";
            // line 227
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 227), "misc", [], "any", false, true, false, 227), "SROK", [], "any", true, true, false, 227)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 227), "misc", [], "any", false, true, false, 227), "SROK", [], "any", false, false, false, 227), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Корреспондентский счёт до нового Плана:</td><td>";
            // line 230
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 230), "misc", [], "any", false, true, false, 230), "NEWKS", [], "any", true, true, false, 230)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 230), "misc", [], "any", false, true, false, 230), "NEWKS", [], "any", false, false, false, 230), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Код <abbr data-tooltip=\"Общероссийский классификатор предприятий и организаций\">ОКПО</abbr>:</td><td>";
            // line 233
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 233), "misc", [], "any", false, true, false, 233), "OKPO", [], "any", true, true, false, 233)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 233), "misc", [], "any", false, true, false, 233), "OKPO", [], "any", false, false, false, 233), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Номер по межфилиальным оборотам:</td><td>";
            // line 236
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 236), "misc", [], "any", false, true, false, 236), "PERMFO", [], "any", true, true, false, 236)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 236), "misc", [], "any", false, true, false, 236), "PERMFO", [], "any", false, false, false, 236), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            <tr>
                <td>Уникальный код по справочнику <abbr data-tooltip=\"Банковский идентификационный код\">БИК</abbr>:</td><td>";
            // line 239
            echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 239), "misc", [], "any", false, true, false, 239), "VKEY", [], "any", true, true, false, 239)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 239), "misc", [], "any", false, true, false, 239), "VKEY", [], "any", false, false, false, 239), "-")) : ("-")), "html", null, true);
            echo "</td>
            </tr>
            </tbody>
        </table>
        ";
            // line 243
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 243), "misc", [], "any", false, false, false, 243), "RKC", [], "any", false, false, false, 243)) {
                // line 244
                echo "            <h3>Расчётно-кассовы";
                if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 244), "misc", [], "any", false, false, false, 244), "RKC", [], "any", false, false, false, 244)) > 1)) {
                    echo "е";
                } else {
                    echo "й";
                }
                echo " центр";
                if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 244), "misc", [], "any", false, false, false, 244), "RKC", [], "any", false, false, false, 244)) > 1)) {
                    echo "ы";
                }
                echo " согласно DBF</h3>
            <div class=\"searchResults bicChain\">
                ";
                // line 246
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 246), "misc", [], "any", false, false, false, 246), "RKC", [], "any", false, false, false, 246));
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
                foreach ($context['_seq'] as $context["_key"] => $context["bank"]) {
                    // line 247
                    echo "                    ";
                    echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["bank"]);
                    if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 247)) {
                        echo " >>> ";
                    }
                    // line 248
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bank'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 249
                echo "            </div>
        ";
            }
            // line 251
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 251), "successors", [], "any", false, false, false, 251)) {
                // line 252
                echo "            <h3>Преемник";
                if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 252), "successors", [], "any", false, false, false, 252)) > 1)) {
                    echo "и";
                }
                echo " согласно DBF</h3>
            <div class=\"searchResults bicChain\">
                ";
                // line 254
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 254), "successors", [], "any", false, false, false, 254));
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
                foreach ($context['_seq'] as $context["_key"] => $context["bank"]) {
                    // line 255
                    echo "                    ";
                    echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["bank"]);
                    if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 255)) {
                        echo " >>> ";
                    }
                    // line 256
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['bank'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 257
                echo "            </div>
        ";
            }
            // line 259
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 259), "predecessors", [], "any", false, false, false, 259)) {
                // line 260
                echo "            <h3>Предшественник";
                if (((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 260), "predecessors", [], "any", false, false, false, 260)) > 1) || (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 260), "predecessors", [], "any", false, false, false, 260), 0, [], "any", false, false, false, 260)) > 1))) {
                    echo "и";
                }
                echo " согласно DBF</h3>
            <div class=\"searchResults\">
                ";
                // line 262
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, false, false, 262), "predecessors", [], "any", false, false, false, 262));
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
                foreach ($context['_seq'] as $context["_key"] => $context["predecessor"]) {
                    // line 263
                    echo "                    ";
                    echo twig_include($this->env, $context, "common/elements/entitycard.twig", $context["predecessor"]);
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['predecessor'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 265
                echo "            </div>
        ";
            }
            // line 267
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DateOut", [], "any", false, false, false, 267)) {
                // line 268
                echo "            <table>
                <caption><h2 class=\"zeroMargin\">Информация о закрытии из DBF</h2></caption>
                <tbody>
                <tr>
                    <td>Причина закрытия счёта:</td><td>";
                // line 272
                echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 272), "removal", [], "any", false, true, false, 272), "R_CLOSE", [], "any", true, true, false, 272)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 272), "removal", [], "any", false, true, false, 272), "R_CLOSE", [], "any", false, false, false, 272), "-")) : ("-")), "html", null, true);
                echo "</td>
                </tr>
                <tr>
                    <td>Основание для ограничения или исключения:</td><td>";
                // line 275
                echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 275), "removal", [], "any", false, true, false, 275), "PRIM1", [], "any", true, true, false, 275)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 275), "removal", [], "any", false, true, false, 275), "PRIM1", [], "any", false, false, false, 275), "-")) : ("-")), "html", null, true);
                echo "</td>
                </tr>
                <tr>
                    <td>Основание для аннулировании в Книге <abbr data-tooltip=\"государственной регистрации кредитных организаций\">ГРКО</abbr>:</td><td>";
                // line 278
                echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 278), "removal", [], "any", false, true, false, 278), "PRIM3", [], "any", true, true, false, 278)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 278), "removal", [], "any", false, true, false, 278), "PRIM3", [], "any", false, false, false, 278), "-")) : ("-")), "html", null, true);
                echo "</td>
                </tr>
                <tr>
                    <td>Реквизиты ликвидационной комиссии:</td><td>";
                // line 281
                echo twig_escape_filter($this->env, ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 281), "removal", [], "any", false, true, false, 281), "PRIM2", [], "any", true, true, false, 281)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["bicdetails"] ?? null), "DBF", [], "any", false, true, false, 281), "removal", [], "any", false, true, false, 281), "PRIM2", [], "any", false, false, false, 281), "-")) : ("-")), "html", null, true);
                echo "</td>
                </tr>
                </tbody>
            </table>
        ";
            }
            // line 286
            echo "    ";
        }
        // line 287
        echo "</section>
";
    }

    public function getTemplateName()
    {
        return "bictracker/bic.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1147 => 287,  1144 => 286,  1136 => 281,  1130 => 278,  1124 => 275,  1118 => 272,  1112 => 268,  1109 => 267,  1105 => 265,  1088 => 263,  1071 => 262,  1063 => 260,  1060 => 259,  1056 => 257,  1042 => 256,  1036 => 255,  1019 => 254,  1011 => 252,  1008 => 251,  1004 => 249,  990 => 248,  984 => 247,  967 => 246,  953 => 244,  951 => 243,  944 => 239,  938 => 236,  932 => 233,  926 => 230,  920 => 227,  914 => 224,  908 => 221,  894 => 218,  883 => 210,  876 => 207,  870 => 205,  867 => 204,  853 => 203,  844 => 202,  826 => 201,  824 => 200,  818 => 199,  812 => 196,  806 => 193,  795 => 185,  789 => 182,  783 => 179,  776 => 174,  773 => 173,  771 => 172,  767 => 170,  750 => 168,  733 => 167,  723 => 165,  720 => 164,  716 => 162,  699 => 160,  682 => 159,  672 => 157,  669 => 156,  665 => 154,  651 => 153,  645 => 151,  642 => 150,  625 => 149,  621 => 147,  607 => 146,  601 => 144,  598 => 143,  581 => 142,  565 => 140,  562 => 139,  558 => 137,  544 => 136,  538 => 135,  521 => 134,  505 => 132,  502 => 131,  497 => 130,  492 => 128,  489 => 127,  487 => 126,  472 => 122,  454 => 119,  448 => 116,  442 => 113,  436 => 110,  430 => 107,  420 => 104,  364 => 96,  358 => 93,  352 => 90,  346 => 87,  340 => 83,  335 => 80,  329 => 79,  291 => 76,  288 => 75,  285 => 74,  280 => 73,  274 => 72,  240 => 69,  237 => 68,  234 => 67,  230 => 66,  222 => 60,  219 => 59,  214 => 56,  208 => 55,  182 => 52,  179 => 51,  176 => 50,  171 => 49,  165 => 48,  143 => 45,  140 => 44,  137 => 43,  133 => 42,  125 => 36,  123 => 35,  119 => 33,  114 => 30,  106 => 28,  103 => 27,  95 => 25,  93 => 24,  89 => 23,  85 => 21,  83 => 20,  78 => 18,  68 => 15,  62 => 12,  56 => 9,  50 => 5,  42 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "bictracker/bic.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\bictracker\\bic.twig");
    }
}
