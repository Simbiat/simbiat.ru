<?php
declare(strict_types = 1);

namespace Simbiat\Website\Twig;

use Simbiat\Database\Query;
use Simbiat\Talks\Enums\SystemUsers;
use Simbiat\Website\Config;
use Simbiat\Website\HomePage;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * Class to implement some extension stuff for Twig
 */
final class Extension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    #[\Override]
    public function getFunctions(): array
    {
        $runtime = new RuntimeExtension();
        return [
            new TwigFunction('genBread', [$runtime, 'genBread'], ['is_safe' => ['html']]),
            new TwigFunction('timeline', [$runtime, 'timeline'], ['is_safe' => ['html']]),
            new TwigFunction('pagination', [$runtime, 'pagination'], ['is_safe' => ['html']]),
            new TwigFunction('basename', [$runtime, 'basename']),
            new TwigFunction('prettyURL', [$runtime, 'prettyURL']),
            new TwigFunction('is_numeric', [$runtime, 'is_numeric']),
            new TwigFunction('nl2p', [$runtime, 'nl2p'], ['is_safe' => ['html']]),
            new TwigFunction('changelog', [$runtime, 'changelog'], ['is_safe' => ['html']]),
            new TwigFunction('preg_replace', [$runtime, 'preg_replace'], ['is_safe' => ['html']]),
            new TwigFunction('sanitize', [$runtime, 'sanitize'], ['is_safe' => ['html']]),
            new TwigFunction('htmlCut', [$runtime, 'htmlCut'], ['is_safe' => ['html']]),
            new TwigFunction('timeTag', [$runtime, 'timeTag'], ['is_safe' => ['html']]),
            new TwigFunction('tooltip', [$runtime, 'tooltip'], ['is_safe' => ['html']]),
            new TwigFunction('linkTags', [$runtime, 'linkTags'], ['is_safe' => ['html']]),
            new TwigFunction('cuteBytes', [$runtime, 'cuteBytes'], ['is_safe' => ['html']]),
            new TwigFunction('uploadedLink', [$runtime, 'uploadedLink'], ['is_safe' => ['html']]),
        ];
    }
    
    /**
     * @return array<string, mixed>
     */
    public function getGlobals(): array
    {
        $defaults = [
            'site_name' => Config::SITE_NAME,
            'domain' => Config::$base_url,
            'canonical' => Config::$canonical,
            'url' => mb_rtrim(Config::$base_url.($_SERVER['REQUEST_URI'] ?? ''), '/', 'UTF-8'),
            'maintenance' => 1,
            'registration' => 0,
        ];
        if (Config::$dbup) {
            #Update default variables with values from database
            try {
                $defaults = \array_merge($defaults, Query::query('SELECT `setting`, `value` FROM `sys__settings`', return: 'pair'));
            } catch (\Throwable) {
                #Do nothing, retain defaults
            }
        }
        #Flag for Save-Data header
        if (\preg_match('/^on$/i', $_SERVER['HTTP_SAVE_DATA'] ?? '') === 1) {
            $save_data = 'true';
        } else {
            $save_data = 'false';
        }
        return \array_merge($defaults, [
            #PROD flag
            'is_prod' => Config::$prod,
            #List of LINK tags
            'link_tags' => Config::$links,
            #Time used as a version of the JS file for cache busting
            'js_version' => \filemtime(Config::$js_dir.'/app.js'),
            #Save data flag
            'save_data' => $save_data,
            'unsupported' => false,
            #Flag whether GET is present
            'has_get' => \count($_GET) !== 0,
            'http_method' => HomePage::$method,
            #System users' IDs
            'system_users' => SystemUsers::getSystemUsers(),
        ]);
    }
}
