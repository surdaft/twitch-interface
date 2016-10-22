<?php

namespace Twitch;

use \InvalidArgumentException;
use Twitch\Helpers\ApiCurl;

class Twitch
{
    /**
     * This is your clientID & secret
     */
    static $api_key;
    static $api_secret;

    const base_path = "https://api.twitch.tv/kraken/";

    /**
     * This is the token that is returned when a user authenticates
     * through the OAuth2 method.
     */
    protected static $access_token;

    public static function getClientId()
    {
        return static::$api_key;
    }

    public static function setClientId($api_key)
    {
        if (!is_string($api_key)) {
            throw new InvalidArgumentException("setClientId only accepts strings.");
        }
        static::$api_key = $api_key;
    }

    public static function getClientSecret()
    {
        return static::$api_secret;
    }

    public static function setClientSecret($api_secret)
    {
        if (!is_string($api_secret)) {
            throw new InvalidArgumentException("setClientSecret only accepts strings.");
        }
        static::$api_secret = $api_secret;
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

        // there would be a chance when changing access_tokens that additional scopes could be added on top of
        // older ones.
        Scope::resetScopes();
        Scope::addAuthorized($access_token_validation->token->authorization->scopes);
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
        ApiCurl::$base_path = self::base_path;

        return new ApiCurl($endpoint);
    }
}
