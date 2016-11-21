<?php

namespace Twitch;

use Twitch\Traits\CallStatically;

class Stream extends BaseMethod
{
    use CallStatically;
    
    public function __construct()
    {
        $this->_endpoint = $this->_base_endpoint = '/streams';
    }
    
    public function channel($channel)
    {
        $this->_endpoint = $this->_base_endpoint . "/{$channel}";
        return $this;
    }
    
    public function featured()
    {
        $this->_endpoint = $this->_base_endpoint . '/featured';
        return $this;
    }
    
    public function summary()
    {
        $this->_endpoint = $this->_base_endpoint . '/summary';
        return $this;
    }
    
    public function followed()
    {
        $this->_endpoint = $this->_base_endpoint . '/followed';
        return $this;
    }
}