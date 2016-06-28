<?php

namespace Twitch;

class Twitch
{
    static $api_key;
    
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
}