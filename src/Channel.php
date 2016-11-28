<?php

namespace Twitch;

use Twitch\BaseMethod;
use Twitch\Exceptions\ChannelException;
use Twitch\Traits\CallStatically;
use Twitch\Exceptions\TwitchScopeException;

/**
 * Channel
 * @link https://dev.twitch.tv/docs/api/v3/channels
 */
class Channel extends BaseMethod
{
    use CallStatically;

    /**
     * Providing no channel falls back to the /channel endpoint
     *
     * @param string $channel_name
     * @param mixed
     */
    function __construct($channel_name = "", $client = null)
    {
        parent::__construct($client);

        $this->_channel = $channel_name;
        $this->_endpoint = $this->_base_endpoint = 'channels/' . $channel_name;

        if (empty($channel_name)) {
            if (empty(Twitch::getAccessToken())) {
                throw new ChannelException("Please provide a channel name, or use Twitch::setAccessToken() to define the channels access token.");
            }

            if (Twitch::$scope->isAuthorized('channel_read') === false) {
                throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_read`.", 401);
            }

            $this->_endpoint = 'channel';
        }
    }

    /**
     * Update channel status for the selected channel
     * @scope channel_editor
     *
     * @return Channel
     * @throws TwitchScopeException
     */
    public function status($new_status)
    {
        if (Twitch::$scope->isAuthorized('channel_editor') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_editor`.", 401);
        }

        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint;

        if (!isset($this->_body->channel)) {
            $this->_body = (object) [
                'channel' => (object) []
            ];
        }

        $this->_body->channel->status = $new_status;

        return $this;
    }

    /**
     * Update game for the selected channel
     * @scope channel_editor
     *
     * @return Channel
     * @throws TwitchScopeException
     */
    public function game($new_game)
    {
        if (Twitch::$scope->isAuthorized('channel_editor') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_editor`.", 401);
        }

        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint;

        if (!isset($this->_body->channel)) {
            $this->_body = (object) [
                'channel' => (object) []
            ];
        }

        $this->_body->channel->game = $new_game;

        return $this;
    }

    /**
     * Update stream delay
     * @scope channel_editor
     *
     * @params string $delay Channel delay in seconds. Requires the channel owner’s OAuth token.
     *
     * @return Channel
     * @throws TwitchScopeException
     */
    public function streamDelay($delay)
    {
        if (Twitch::$scope->isAuthorized('channel_editor') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_editor`.", 401);
        }

        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint;

        if (!isset($this->_body->channel)) {
            $this->_body = (object) [
                'channel' => (object) []
            ];
        }

        $this->_body->channel->delay = $delay;

        return $this;
    }

    /**
     * Reset stream key
     * @scope channel_stream
     *
     * @return Channel
     * @throws TwitchScopeException
     */
    public function resetStreamKey()
    {
        if (Twitch::$scope->isAuthorized('channel_stream') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_stream`.", 401);
        }

        $this->_verb = 'DELETE';
        $this->_endpoint = $this->_base_endpoint;

        return $this;
    }

    /**
     * Return videos for a channel
     *
     * @params mixed[] $params {
     *     @type int $limit Maximum number of objects in array. Default is 10. Maximum is 100.
     *     @type int $offset Object offset for pagination. Default is 0.
     *     @type bool $broadcasts Returns only broadcasts when true. Otherwise only highlights are returned. Default is false.
     *     @type bool $hls Returns only HLS VoDs when true. Otherwise only non-HLS VoDs are returned. Default is false.
     * }
     *
     * @return Channel
     */
    public function videos(array $params = [])
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/videos';

        if (!empty($params)) {
            $this->_endpoint = $this->_endpoint . '?' . http_build_query($params);
        }

        return $this;
    }

    /**
     * Return followers for a channel
     *
     * @params mixed[] $params {
     *      @type int $limit Maximum number of objects in array. Default is 25. Maximum is 100.
     *      @type int $offset (deprecated) Object offset for pagination. Default is 0. Maximum is 1600.
     *      @type int $cursor Twitch uses cursoring to paginate long lists of followers. Check _cursor in response body and set cursor to this value to get the next page of results, or use _links.next to navigate to the next page of results.
     *      @type string $direction Creation date sorting direction. Default is desc. Valid values are asc and desc.
     * }
     *
     * @return Channel
     */
    public function followers()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/follows';

        return $this;
    }

    /**
     * Returns a list of editors for a channel
     *
     * @return Channel
     * @throws TwitchScopeException
     */
    public function editors()
    {
        if (Twitch::$scope->isAuthorized('channel_read') === false) {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_read`.", 401);
        }

        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/editors';

        return $this;
    }

    /**
     * Returns a list of teams for a channel
     *
     * @return Channel
     */
    public function teams()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/teams';

        return $this;
    }

    /**
     * Run a commercial
     *
     * @params int $length Length of commercial break in seconds. Default value is 30. Valid values are 30, 60, 90, 120, 150, and 180. You can only trigger a commercial once every 8 minutes.
     *
     * @return Channel
     * @throws TwitchScopeException
     */
    public function commercial($length = 30)
    {
        if (Twitch::$scope->isAuthorized('channel_commercial') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_commercial`.", 401);
        }

        $supported_lengths = [
            30,
            60,
            90,
            120,
            150,
            180
        ];

        if (!in_array($length, $supported_lengths)) {
            throw new ChannelException("Unsupported commercial length.");
        }

        $this->_verb = 'POST';
        $this->_endpoint = $this->_base_endpoint . '/commercial';

        $this->_body = [
            'length' => $length
        ];

        return $this;
    }

    /**
     * Return a channels feed.
     */
    public function feed()
    {
        return (new ChannelFeed($this->_channel));
    }

    public function emoticons()
    {
        return (new Chat($this->_channel))->emoticons();
    }
}
