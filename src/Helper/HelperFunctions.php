<?php

namespace Twitch\Helpers;

class HelperFunctions
{
    public static function is_json($json)
    {
        return json_decode($json) !== null;
    }
}