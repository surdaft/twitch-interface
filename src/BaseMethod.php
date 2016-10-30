<?php

namespace Twitch;
use Twitch\Exceptions\TwitchException;
use GuzzleHttp\Client;

class BaseMethod
{
    protected $_data;
    protected $_endpoint;

    protected $_verb = 'GET';

    public function __construct($client = null)
    {
        $this->client = $client ?: $this->getClient();
    }

    public function __toString()
    {
        return var_export($this->_data, 1);
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
        if (!Twitch::getClientId()) {
            throw new TwitchException('No client id specified');
        }

        $request = $this->client->request($this->_verb, $this->_endpoint);
        $response = (string) $request->getBody();

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
        $headers = [
            'Client-ID' => Twitch::getClientId()
        ];

        if (Twitch::getAccessToken()) {
            $headers['Authorization'] = 'Oauth ' . Twitch::getAccessToken();
        }

        return new Client([
            'headers' => $headers,
            'base_uri' => Twitch::baseURI
        ]);
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
