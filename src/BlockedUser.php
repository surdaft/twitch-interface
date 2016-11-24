<?php

namespace Twitch;

use Twitch\Traits\CallStatically;
use Twitch\Exceptions\TwitchScopeException;

/**
 * Blocked User
 * @link https://dev.twitch.tv/docs/api/v3/blocks/
 * @method public static function fetch($user, $blocked_user)
 */
class BlockedUser extends BaseMethod
{
    use CallStatically;
    
    protected $user;
    protected $blocked_user;
    
    /**
     * This is not an actual endpoint
     */
    public function __construct($user, $blocked_user)
    {
        $this->_user = $user;
        $this->_blocked_user = $blocked_user;
        $this->_endpoint = $this->_base_endpoint = "/users/{$user}/blocks/{$blocked_user}";
    }
    
    /**
     * Block the user selected in the constructor.
     * @scope user_blocks_edit
     * 
     * @return BlockedUser
     * @throws \Twitch\Exceptions\TwitchScopeException
     */
    public function block()
    {
        if (Twitch::$scope->isAuthorized('user_blocks_edit') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `user_blocks_edit`.", 401);
        }
        
        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint;
        
        return $this;
    }
    
    /**
     * Unblock the user selected in the constructor
     * @scope user_blocks_edit
     * 
     * @return BlockedUser
     * @throws \Twitch\Exceptions\TwitchScopeException
     */
    public function unblock()
    {
        if (Twitch::$scope->isAuthorized('user_blocks_edit') === false)    {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `user_blocks_edit`.", 401);
        }
        
        $this->_verb = 'DELETE';
        $this->_endpoint = $this->_base_endpoint;
        
        return $this;
    }
}