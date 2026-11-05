<?php

declare(strict_types=1);

namespace App\Service;

use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\ClientHints;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Bot;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Client\FeedReader;
use DeviceDetector\Parser\Client\Library;
use DeviceDetector\Parser\Client\MediaPlayer;
use DeviceDetector\Parser\Client\MobileApp;
use DeviceDetector\Parser\Client\PIM;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\OperatingSystem;
use DeviceDetector\Yaml\Pecl;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * Minimized DeviceDetector logic to help parse.
 */
final readonly class UserAgentParser
{
    private AbstractParser $bot_parser;

    private array $client_parsers;

    private AbstractParser $os_parser;

    /**
     * @param bool $use_hints Whether to use Client Hints. Needed when parsing a live session only.
     *
     * @throws CacheException|\InvalidArgumentException
     */
    public function __construct(bool $use_hints = false)
    {
        // Force full string versions in Matomo Device Detector parsers
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        $client_hints = null;
        if ($use_hints) {
            $client_hints = ClientHints::factory($_SERVER);
        }

        $this->bot_parser = self::createParser(Bot::class, $client_hints);
        $this->os_parser = self::createParser(OperatingSystem::class, $client_hints);
        // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys -- needs to be in certain order due to how Detector works
        $this->client_parsers = [
            'feed' => self::createParser(FeedReader::class, $client_hints),
            'app' => self::createParser(MobileApp::class, $client_hints),
            'player' => self::createParser(MediaPlayer::class, $client_hints),
            'pim' => self::createParser(PIM::class, $client_hints),
            'browser' => self::createParser(Browser::class, $client_hints),
            'library' => self::createParser(Library::class, $client_hints),
        ];
    }

    /**
     * Get bot from User Agent.
     *
     * @param string $user_agent User Agent to parse
     *
     * @return array|null
     */
    public function getBot(string $user_agent): ?array
    {
        $this->bot_parser->setUserAgent($user_agent);

        return $this->bot_parser->parse();
    }

    /**
     * Get bot from User Agent.
     *
     * @param string $user_agent User Agent to parse
     *
     * @return array|null
     */
    public function getClient(string $user_agent): ?array
    {
        foreach ($this->client_parsers as $client_parser) {
            $client_parser->setUserAgent($user_agent);
            $client = $client_parser->parse();
            if (!empty($client)) {
                return $client;
            }
        }

        return null;
    }

    /**
     * Get Operating System from User Agent.
     *
     * @param string $user_agent User Agent to parse
     *
     * @return array|null
     */
    public function getOs(string $user_agent): ?array
    {
        $this->os_parser->setUserAgent($user_agent);

        return $this->os_parser->parse();
    }

    /**
     * Helper to create parser objects.
     *
     * @param string           $class_name   Fully qualified class name for the object to create
     * @param ClientHints|null $client_hints Optional client hints object
     *
     * @return AbstractParser
     *
     * @throws CacheException|\InvalidArgumentException
     */
    private static function createParser(string $class_name, ?ClientHints $client_hints): AbstractParser
    {
        if (!\class_exists($class_name)) {
            throw new \InvalidArgumentException('Class `'.$class_name.'` does not exist.');
        }

        if (!\is_subclass_of($class_name, AbstractParser::class)) {
            throw new \InvalidArgumentException('Class `'.$class_name.'` is not an instance of `'.AbstractParser::class.'`.');
        }

        $parser = new $class_name(clientHints: $client_hints);
        $parser->setYamlParser(new Pecl());
        $parser->setCache(new PSR6Bridge(new ApcuAdapter('Matomo')));

        return $parser;
    }
}
