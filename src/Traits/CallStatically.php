<?php

namespace Twitch\Traits;

trait CallStatically
{
    public static function __callStatic($name, $params)
    {
        if ($name == 'fetch') {
            return (new static(array_shift($params)));
        }

        return static::$name(array_shift($params));
        
        // Optimally this would be the below, but due to limitations of php5.5
        // return static::$name(...$params);
    }
}