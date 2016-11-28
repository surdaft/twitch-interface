<?php

namespace Twitch;

use Twitch\BaseMethod;
use Twitch\Traits\CallStatically;
use Twitch\Exceptions\ChannelFeedPostException;

/**
 * Channel Feed Post
 * @link 
 */
class ChannelFeedPost extends BaseMethod
{
    use CallStatically;
    
    protected $_post_id;
    
    function __construct($channel, $post_id)
    {
        $this->_channel = $channel;
        $this->_post_id = $post_id;
        $this->_endpoint = $this->_base_endpoint = "feed/{$channel}/posts/{$post_id}";
    }

    public function delete()
    {
        if (Twitch::$scope->isAuthorized('channel_feed_edit') === false) {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_feed_edit`.", 401);
        }
        
        $this->_verb = 'DELETE';
        $this->_endpoint = $this->_base_endpoint;

        return $this;
    }

    /**
     * React to a post
     *
     * This posts a reaction to the post on behalf of the access token holder.
     * Emote ID 25 is Kappa.
     */
    public function react($emote_id)
    {
        if (Twitch::$scope->isAuthorized('channel_feed_edit') === false) {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_feed_edit`.", 401);
        }

        if (!is_string($emote_id) && !is_numeric($emote_id)) {
            throw new InvalidArgumentException("Emote ID must be either a string or a number.");
        }
        
        $this->_verb = 'POST';
        $this->_endpoint = $this->_base_endpoint . '/reactions';
        $this->_body = [
            'emote_id' => $emote_id
        ];
        
        return $this;
    }
    
    /**
     * This undoes the reaction from the method above.
     */
    public function unreact($emote_id)
    {
        if (Twitch::$scope->isAuthorized('channel_feed_edit') === false) {
            throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_feed_edit`.", 401);
        }

        if (!is_string($emote_id) && !is_numeric($emote_id)) {
            throw new InvalidArgumentException("Emote ID must be either a string or a number.");
        }
        
        $this->_verb = 'DELETE';
        $this->_endpoint = $this->_base_endpoint . '/reactions';
        $this->_body = [
            'emote_id' => $emote_id
        ];
        
        return $this;
    }
}