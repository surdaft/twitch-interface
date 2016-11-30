<?php

namespace Twitch;

use Twitch\Traits\CallStatically;

class User extends BaseMethod
{
    use CallStatically;

    protected $user;

    public function __construct($user, $client = null)
    {
        parent::__construct($client);

        $this->_user = $user;
        $this->_endpoint = $this->_base_endpoint = "users/{$user}";
    }

    public function emotes()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/emotes';

        return $this;
    }

    /**
     * TODO: Add @scope validation
     */
    public function followed()
    {
        $this->_verb = 'GET';
        $this->_endpoint = '/streams/followed';

        return $this;
    }

    /**
     * TODO: Add @scope validation
     */
    public function videos()
    {
        $this->_verb = 'GET';
        $this->_endpoint = '/videos/followed';

        return $this;
    }

    public function blocked()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/blocks';

        return $this;
    }
}