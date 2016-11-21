<?php

namespace Twitch;

use Twitch\Channel;
use Twitch\BaseMethod;
use Twitch\Traits\CallStatically;

use Twitch\Exceptions\ChannelFeedException;

class ChannelFeed extends BaseMethod
{
    use CallStatically;
    
    /**
     * @params $channel string
     *
     * @return array An array of all the posts in the channel
     */
    function __construct($channel)
    {
        $this->_channel = $channel;
        $this->_endpoint = $this->_base_endpoint = "feed/{$channel}/posts";
    }

    /**
     * Create a new feed post
     * Access token must be authorized with the channel scope `channel_feed_edit`.
     *
     * @link https://dev.twitch.tv/docs/api/v3/channel-feed#post-feedchannelposts
     */
    public function create(array $params)
    {
        if (empty($params['content'])) {
            throw new ChannelFeedException("Content is required when creating a new channel feed post.");
        }

        if (Twitch::$scope->isAuthorized('channel_feed_edit') === false) {
           throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_feed_edit`.", 401);
       }
       
       $this->_verb = 'POST';
       $this->_endpoint = $this->_base_endpoint;
       $this->_body = [
           'content' => $params['content'],
           'share' => !empty($params['share'])
       ];

        return $this;
    }

    /**
     * Select a post
     * Selecting a post returns all the post data as well as gives you access to
     * certain functions that respond to posts. Like React and Delete.
     *
     * @params number $post_id
     *
     * @return ChannelFeedPost returns a channel feed post which can do more with
     * posts specifically.
     */
    public function post($post_id)
    {
        // Optional scope?
        //
        // if (!Twitch::isAuthorizedFor('channel_feed_read')) {
        //     throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_feed_read`.", 401);
        // }

        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . "/{$post_id}";

        return $this;
    }
}
