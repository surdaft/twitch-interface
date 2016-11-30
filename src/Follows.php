<?php

namespace Twitch;

use Twitch\Traits\CallStatically;

class Follows extends BaseMethod
{
    use CallStatically;

    protected $user;

    public function __construct($user, $client = null)
    {
        parent::__construct($client);

        $this->_user = $user;
        $this->_endpoint = $this->_base_endpoint = "users/{$user}/follows";
    }

    public function channels()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/channels';

        return $this;
    }

    public function relationship($channel)
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . "/channels/{$channel}";

        return $this;
    }

    public function follow($channel)
    {
        $this->_verb = 'PUT';
        $this->_endpoint = $this->_base_endpoint . "/channels/{$channel}";

        return $this;
    }

    public function unfollow($channel)
    {
        $this->_verb = 'DELETE';
        $this->_endpoint = $this->_base_endpoint . "/channels/{$channel}";

        return $this;
    }
}