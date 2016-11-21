<?php

namespace Twitch;

use Twitch\Twitch;
use Twitch\Exceptions\TwitchInterfaceException;
/**
 * @method static generate(array $scopes, $redirect_uri, $state = '')
 */
class Auth
{
    private $_scopes = [];
    private $_redirect_uri;
    private $_state;

    public static function __callStatic($name, $args)
    {
        if ($name === 'generate') {
            return (new static(...$args))->_generate();
        }
    }

    public function __construct(array $scopes, $redirect_uri, $state = "")
    {
        if (empty(Twitch::getClientId())) {
            throw new TwitchInterfaceException('You require a client ID to generate an auth URL');
        }

        $this->scopes($scopes);
        $this->redirectUri($redirect_uri);
        $this->state($state);
    }

    public function scopes(array $scopes)
    {
        $this->_scopes = array_merge($this->_scopes, $scopes);
        return $this;
    }

    public function redirectUri($redirect_uri)
    {
        $this->_redirect_uri = $redirect_uri;
        return $this;
    }

    public function state($state)
    {
        $this->_state = $state;
        return $this;
    }

    public function _generate()
    {
        $params = [
            'response_type' => 'code',
            'client_id' => Twitch::getClientId(),
            'redirect_uri' => $this->_redirect_uri,
            'scope' => implode(' ', $this->_scopes)
        ];

        if (!empty($this->_state)) {
            $params['state'] = $this->_state;
        }

        $query_string = http_build_query($params);

        return Twitch::base_path . "/oauth2/authorize/?" . $query_string;
    }

    public static function codeListener($url_parameters, $redirect_uri, $state = '')
    {
        if (empty($url_parameters['code'])) {
            return;
        }

        $post_data = [
            'client_id' => Twitch::getClientId(),
            'client_secret' => Twitch::getClientSecret(),
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirect_uri,
            'code' => $url_parameters['code']
        ];

        if (!empty($state)) {
            $post_data['state'] = $state;
        }

        return Twitch::api('oauth2/token')->post($post_data)->data();
    }
}
