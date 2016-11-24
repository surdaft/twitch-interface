<?php

namespace Twitch;

use Twitch\BaseMethod;
use Twitch\Exceptions\ChannelException;
use Twitch\Traits\CallStatically;
use Twitch\Exceptions\TwitchScopeException;

class Channel extends BaseMethod
{
    use CallStatically;
    
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

        $this->_channel = $channel_name;
        $this->_endpoint = 'channels/' . $channel_name;
        $this->_base_endpoint = 'channels/' . $channel_name;
    }

    public function status($new_status)
    {
        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint;

        $this->_body->channel->status = $new_status;
        return $this;
    }

    public function game($new_game)
    {
        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint;

        $this->_body->channel->game = $new_game;
        return $this;
    }
    
    // after here needs updating with the new way of getting data

    public function resetStreamKey()
    {
        if (Twitch::$scope->isAuthorized('channel_stream') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_stream`.", 401);
        }
        
        $this->_verb = 'DELETE';
        $this->_endpoint = $this->_base_endpoint;

        return $this;
    }

    public function videos()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/videos';
        
        return $this;
    }

    public function followers()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/follows';
        
        return $this;
    }

    public function editors()
    {
        if (Twitch::$scope->isAuthorized('channel_read') === false) {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_read`.", 401);
        }

        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/editors';

        return $this;
    }

    public function teams()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/teams';
        
        return $this;
    }

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
