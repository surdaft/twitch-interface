<?php

namespace Twitch;
use Twitch\Exceptions\TwitchException;
use GuzzleHttp\Client;

/**
 * BaseMethod
 * Extend this method to create a new endpoint to connect to twitch via. This provides an easy to use basis for sending
 * new requests and adding new methods. Simply update the _verb, _endpoint and _data to set up the new request.
 *
 * @method public static fetch($client)
 */
class BaseMethod
{
    protected $_channel;
    protected $_endpoint = '';
    protected $_base_endpoint = ''; // to set the standard endpoint so that when it's overwritten we have an original

    protected $_verb = 'GET';
    protected $_body;
    protected $_client;

    public function __construct($client = null)
    {
        $this->_client = $client ?: $this->getClient();
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

        if (is_null($this->_client)) {
            throw new TwitchException('No http client, this method must be dodgy. Please post an issue in github: https://github.com/surdaft/twitch-interface');
        }

        $request = $this->_client->request($this->_verb, $this->_endpoint, [], $this->_body);
        $response = (string) $request->getBody();

        $this->_body = '';

        $decoded_response = json_decode($response);
        if (strlen($response) && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Could not decode response from Twitch: ' . json_last_error_msg());
        }

        return $decoded_response;
    }

    private function getClient()
    {
        $headers = [
            'Client-ID' => Twitch::getClientId(),
            'Accept' => 'application/vnd.twitchtv.v' . Twitch::version() . '+json'
        ];

        if (Twitch::getAccessToken()) {
            $headers['Authorization'] = 'OAuth ' . Twitch::getAccessToken();
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
