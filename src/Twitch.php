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
    protected static $access_token;
    protected static $scopes = [];

    const TWITCH_API_BASE_PATH = "https://api.twitch.tv/kraken/";

    public static function getClientId()
    {
        return static::$api_key;
    }

    public static function setClientId($api_key)
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
        
        $access_token_validation = Twitch::api()->get()->data();
        
        if (!$access_token_validation->token->valid) {
            throw new \Exception("This access token is not valid. Please confirm you have authorized the user correctly.", 401);
        }
        
        static::$scopes = $access_token_validation->token->authorization->scopes;
    }

    /**
     * Curl twitch
     * Use the curl function to GET / PUT / POST / DELETE requests to twitch' api.
     * 
     * Example: Twitch::api('channels/surdaft')->get();
     * 
     * $params $endpoint string
     */
    public static function api($endpoint = '')
    {
        return new ApiCurl($endpoint);
    }
}
