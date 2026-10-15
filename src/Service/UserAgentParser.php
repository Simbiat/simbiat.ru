<?php

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

/**
 * Minimized DeviceDetector logic to help parse
 */
final class UserAgentParser
{

    private AbstractParser $bot_parser;
    private AbstractParser $os_parser;
    private array $client_parsers;

    /**
     * @param bool $use_hints Whether to use Client Hints. Needed when parsing a live session only.
     */
    public function __construct(bool $use_hints = false)
    {
        // Force full string versions in Matomo Device Detector parsers
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        $client_hints = null;
        if ($use_hints) {
            $client_hints = ClientHints::factory($_SERVER);
        }
        $this->bot_parser = $this->createParser(Bot::class, $client_hints);
        $this->os_parser = $this->createParser(OperatingSystem::class, $client_hints);
        // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys -- needs to be in certain order due to how Detector works
        $this->client_parsers = [
            'feed' => $this->createParser(FeedReader::class, $client_hints),
            'app' => $this->createParser(MobileApp::class, $client_hints),
            'player' => $this->createParser(MediaPlayer::class, $client_hints),
            'pim' => $this->createParser(PIM::class, $client_hints),
            'browser' => $this->createParser(Browser::class, $client_hints),
            'library' => $this->createParser(Library::class, $client_hints),
        ];
    }

    /**
     * Helper to create parser objects
     * @param string                           $class_name   Fully qualified class name for the object to create
     * @param \DeviceDetector\ClientHints|null $client_hints Optional client hints object
     *
     * @return \DeviceDetector\Parser\AbstractParser
     */
    private function createParser(string $class_name, ?ClientHints $client_hints): AbstractParser
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

    /**
     * Get Operating System from User Agent
     * @param string $user_agent User Agent to parse
     *
     * @return array|null
     */
    public function getOs(string $user_agent): array|null
    {
        $this->os_parser->setUserAgent($user_agent);

        return $this->os_parser->parse();
    }

    /**
     * Get bot from User Agent
     * @param string $user_agent User Agent to parse
     *
     * @return array|null
     */
    public function getBot(string $user_agent): array|null
    {
        $this->bot_parser->setUserAgent($user_agent);

        return $this->bot_parser->parse();
    }

    /**
     * Get bot from User Agent
     * @param string $user_agent User Agent to parse
     *
     * @return array|null
     */
    public function getClient(string $user_agent): array|null
    {
        foreach ($this->client_parsers as $parser) {
            $parser->setUserAgent($user_agent);
            $client = $parser->parse();
            if (!empty($client)) {
                return $client;
            }
        }

        return null;
    }
}
