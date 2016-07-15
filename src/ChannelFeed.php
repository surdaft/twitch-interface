<?php

namespace Twitch;

use Twitch\Channel;
use Twitch\BaseMethod;
use Twitch\Traits\CallStatically;

use Twitch\Exceptions\ChannelFeedException;

class ChannelFeed extends BaseMethod
{
    use CallStatically;
    
    private $_channel;
    
    /**
     * @params $channel string
     * 
     * @return array An array of all the posts in the channel
     */
    function __construct($channel)
    {
        // todo: validate the channel name
        
        $this->_channel = $channel;
        $this->setEndpoint("feed/{$channel}");
        
        $curl = Twitch::Api($this->endpoint() . "/posts")->get();
        $this->setData($curl->data()->posts);
        
        if (empty($this->data())) {
            throw new ChannelFeedException("Errors encountered retrieving posts.");
        }
    }

    /**
     * Create a new feed post
     * Access token must be authorized with the channel scope `channel_feed_edit`.
     * 
     * @params $params[] $content You must provide atleast content within the params.
     */
    public function create(array $params)
    {
        if (empty($params['content'])) {
            throw new ChannelFeedException("Content is required when creating a new channel feed post.");
        }

        if (!Twitch::isAuthorizedFor('channel_feed_edit')) {
           throw new TwitchScopeException("You do not have sufficient scope priviledges to run this command. Make sure you're authorized for `channel_feed_edit`.", 401);
       }

        return Twitch::Api($this->endpoint())->post([
            'content' => $params['content'],
            'share' => !empty($params['share'])
        ]);
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

        return (new ChannelFeedPost([
            'channel' => $this->_channel,
            'post_id' => $post_id
        ]));
    }
}
