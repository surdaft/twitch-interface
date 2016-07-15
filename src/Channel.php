<?php

namespace Twitch;

use Twitch\BaseMethod;
use Twitch\Exceptions\ChannelException;
use Twitch\Traits\CallStatically;

class Channel extends BaseMethod
{
    use CallStatically;
    
    private $_channel;

    /**
     * No channel name means it returns the channel of the access_token
     * holder. This shows your email address as well as your stream key.
     * @param string $channel_name [description]
     */
    function __construct($channel_name = "")
    {
        if (empty($channel_name)) {
            if (empty(Twitch::getAccessToken())) {
                throw new ChannelException("Please provide a channel name, or use Twitch::setAccessToken() to define the channels access token.");
            }

            $this->setEndpoint("channel");
        } else {
            $this->setEndpoint("channels/{$channel_name}");
        }

        $curl = Twitch::api($this->endpoint())->get();
        $this->setData($curl->data());
    }

    public function status($new_status)
    {
        $this->data()->status = $new_status;
        return $this;
    }

    public function game($new_game)
    {
        $this->data()->game = $new_game;
        return $this;
    }

    // to be used with setTitle and setGame
    public function update()
    {
        $response = Twitch::api($this->endpoint())->put([
          'channel' => [
              'game' => $this->data()->game,
              'status' => $this->data()->status
            ]
        ]);
        
        return $this;
    }

    // the other endpoint options available
    public function resetStreamKey()
    {
        return Twitch::api($this->endpoint())->delete();
    }

    public function videos()
    {
        $response = Twitch::api($this->endpoint() . "/videos")->get()->data();

        if ($response) {
            return $response->videos;
        } else {
            throw new ChannelException("Errors encountered retrieving videos.");
        }
    }

    public function followers()
    {
        $response = Twitch::api($this->endpoint() . "/follows")->get()->data();

        if ($response) {
            return $response->follows;
        } else {
            throw new ChannelException("Errors encountered retrieving follows.");
        }
    }

    public function editors()
    {
        $response = Twitch::api($this->endpoint() . "/editors")->get()->data();

        if ($response) {
            return $response->users;
        } else {
            throw new ChannelException("Errors encountered retrieving editors.");
        }
    }

    public function teams()
    {
        $response = Twitch::api($this->endpoint() . "/teams")->get()->data();

        if ($response) {
            return $response->teams;
        } else {
            throw new ChannelException("Errors encountered retrieving teams.");
        }
    }

    public function commercial($length = 30)
    {
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
