<?php

namespace Twitch;

class ChannelFeed implements MethodInterface
{
    private $_post;

    public function posts()
    {

    }

    public function create()
    {

    }

    public function post($post_id)
    {
        $post = (object) [];
        $this->_post = $post;

        return $this;
    }

    // $channel_feed->delete(3)

    // $channel_feed->post(3)->delete();
    // $channel_feed->post(3)->react(213123);

    public function delete()
    {
        
        $response = Twitch::api($this->_endpoint)->delete();
    }

    public function react()
    {

    }
}
