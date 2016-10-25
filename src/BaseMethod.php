<?php

namespace Twitch;

class BaseMethod
{
    protected $_data;

    public function __construct($client = null)
    {
        if (is_null($client)) {
            $client = $this->getClient();
        }

        $this->client = $client;
    }

    public function __toString()
    {
        return print_r($this->_data, 1);
    }

    public static function __callStatic($name, $params)
    {
        if ($name == 'fetch') {
            return (new static(...$params))->send();
        }

        return static::$name(...$params);
    }

    public function send()
    {
        $response = (string) $this->client->getBody();
        $decoded_response = json_decode($response);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Could not decode response from Twitch: ' . json_last_error_msg());
        }

        return $decoded_response;
    }

    protected function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    public function data()
    {
        return $this->_data;
    }

    private function getClient()
    {
        throw new \Exception('This is not set up yet.');

        $client = null;
        return $client;
    }

    public static function getClientHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.twitchtv.v3+json'
        ];

        if (!empty(Twitch::getClientId())) {
            $headers['Client-ID'] = Twitch::getClientId();
        }

        if (!empty(Twitch::getAccessToken())) {
            $headers['Authorization'] = "OAuth " . Twitch::getAccessToken();
        }

        return $headers;
    }
}
