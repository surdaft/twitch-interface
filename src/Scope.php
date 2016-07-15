<?php

namespace Twitch;

use Twitch\Exceptions\TwitchException;

class Scope
{
    /**
     * is authorized for
     * Check whether an access token is authorized for a scope, this is used internally
     * to determine whether we can use a function before it does an API call.
     */
    public static function isAuthorized()
    {
        return in_array($scope, Twitch::$scopes);
    }

    /**
     * Get authorized scopes
     * Returns an array of scopes the access token is authorized for
     */
    public function getAuthorized()
    {
        return Twitch::$scopes;
    }
}
