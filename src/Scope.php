<?php

namespace Twitch;

use Twitch\Exceptions\TwitchException;

class Scope
{
    protected $scopes = [];

    /**
     * is authorized for
     * Check whether an access token is authorized for a scope, this is used internally
     * to determine whether we can use a function before it does an API call.
     * @param string The scope you want to verify is authorized
     * @return bool
     */
    public function isAuthorized($scope)
    {
        return in_array($scope, $this->scopes);
    }

    /**
     * Add a scope to the authorized list
     * @param string | array The scope or scopes you want to add
     */
    public function addScope($scope)
    {
        $this->scopes[] = $scope;
        $this->scopes = array_unique($this->scopes);
    }

    public function addScopes(array $scopes)
    {
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * Remove a scope from the authorized list
     * @param  string | array $scope The scope or scopes you want to remove
     * @return bool
     */
    public function removeScope($scope)
    {
        if (is_array($scope)) {
            foreach ($scope as $single_scope) {
                $scope_key = $this->findScopeKey($single_scope);

                if (!$scope_key) {
                    continue;
                }

                unset($this->scopes[$scope_key]);
            }
        } else {
            $scope_key = $this->findScopeKey($scope);

            if (!$scope_key) {
                return true;
            }

            unset($this->scopes[$scope_key]);
        }

        return true;
    }

    /**
     * Find the key for a scope
     * @param  string $scope Name of the scope you want to find the key of
     * @return int | false   False would mean the scope was not found
     */
    private function findScopeKey($scope)
    {
        if (!$this->isAuthorized($scope)) {
            return false;
        }

        return array_search($scope, $this->scopes);
    }

    /**
     * Get authorized scopes
     * Returns an array of scopes the access token is authorized for
     * @return array
     */
    public function authorized()
    {
        return $this->scopes;
    }

    /**
     * Wipe out the scopes array
     */
    public function resetScopes()
    {
        \Twitch\Twitch::$scope = new self;
    }
}
