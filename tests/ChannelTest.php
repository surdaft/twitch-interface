<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Twitch\Channel;

class ChannelTest extends TestCase
{
    protected $base_uri = 'https://api.twitch.tv/kraken/';
    
    public function setUp()
    {
        \Twitch\Twitch::setClientId('test');
        
        // sets a scope
        \Twitch\Twitch::setAccessToken('test_access_token', $this->getClient([
            new Response(200, [], file_get_contents('data/root.json'))
        ]));
    }

    private function getClient($mock_responses = [])
    {
        return new Client([
            'headers' => [
                'Client-ID' => 'test'
            ],
            'base_uri' => $this->base_uri,
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
    
    /**
     * @test
     */
    public function setting_status_or_game_sets_endpoint()
    {
        $channel = new Channel('surdaft', $this->getClient());
        
        // editors changes the endpoint
        $channel->editors()->status('test');
        
        $this->assertEquals($this->base_uri, $channel->endpoint());
    }
    
    /**
     * @test
     */
    public function reset_stream_key_updates_verb()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->resetStreamKey();
        
        $this->assertEquals('DELETE', $channel->verb());
    }
    
    /**
     * @test
     */
    public function check_videos_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->videos();
        
        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals($this->base_uri . '/videos', $channel->endpoint());
    }
    
    /**
     * @test
     */
    public function check_followers_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->followers();
        
        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals($this->base_uri . '/followers', $channel->endpoint());
    }
    
    /**
     * @test
     */
    public function check_editors_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->editors();
        
        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals($this->base_uri . '/editors', $channel->endpoint());
    }
    
    /**
     * @test
     */
    public function check_teams_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->teams();
        
        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals($this->base_uri . '/teams', $channel->endpoint());
    }
    
    /**
     * @test
     * @expectedException ScopeException
     */
    public function check_commercial_scope_does_not_exist()
    {
        $channel = new Channel('surdaft', $this->getClient());
        $channel->commercial();
        
    }
    
    /**
     * @test
     */
    public function check_commercial_verb_and_endpoint()
    {
        
        \Twitch\Twitch::setAccessToken('test_token', $this->getClient([
            new Response(200, [], file_get_contents('data/root.json'))
        ]));
        
        $channel = new Channel('surdaft', $this->getClient());
        $channel->commercial();
        
        $this->assertEquals('POST', $channel->endpoint());
        $this->assertEquals($this->base_uri . '/commercial', $channel->endpoint());
    }
    
    /**
     * @test
     */
    public function check_commercial_lengths()
    {
        $channel = new Channel('surdaft', $this->getClient());
        
        try {
            $channel->commercial(98);
        } catch (\Exception $e) {
            $this->assertEquals('Unsupported commercial length.', $e->getMessage());
        }
    }
}
