<?php

namespace Twitch;

class BaseMethod
{
    protected $_data;
    protected $_endpoint;
    
    public function __toString()
    {
        return print_r($this->_data, 1);
    }
    
    protected function setEndpoint($endpoint)
    {
        $this->_endpoint = $endpoint;
        return $this;
    }
    
    protected function setData($data)
    {
        $this->_data = $data;
        return $this;
    }
    
    protected function endpoint()
    {
        return $this->_endpoint;
    }
    
    public function data()
    {
        return $this->_data;
    }
    
    // public function __get($variable_name)
    // {
    //     if ( !isset($this->_data->$variable_name) && !property_exists($this, $variable_name) ) {
    //         throw new \Exception("This object does not exist: {$variable_name}");
    //     }
        
    //     if (isset($this->_data->$variable_name)) {
    //         return $this->_data->$variable_name;
    //     } elseif (property_exists($this, $variable_name)) {
    //         return $this->$variable_name;
    //     } elseif (method_exists($this, $variable_name)) {
    //         return $this->$variable_name();
    //     }
    // }
    
    // public function __set($variable_name, $variable_value)
    // {
    //     if (method_exists($this, $variable_name)) {
    //         $this->$variable_name($variable_value);
    //     } elseif (property_exists($this, $variable_name)) {
    //         $this->$variable_name = $variable_value;
    //     } elseif (isset($this->_data->$variable_name)) {
    //         $this->_data->$variable_name = $variable_value;
    //     } else {
    //         throw new \Exception("Unable to handle setting this variable.");
    //     }
        
    //     return $this;
    // }
}