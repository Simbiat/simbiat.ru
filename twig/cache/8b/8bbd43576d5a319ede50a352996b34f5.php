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

/* common/layout/metatags.twig */
class __TwigTemplate_72b0574a835ea82a8d4f548ee07bf43e extends Template
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
        echo "<meta charset=\"UTF-8\" />
<meta name=\"Content-Type\" content=\"text/html; charset=UTF-8\" />

<!-- General Meta -->
<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />
<meta name=\"description\" content=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["ogdesc"] ?? null), "html", null, true);
        echo "\" />
<meta name=\"theme-color\" content=\"#2e293d\">

<!-- Author Meta -->
<meta name=\"author\" content=\"Dmitry Kustov\" />
<link rel=\"author\" href=\"https://www.facebook.com/simbiat19\"/>

<!-- Google Meta -->
<meta itemprop=\"name\" content=\"";
        // line 14
        if (($context["title"] ?? null)) {
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            if ( !array_key_exists("http_error", $context)) {
                echo " on ";
                echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
            }
        } else {
            echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        }
        echo "\">
<meta itemprop=\"description\" content=\"";
        // line 15
        echo twig_escape_filter($this->env, ($context["ogdesc"] ?? null), "html", null, true);
        echo "\" />
<meta itemprop=\"image\" content=\"";
        // line 16
        echo twig_escape_filter($this->env, ($context["ogimage"] ?? null), "html", null, true);
        echo "\">

<meta name=\"X-CSRF-Token\" content=\"";
        // line 18
        echo twig_escape_filter($this->env, ($context["XCSRFToken"] ?? null), "html", null, true);
        echo "\" />
<!-- Custom meta tag to indicate whether Save-Data header was received -->
<meta name=\"save_data\" content=\"";
        // line 20
        echo twig_escape_filter($this->env, ($context["save_data"] ?? null), "html", null, true);
        echo "\" />

<!-- FaceBook IDs -->
<meta property=\"fb:app_id\" content=\"";
        // line 23
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["facebook"] ?? null), "appid", [], "any", false, false, false, 23), "html", null, true);
        echo "\"/>
<meta property=\"fb:admins\" content=\"";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["facebook"] ?? null), "adminid", [], "any", false, false, false, 24), "html", null, true);
        echo "\"/>

<!-- Open Graph Protocol -->
<meta property=\"og:site_name\" content=\"";
        // line 27
        echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo "\" />
<meta property=\"og:title\" content=\"";
        // line 28
        if (($context["title"] ?? null)) {
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            if ( !array_key_exists("http_error", $context)) {
                echo " on ";
                echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
            }
        } else {
            echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        }
        echo "\" />
<meta property=\"og:description\" content=\"";
        // line 29
        echo twig_escape_filter($this->env, ($context["ogdesc"] ?? null), "html", null, true);
        echo "\" />
<meta property=\"og:url\" content=\"";
        // line 30
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "\" />
<meta property=\"og:image\" content=\"";
        // line 31
        echo twig_escape_filter($this->env, ($context["ogimage"] ?? null), "html", null, true);
        echo "\" />
<meta property=\"og:image:type\" content=\"image/png\" />
<meta property=\"og:image:width\" content=\"1200\" />
<meta property=\"og:image:height\" content=\"630\" />
";
        // line 35
        if ( !($context["ogtype"] ?? null)) {
            // line 36
            echo "    <meta property=\"og:type\" content=\"website\" />
";
        } else {
            // line 38
            echo "    <meta property=\"og:type\" content=\"";
            echo twig_escape_filter($this->env, ($context["ogtype"] ?? null), "html", null, true);
            echo "\" />
";
        }
        // line 40
        echo ($context["ogextra"] ?? null);
        echo "

<!-- Twitter Card -->
<meta name=\"twitter:card\" content=\"summary\" />
<meta name=\"twitter:title\" content=\"";
        // line 44
        if (($context["title"] ?? null)) {
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
            if ( !array_key_exists("http_error", $context)) {
                echo " on ";
                echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
            }
        } else {
            echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        }
        echo "\" />
<meta name=\"twitter:site\" content=\"@";
        // line 45
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["twitter_card"] ?? null), "name", [], "any", false, false, false, 45), "html", null, true);
        echo "\" />
<meta name=\"twitter:site:id\" content=\"";
        // line 46
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["twitter_card"] ?? null), "id", [], "any", false, false, false, 46), "html", null, true);
        echo "\" />
<meta name=\"twitter:creator\" content=\"@";
        // line 47
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["twitter_card"] ?? null), "name", [], "any", false, false, false, 47), "html", null, true);
        echo "\" />
<meta name=\"twitter:creator:id\" content=\"";
        // line 48
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["twitter_card"] ?? null), "id", [], "any", false, false, false, 48), "html", null, true);
        echo "\" />
<meta name=\"twitter:description\" content=\"";
        // line 49
        echo twig_escape_filter($this->env, ($context["ogdesc"] ?? null), "html", null, true);
        echo "\" />
";
        // line 50
        if (($context["favicon"] ?? null)) {
            // line 51
            echo "    <meta name=\"twitter:image\" content=\"";
            echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
            echo twig_escape_filter($this->env, ($context["favicon"] ?? null), "html", null, true);
            echo "\" />
";
        } else {
            // line 53
            echo "    <meta name=\"twitter:image\" content=\"";
            echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
            echo "/img/favicons/simbiat.png\" />
";
        }
        // line 55
        echo "<meta name=\"twitter:image:alt\" content=\"";
        echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo " logo\" />

<!-- MS Tiles -->
<meta name=\"application-name\" content=\"";
        // line 58
        echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo "\" />
<meta name=\"msapplication-navbutton-color\" content=\"#000000\" />
<meta name=\"msapplication-TileColor\" content=\"#2e293d\" />
<meta name=\"msapplication-square70x70logo\" content=\"";
        // line 61
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/mstile/70x70.png\" />
<meta name=\"msapplication-square150x150logo\" content=\"";
        // line 62
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/mstile/150x150.png\" />
<meta name=\"msapplication-square310x310logo\" content=\"";
        // line 63
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/mstile/310x310.png\" />
<meta name=\"msapplication-wide310x150logo\" content=\"";
        // line 64
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/mstile/310x150.png\" />
<meta name=\"msapplication-TileImage\" content=\"";
        // line 65
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "/img/favicons/mstile/144x144.png\" />
<meta name=\"msapplication-tooltip\" content=\"Simbiat Software\" />
<meta name=\"msapplication-starturl\" content=\"";
        // line 67
        echo twig_escape_filter($this->env, ($context["domain"] ?? null), "html", null, true);
        echo "\" />
<meta name=\"msapplication-window\" content=\"width=800;height=600\" />
<meta name=\"msapplication-allowDomainApiCalls\" content=\"true\" />
<meta name=\"msapplication-allowDomainMetaTags\" content=\"true\" />
<meta name=\"msapplication-config\" content=\"/browserconfig.xml\" />
<meta name=\"msapplication-tap-highlight\" content=\"no\" />

<!-- Web Apps -->
<meta name=\"mobile-web-app-capable\" content=\"yes\" />
<meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />
<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black-translucent\">
<meta name=\"apple-mobile-web-app-title\" content=\"";
        // line 78
        echo twig_escape_filter($this->env, ($context["site_name"] ?? null), "html", null, true);
        echo "\" />

<!-- Preloads -->
<link href=\"/img/logo.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">
<link href=\"/img/share.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">
<link href=\"/img/navigation/home.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">
<link href=\"/img/navigation/blog.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">
<link href=\"/img/navigation/fftracker.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">
<link href=\"/img/navigation/bictracker.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">
<link href=\"/img/navigation/about.svg\" rel=\"preload\" type=\"image/svg+xml\" as=\"image\">

<!-- Favicons -->
";
        // line 90
        if (($context["favicon"] ?? null)) {
            // line 91
            echo "    <link href=\"";
            echo twig_escape_filter($this->env, ($context["favicon"] ?? null), "html", null, true);
            echo "\" rel=\"icon\" type=\"image/png\">
";
        } else {
            // line 93
            echo "    <link href=\"/img/logo.svg\" title=\"SVG\" rel=\"icon\" type=\"image/svg+xml\">
    <link href=\"/img/favicons/favicon.ico\" title=\"ICON\" rel=\"icon\" type=\"image/vnd.microsoft.icon\" sizes=\"16x16 32x32 48x48\">
    <link href=\"/img/favicons/favicon-16x16.png\" title=\"16x16\" rel=\"icon\" type=\"image/png\" sizes=\"16x16\">
    <link href=\"/img/favicons/favicon-32x32.png\" title=\"32x32\" rel=\"icon\" type=\"image/png\" sizes=\"32x32\">
    <link href=\"/img/favicons/android/36x36.png\" title=\"36x36\" rel=\"icon\" type=\"image/png\" sizes=\"36x36\">
    <link href=\"/img/favicons/android/48x48.png\" title=\"48x48\" rel=\"icon\" type=\"image/png\" sizes=\"48x48\">
    <link href=\"/img/favicons/android/72x72.png\" title=\"72x72\" rel=\"icon\" type=\"image/png\" sizes=\"72x72\">
    <link href=\"/img/favicons/android/96x96.png\" title=\"96x96\" rel=\"icon\" type=\"image/png\" sizes=\"96x96\">
    <link href=\"/img/favicons/android/144x144.png\" title=\"144x144\" rel=\"icon\" type=\"image/png\" sizes=\"144x144\">
    <link href=\"/img/favicons/android/192x192.png\" title=\"192x192\" rel=\"icon\" type=\"image/png\" sizes=\"192x192\">
    <link href=\"/img/favicons/android/384x384.png\" title=\"384x384\" rel=\"icon\" type=\"image/png\" sizes=\"384x384\">
    <link href=\"/img/favicons/android/512x512.png\" title=\"512x512\" rel=\"icon\" type=\"image/png\" sizes=\"512x512\">
    <link href=\"/img/favicons/apple/57x57.png\" title=\"57x57\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"57x57\">
    <link href=\"/img/favicons/apple/60x60.png\" title=\"60x60\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"60x60\">
    <link href=\"/img/favicons/apple/72x72.png\" title=\"72x72\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"72x72\">
    <link href=\"/img/favicons/apple/76x76.png\" title=\"76x76\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"76x76\">
    <link href=\"/img/favicons/apple/114x114.png\" title=\"114x114\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"114x114\">
    <link href=\"/img/favicons/apple/120x120.png\" title=\"120x120\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"120x120\">
    <link href=\"/img/favicons/apple/144x144.png\" title=\"144x144\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"144x144\">
    <link href=\"/img/favicons/apple/152x152.png\" title=\"152x152\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"152x152\">
    <link href=\"/img/favicons/apple/180x180.png\" title=\"180x180\" rel=\"apple-touch-icon\" type=\"image/png\" sizes=\"180x180\">
    <link href=\"/img/favicons/safari-pinned-tab.svg\" title=\"Safari\" rel=\"mask-icon\" type=\"image/svg+xml\" color=\"#000000\">
";
        }
        // line 116
        echo "
<!-- Manifest -->
<link href=\"/manifest.webmanifest\" rel=\"manifest\" type=\"application/manifest+json\">
";
    }

    public function getTemplateName()
    {
        return "common/layout/metatags.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  290 => 116,  265 => 93,  259 => 91,  257 => 90,  242 => 78,  228 => 67,  223 => 65,  219 => 64,  215 => 63,  211 => 62,  207 => 61,  201 => 58,  194 => 55,  188 => 53,  181 => 51,  179 => 50,  175 => 49,  171 => 48,  167 => 47,  163 => 46,  159 => 45,  147 => 44,  140 => 40,  134 => 38,  130 => 36,  128 => 35,  121 => 31,  117 => 30,  113 => 29,  101 => 28,  97 => 27,  91 => 24,  87 => 23,  81 => 20,  76 => 18,  71 => 16,  67 => 15,  55 => 14,  44 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "common/layout/metatags.twig", "C:\\Users\\simbi\\OneDrive\\Documents\\!Personal\\Coding\\WebServer\\htdocs\\twig\\common\\layout\\metatags.twig");
    }
}
