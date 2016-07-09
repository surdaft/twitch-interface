<?php

namespace Twitch;

use Twitch\Helpers\ApiCurl;

class Twitch
{
    /**
     * This is your clientID
     */
    static $api_key;

    /**
     * This is the token that is returned when a user authenticates
     * through the OAuth2 method.
     */
    static $access_token;

    const TWITCH_API_BASE_PATH = "https://api.twitch.tv/kraken/";

    public static function getApiKey()
    {
        return static::$api_key;
    }

    public static function setApiKey($api_key)
    {
        if (!is_string($api_key)) {
            throw new InvalidArgumentException("setApiKey only accepts strings.");
        }
        static::$api_key = $api_key;
    }

    public static function getAccessToken()
    {
        return static::$access_token;
    }

    /**
     * You obtain an access token when you follow the authentication process
     * https://github.com/justintv/Twitch-API/blob/master/authentication.md
     */
    public static function setAccessToken($access_token)
    {
        if (!is_string($access_token)) {
            throw new InvalidArgumentException("setAccessToken only accepts strings.");
        }
        static::$access_token = $access_token;
    }

    public static function api($endpoint)
    {
        /**
         * Sudo code
         * return new Curl($endpoint);
         *
         * Indended use
         * Twitch::api('channels')->get();
         * Twitch::api('channels')->data(['title' => 'HELLO', 'game' => 'Creative'])->put();
         */
        return new ApiCurl($endpoint);
    }
}
