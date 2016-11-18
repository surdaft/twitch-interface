<?php

namespace Twitch;
use Twitch\Exceptions\TwitchException;
use GuzzleHttp\Client;

class BaseMethod
{
    protected $_endpoint = '';
    protected $_base_endpoint = ''; // to set the standard endpoint so that when it's overwritten we have an original

    protected $_body = '';
    protected $_verb = 'GET';

    public function __construct($client = null)
    {
        $this->client = $client ?: $this->getClient();
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

        $request = $this->client->request($this->_verb, $this->_endpoint, [], $this->_body);
        $response = (string) $request->getBody();

        $this->_body = '';

        $decoded_response = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Could not decode response from Twitch: ' . json_last_error_msg());
        }

        return $decoded_response;
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

    public function body()
    {
        return $this->_body;
    }

    public function endpoint()
    {
        return $this->_endpoint;
    }

    public function verb()
    {
        return $this->_verb;
    }
}
