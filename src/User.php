<?php

namespace Twitch;

use Twitch\Traits\CallStatically;

class User extends BaseMethod
{
    use CallStatically;

    protected $user;

    public function __construct($user_id, $client = null)
    {
        parent::__construct($client);

        $this->_user = $user_id;
        $this->_endpoint = $this->_base_endpoint = "users/{$user_id}";
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
    public function following($live_only = false)
    {
        if ($live_only) {
            $this->_verb = 'GET';
            $this->_endpoint = 'streams/followed';
        } else {
            $this->_verb = 'GET';
            $this->_endpoint = $this->_base_endpoint . '/follows/channels';
        }

        return $this;
    }

    /**
     * TODO: Add @scope validation
     */
    public function videos()
    {
        $this->_verb = 'GET';
        $this->_endpoint = 'videos/followed';

        return $this;
    }

    public function blocked()
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/blocks';

        return $this;
    }

    public function usingDisplayName()
    {
        $this->_endpoint = 'users?login=' . $this->_user;
        return $this;
    }

    public static function getUserFromDisplayName($display_name)
    {
        $user = new static($display_name);
        $user->usingDisplayName();

        return $user->send();
    }
}
