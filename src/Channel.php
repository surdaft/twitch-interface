<?php

namespace Twitch;

use Twitch\BaseMethod;
use Twitch\Exceptions\ChannelException;
use Twitch\Traits\CallStatically;

class Channel extends BaseMethod
{
    /**
     * No channel name means it returns the channel of the access_token
     * holder. This shows your email address as well as your stream key.
     * @param string $channel_name [description]
     */
    function __construct($channel_name = "", $client = null)
    {
        parent::__construct($client);

        $this->_body = (object) [
            'channel' => (object) []
        ];

        if (empty($channel_name)) {
            if (empty(Twitch::getAccessToken())) {
                throw new ChannelException("Please provide a channel name, or use Twitch::setAccessToken() to define the channels access token.");
            }

            if (Twitch::$scope->isAuthorized('channel_read') === false) {
                throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_read`.", 401);
            }
        }

        $this->_endpoint = 'channels/' . $channel_name;
    }

    public function status($new_status)
    {
        $this->_verb = 'PUT';

        $this->_body->channel->status = $new_status;
        return $this;
    }

    public function game($new_game)
    {
        $this->_verb = 'PUT';

        $this->_body->channel->game = $new_game;
        return $this;
    }
    
    // after here needs updating with the new way of getting data

    public function resetStreamKey()
    {
        if (Twitch::$scope->isAuthorized('channel_stream') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_stream`.", 401);
        }

        return Twitch::api($this->endpoint())->delete();
    }

    public function videos()
    {
        $response = Twitch::api($this->endpoint() . "/videos")->get()->data();

        if ($response) {
            return $response;
        } else {
            throw new ChannelException("Errors encountered retrieving videos.");
        }
    }

    public function followers()
    {
        $response = Twitch::api($this->endpoint() . "/follows")->get()->data();

        if ($response) {
            return $response;
        } else {
            throw new ChannelException("Errors encountered retrieving follows.");
        }
    }

    public function editors()
    {
        if (Twitch::$scope->isAuthorized('channel_read') === false) {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_read`.", 401);
        }

        $response = Twitch::api($this->endpoint() . "/editors")->get()->data();

        if ($response) {
            return $response;
        } else {
            throw new ChannelException("Errors encountered retrieving editors.");
        }
    }

    public function teams()
    {
        $response = Twitch::api($this->endpoint() . "/teams")->get()->data();

        if ($response) {
            return $response;
        } else {
            throw new ChannelException("Errors encountered retrieving teams.");
        }
    }

    public function commercial($length = 30)
    {
        if (Twitch::$scope->isAuthorized('channel_commercial') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_commercial`.", 401);
        }

        if (empty($this->data()->partner)) {
            throw new ChannelException("You need to be a partner to run commercials.");
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

        return Twitch::api($this->endpoint() . "/commercial")->post(['length' => $length]);
    }

    public function channel()
    {
        return $this->data()->name;
    }

    /**
     *
     */
    public function feed()
    {
        return ChannelFeed::fetch($this->data()->name);
    }
}
