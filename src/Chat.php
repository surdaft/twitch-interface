<?php

namespace Twitch;

use Twitch\Traits\CallStatically;

class Chat extends BaseMethod
{
    use CallStatically;

    public function __construct($channel = false, $client = null)
    {
        parent::__construct($client);

        $this->_channel = $channel;
        $this->_endpoint = $this->_base_endpoint = 'chat';
    }

    public function emoticons()
    {
        $this->_verb = 'GET';

        if ($this->_channel) {
            $this->_endpoint = $this->_base_endpoint . "/{$this->_channel}/badges";
        } else {
            $this->_endpoint = $this->_base_endpoint . '/emoticons';
        }

        return $this;
    }

    public function emoticon_images(array $emotesets)
    {
        $this->_verb = 'GET';
        $this->_endpoint = $this->_base_endpoint . '/emoticon_images?emotesets=' . implode(',', $emotesets);

        return $this;
    }
}
