<?php

namespace Twitch;

use Twitch\Interfaces\MethodInterface;
use Twitch\Exceptions\Channel as ChannelException;

class Channel
{
    protected $_endpoint;
    protected $_game;
    protected $_status;
    protected $_name;
    protected $_display_name;
    protected $_logo;
    protected $_video_banner;
    protected $_profile_banner;
    protected $_partner;
    protected $_url;
    protected $_views;
    protected $_follows;
    protected $_data;

    public static function __callStatic($name, $params)
    {
        if ($name == 'fetch') {
            return (new Channel(...$params));
        }

        return static::$name(...$params);
    }


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

            $this->_endpoint = "channel";
        } else {
            $this->_endpoint = "channels/{$channel_name}";
        }

        $this->_curl = Twitch::api($this->_endpoint)->get();
        $this->_data = $this->_curl->data();

        if (empty($this->_data)) {
            print_r($this->_curl); exit;
            throw new ChannelException("Errors encountered retrieving data.");
        }

        $this->_name = $this->_data->name;
        $this->_game = $this->_data->game;
        $this->_status = $this->_data->status;
        $this->_display_name = $this->_data->display_name;
        $this->_logo = $this->_data->logo;
        $this->_video_banner = $this->_data->video_banner;
        $this->_profile_banner = $this->_data->profile_banner;
        $this->_partner = !empty($this->_data->partner);
        $this->_url = $this->_data->url;
        $this->_views = $this->_data->views;
        $this->_follows = $this->_data->followers;
    }

    public function setStatus($new_status)
    {
        $this->_status = $new_status;
        return $this;
    }

    public function setGame($new_game)
    {
        $this->_game = $new_game;
        return $this;
    }

    // to be used with setTitle and setGame
    public function update()
    {
        return Twitch::api($this->_endpoint)->scope('channel_editor')->put([
            'game' => $this->_game,
            'status' => $this->_status
        ]);
    }

    // the other endpoint options available
    public function resetStreamKey()
    {
        $response = Twitch::api($this->_endpoint)->delete();
    }

    public function videos()
    {
        $response = Twitch::api($this->_endpoint . "/videos")->get()->data();

        if ($response) {
            return $response->videos;
        } else {
            throw new ChannelException("Errors encountered retrieving videos.");
        }
    }

    public function followers()
    {
        $response = Twitch::api($this->_endpoint . "/follows")->get()->data();

        if ($response) {
            return $response->follows;
        } else {
            throw new ChannelException("Errors encountered retrieving follows.");
        }
    }

    public function editors()
    {
        $response = Twitch::api($this->_endpoint . "/editors")->get()->data();

        if ($response) {
            return $response->users;
        } else {
            throw new ChannelException("Errors encountered retrieving editors.");
        }
    }

    public function teams()
    {
        $response = Twitch::api($this->_endpoint . "/teams")->get()->data();

        if ($response) {
            return $response->teams;
        } else {
            throw new ChannelException("Errors encountered retrieving teams.");
        }
    }

    public function commercial($length = 30)
    {
        if (!$this->_partner) {
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

        return Twitch::api($this->_endpoint . "/commercial")->put(['length' => $length]);
    }
}
