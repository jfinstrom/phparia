<?php
namespace phparia\Tests\Functional;

use phparia\Client\Phparia;
use \PHPUnit_Framework_TestCase;
use Symfony\Component\Yaml\Yaml;
use Psr\Log\LoggerInterface;
use Psr\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


abstract class PhpariaTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phparia
     */
    protected $client;

    /**
     * @var string
     */
    protected $ariAddress;

    /**
     * @var string
     */
    protected $amiAddress;

    /**
     * @var string
     */
    protected $dialString;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function setUp()
    {
        parent::setUp();

        $configFile = __DIR__.'/../../config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $this->ariAddress = $value['tests']['ari_address'];
        $this->amiAddress = $value['tests']['ami_address'];
        $this->dialString = $value['tests']['dial_string'];

        $this->logger = new Logger();
        $this->logger->pushHandler(new StreamHandler('php://output', Logger::NOTICE))

        $this->client = new Phparia($this->logger);
        $this->client->connect($this->ariAddress, $this->amiAddress);
    }

    public function tearDown()
    {
        $channels = $this->client->channels()->getChannels();
        foreach ($channels as $channel) {
            $channel->hangup();
        }

        parent::tearDown();
    }
}
