<?php

namespace Twitch\Helpers;

use Twitch\Helpers\HelperFunctions;

class CurlHelper
{
    private $endpoint;
    private $errors = [];
    private $curl;
    private $data = [];
    
    public function _construct($endpoint)
    {
        $this->endpoint = $endpoint;
        $this->curl = curl_init($endpoint);
        
        curl_setop_array($this->curl, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                // OAUTH
            ]
        ]);
    }
    
    public function get()
    {
        // get
        
    }
    
    public function put($data = [])
    {
        // set the method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        
        $this->data = array_merge($data, $this->data);
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->data));
    }
    
    public function delete()
    {
        // delete
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    
    private function curl()
    {
        $response = curl_exec($this->curl);
        
        if (!HelperFunctions::is_json($response)) {
            return false;
        }
        
        return json_decode($response);
    }
    
    public function data(array $data)
    {
        $this->data = array_merge($data, $this->data);
        return $this;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function errors()
    {
        return $this->errors;
    }
}