<?php

namespace Twitch;

use Twitch\Exceptions\TwitchException;

class Scope
{
    protected static $scopes = [];

    /**
     * is authorized for
     * Check whether an access token is authorized for a scope, this is used internally
     * to determine whether we can use a function before it does an API call.
     * @param string The scope you want to verify is authorized
     * @return bool
     */
    public static function isAuthorized($scope)
    {
        return in_array($scope, static::$scopes);
    }

    /**
     * Add a scope to the authorized list
     * @param string | array The scope or scopes you want to add
     */
    public static function addAuthorized($scope)
    {
        if (is_array($scope)) {
            static::$scopes = array_merge(static::$scopes, $scope);
        } else {
            static::$scopes[] = $scope;
        }

        static::$scopes = array_unique(static::$scopes);
    }

    /**
     * Remove a scope from the authorized list
     * @param  string | array $scope The scope or scopes you want to remove
     * @return bool
     */
    public static function removeAuthorized($scope)
    {
        if (is_array($scope)) {
            foreach ($scope as $single_scope) {
                $scope_key = static::findScopeKey($single_scope);

                if (!$scope_key) {
                    continue;
                }

                unset(static::$scopes[$scope_key]);
            }
        } else {
            $scope_key = static::findScopeKey($scope);

            if (!$scope_key) {
                return true;
            }

            unset(static::$scopes[$scope_key]);
        }

        return true;
    }

    /**
     * Find the key for a scope
     * @param  string $scope Name of the scope you want to find the key of
     * @return int | false   False would mean the scope was not found
     */
    private static function findScopeKey($scope)
    {
        if (!static::isAuthorized($scope)) {
            return false;
        }

        return array_search($scope, static::$scopes);
    }

    /**
     * Get authorized scopes
     * Returns an array of scopes the access token is authorized for
     * @return array
     */
    public function authorized()
    {
        return static::$scopes;
    }

    /**
     * Wipe out the scopes array
     */
    public function resetScopes()
    {
        static::$scopes = [];
    }
}
