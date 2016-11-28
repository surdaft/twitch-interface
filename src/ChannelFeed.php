<?php

namespace Twitch;

use Twitch\Channel;
use Twitch\BaseMethod;
use Twitch\Traits\CallStatically;

use Twitch\Exceptions\ChannelFeedException;

/**
 * Channel Feed
 * @link https://dev.twitch.tv/docs/api/v3/channel-feed
 */
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
     * @scope channel_feed_edit
     *
     * @return ChannelFeed
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
     * @scope channel_feed_read
     *
     * @params int $post_id
     *
     * @return ChannelFeedPost
     */
    public function post($post_id)
    {
        return new ChannelFeedPost($this->_channel, $post_id);
    }
}
