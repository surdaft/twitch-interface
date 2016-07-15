<?php

namespace Twitch;

use Twitch\BaseMethod;
use Twitch\Traits\CallStatically;
use Twitch\Exceptions\ChannelFeedPostException;

class ChannelFeedPost extends BaseMethod
{
    use CallStatically;
    
    function __construct(array $params)
    {
        if (empty($params['channel']) || empty($params['post_id'])) {
            throw new ChannelFeedPostException("We require both the channel and post_id to be passed to ChannelFeedPost as an array.");
        }
        
        $channel = $params['channel'];
        $post_id = $params['post_id'];
        
        $this->setEndpoint("feed/{$channel}/posts/{$post_id}");
        
        $curl = Twitch::Api($this->endpoint())->get();
        $this->setData($curl->data());
    }

    public function delete()
    {
        return Twitch::api($this->endpoint())->delete();
    }

    /**
     * React to a post
     * 
     * This posts a reaction to the post on behalf of the access token holder.
     * Emote ID 25 is Kappa.
     */
    public function react($emote_id)
    {
        $response = Twitch::api($this->endpoint() . "/reactions")->post([
            'emote_id' => $emote_id
        ]);
        
        dd($response);
        
        return $this;
    }
    
    /**
     * This undoes the reaction from the method above.
     */
    public function unreact($emote_id)
    {
        $response = Twitch::api($this->endpoint() . "/reactions")->delete([
            'emote_id' => $emote_id
        ]);
        
        return $this;
    }
}