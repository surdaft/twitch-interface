<?php

namespace Twitch;

use Twitch\Interfaces\MethodInterface;

class Channel implements MethodInterface
{
    // public function _callStatic($name, $channel_name)
    // {
    //     if ($name == 'fetch') {
    //         return (new Channel($channel_name));
    //     }
        
    //     return static::$name($params);
    // }
    
    public static function fetch($channel_name)
    {
        return (new Channel)->_fetch($channel_name);
    }
    
    private function _fetch($channel_name)
    {
        
    }
}