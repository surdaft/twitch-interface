<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Twitch\Channel;

class ChannelTest extends TestCase
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
    public function channel_modifies_default_body()
    {
        $channel = new Channel('surdaft', $this->getClient());

        $this->assertEquals((object) [
            'channel' => (object) []
        ], $channel->body());
    }

    /**
     * @test
     */
    public function setting_game_modifies_body()
    {
        $new_game = 'Overwatch';

        $channel = new Channel('surdaft', $this->getClient());
        $channel->game($new_game);

        $this->assertEquals((object) [
            'channel' => (object) [
                'game' => $new_game
            ]
        ], $channel->body());
    }

    /**
     * @test
     */
    public function setting_status_modifies_body()
    {
        $new_status = 'This is a status';

        $channel = new Channel('surdaft', $this->getClient());
        $channel->status($new_status);

        $this->assertEquals((object) [
            'channel' => (object) [
                'status' => $new_status
            ]
        ], $channel->body());
    }

    /**
     * @test
     */
    public function setting_game_and_status_modifies_body_correctly()
    {
        $new_status = 'This is a status';
        $new_game = 'Overwatch';

        $channel = new Channel('surdaft', $this->getClient());
        $channel->status($new_status);
        $channel->game($new_game);

        $this->assertEquals((object) [
            'channel' => (object) [
                'status' => $new_status,
                'game' => $new_game
            ]
        ], $channel->body());
    }

    /**
     * @test
     */
    public function setting_status_or_game_updates_verb()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->status('test');

        $this->assertEquals('PUT', $channel->verb());

        $another_channel = new Channel('surdaft', $this->getClient());
        $another_channel->game('Test');

        $this->assertEquals('PUT', $another_channel->verb());
    }
}
