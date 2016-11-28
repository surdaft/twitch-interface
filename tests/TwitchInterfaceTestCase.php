<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use Twitch\Twitch;
use Twitch\Scope;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class TwitchInterfaceTestCase extends TestCase
{

    public function setUp()
    {
        Twitch::setClientId('test');

        // sets a fresh scope
        Twitch::$scope = new Scope;
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

    public function setAccessToken()
    {
        Twitch::setAccessToken('test_access_token', $this->getClient([
            new Response(200, [], file_get_contents(__DIR__ . '/data/root.json'))
        ]));

        Twitch::$scope = new Scope;
    }
}
