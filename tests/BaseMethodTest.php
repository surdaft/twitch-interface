<?php

use PHPUnit\Framework\TestCase;

use Twitch\BaseMethod;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class BaseMethodTest extends TestCase
{
    public function setUp()
    {
        \Twitch\Twitch::setClientId('test');
    }

    public function getClient($mock_responses = [])
    {
        return new Client([
            'headers' => [
                'Client-ID' => 'test'
            ],
            'base_uri' => 'https://api.twitch.tv/kraken/',
            'handler' => new MockHandler($mock_responses)
        ]);
    }

    /**
     * @test
     */
    public function test_default_verb()
    {
        $base_method = new BaseMethod($this->getClient());
        $this->assertEquals('GET', $base_method->verb());
    }

    /**
     * @test
     */
    public function test_recieving_expected_response_decoded()
    {
        $test_file = file_get_contents(__DIR__ . '/data/channel.json');

        $base_method = new BaseMethod($this->getClient([
            new Response(200, [], $test_file)
        ]));

        $this->assertEquals(json_decode($test_file), $base_method->send());
    }
}
