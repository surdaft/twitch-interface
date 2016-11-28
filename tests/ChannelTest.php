<?php

use Twitch\Twitch;
use Twitch\Channel;

use Tests\TwitchInterfaceTestCase;

use GuzzleHttp\Psr7\Response;

class ChannelTest extends TwitchInterfaceTestCase
{
    /**
     * @test
     * @expectedException Twitch\Exceptions\ChannelException
     */
    public function without_channel_requires_access_token()
    {
        $channel = new Channel();
    }

    // test endpoints requiring a scope

    /**
     * @test
     * @expectedException Twitch\Exceptions\TwitchScopeException
     */
    public function without_channel_requires_channel_read()
    {
        $this->setAccessToken();
        $channel = new Channel();
    }

    /**
     * @test
     * @expectedException Twitch\Exceptions\TwitchScopeException
     */
    public function status_requires_scope_channel_editor()
    {
       $channel = new Channel('surdaft');
       $channel->status('test');
    }

    /**
     * @test
     */
    public function status_has_required_scope()
    {
        Twitch::$scope->addScope('channel_editor'); // replicates adding an access token with this scope.

        $channel = new Channel('surdaft');
        $channel->status('test');
    }

    /**
     * @test
     * @expectedException Twitch\Exceptions\TwitchScopeException
     */
    public function game_requires_scope_channel_editor()
    {
       $channel = new Channel('surdaft');
       $channel->game('Overwatch');
    }

    /**
     * @test
     */
    public function game_has_required_scope()
    {
        Twitch::$scope->addScope('channel_editor');

        $channel = new Channel('surdaft');
        $channel->game('Overwatch');
    }

    /**
     * @test
     * @expectedException Twitch\Exceptions\TwitchScopeException
     */
    public function reset_stream_key_requires_scope()
    {
        $channel = new Channel('surdaft');
        $channel->resetStreamKey();
    }

    /**
     * @test
     */
    public function reset_stream_has_required_token()
    {
        Twitch::$scope->addScope('channel_stream');

        $channel = new Channel('surdaft');
        $channel->resetStreamKey();
    }

    /**
     * @test
     * @expectedException Twitch\Exceptions\TwitchScopeException
     */
    public function stream_delay_requires_scope()
    {
        $channel = new Channel('surdaft');
        $channel->streamDelay(10);
    }

    /**
     * @test
     */
    public function stream_delay_has_required_Scope()
    {
        Twitch::$scope->addScope('channel_editor');

        $channel = new Channel('surdaft');
        $channel->streamDelay(10);
    }

    // end of scope tests

    /**
     * @test
     */
    public function setting_game_modifies_body()
    {
        Twitch::$scope->addScope('channel_editor');
        $new_game = 'Overwatch';

        $channel = new Channel('surdaft');
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
        Twitch::$scope->addScope('channel_editor');
        $new_status = 'This is a status';

        $channel = new Channel('surdaft');
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
        Twitch::$scope->addScope('channel_editor');
        $new_status = 'This is a status';
        $new_game = 'Overwatch';

        $channel = new Channel('surdaft');
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
        Twitch::$scope->addScope('channel_editor');

        $new_status = 'test';
        $new_game = 'Overwatch';
        $expected_verb = 'PUT';

        $channel = new Channel('surdaft');
        $channel->status($new_status);

        $another_channel = new Channel('surdaft');
        $another_channel->game($new_game);

        $this->assertEquals($expected_verb, $channel->verb());
        $this->assertEquals($expected_verb, $another_channel->verb());
    }

    /**
     * @test
     */
    public function setting_status_or_game_sets_endpoint()
    {
        Twitch::$scope->addScopes(['channel_editor', 'channel_read']);
        $channel_user = 'surdaft';

        $channel = new Channel($channel_user, $this->getClient());

        // editors changes the endpoint
        $channel->editors()->status('test');

        $this->assertEquals('channels/' . $channel_user, $channel->endpoint());
    }

    /**
     * @test
     */
    public function reset_stream_key_updates_verb()
    {
        Twitch::$scope->addScope('channel_stream');

        $channel = new Channel('surdaft');
        $channel->resetStreamKey();

        $this->assertEquals('DELETE', $channel->verb());
    }

    /**
     * @test
     */
    public function check_videos_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft');
        $channel->videos();

        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals('channels/surdaft/videos', $channel->endpoint());
    }

    /**
     * @test
     */
    public function check_followers_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft');
        $channel->followers();

        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals('channels/surdaft/follows', $channel->endpoint());
    }

    /**
     * @test
     */
    public function check_editors_method_verb_and_endpoint()
    {
        \Twitch\Twitch::$scope->addScope('channel_read');

        $channel = new Channel('surdaft');
        $channel->editors();

        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals('channels/surdaft/editors', $channel->endpoint());
    }

    /**
     * @test
     */
    public function check_teams_method_verb_and_endpoint()
    {
        $channel = new Channel('surdaft');
        $channel->teams();

        $this->assertEquals('GET', $channel->verb());
        $this->assertEquals('channels/surdaft/teams', $channel->endpoint());
    }

    /**
     * @test
     */
    public function check_commercial_verb_and_endpoint()
    {
        Twitch::$scope->addScope('channel_commercial');

        $channel = new Channel('surdaft');
        $channel->commercial();

        $this->assertEquals('POST', $channel->verb());
        $this->assertEquals('channels/surdaft/commercial', $channel->endpoint());
    }

    /**
     * @test
     * @expectedException Twitch\Exceptions\ChannelException
     */
    public function check_commercial_invalid_length()
    {
        Twitch::$scope->addScope('channel_commercial');

        $channel = new Channel('surdaft');
        $channel->commercial(98);
    }

    /**
     * @test
     */
    public function check_commercial_valid_length()
    {
        Twitch::$scope->addScope('channel_commercial');

        $channel = new Channel('surdaft');
        $channel->commercial(30);
    }

    /**
     * @test
     */
    public function feed_returns_feed_instance()
    {
        $channel = new Channel('surdaft');
        $feed = $channel->feed();

        $this->assertTrue(is_a($feed, 'Twitch\\ChannelFeed'));
    }

    /**
     * @test
     */
    public function emoticons_returns_chat_instance()
    {
        $channel = new Channel('surdaft');
        $emoticons = $channel->emoticons();

        $this->assertTrue(is_a($emoticons, 'Twitch\\Chat'));
    }
}
