<?php

namespace Twitch;

use Twitch\Exceptions\TwitchInterfaceException;
use InvalidArgumentException;

/**
 * @method static generate(array $scopes, $redirect_uri, $state = '') Generate an auth url from scopes, and redirect URI
 */
class Auth
{
    private $_scopes = [];
    private $_redirect_uri;
    private $_state;

    /**
     * Call static methods to return an instance
     *
     * @param $name
     * @param $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        if ($name === 'generate') {
            return (new static(...$args))->_generate();
        }
    }

    /**
     * Auth constructor.
     *
     * @param array $scopes The scopes you want the user to grant you access to.
     * @param string $redirect_uri The uri you want your user to go to, after authorizing your application.
     * @param string $state You would optionally generate a random ID to pass through here.
     *
     * @throws TwitchInterfaceException
     */
    public function __construct(array $scopes, $redirect_uri, $state = null)
    {
        if (empty(Twitch::getClientId())) {
            throw new TwitchInterfaceException('You require a client ID to generate an auth URL');
        }

        $this->scopes($scopes);
        $this->redirectUri($redirect_uri);

        if ($state) {
            $this->state($state);
        }
    }

    /**
     * Set the scopes you are requesting access to
     *
     * @param array $scopes Array of the scopes
     * @return $this
     */
    public function scopes(array $scopes)
    {
        $this->_scopes = array_merge($this->_scopes, $scopes);
        return $this;
    }

    /**
     * Set the url you want Twitch to redirect the user to, after authorizing with Twitch.
     *
     * @param string $redirect_uri The redirect url
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function redirectUri($redirect_uri)
    {
        if (!is_string($redirect_uri)) {
            throw new InvalidArgumentException('redirectUri only accepts strings.');
        }

        $this->_redirect_uri = $redirect_uri;
        return $this;
    }

    /**
     * Set the state for this auth url
     *
     * @param string $state A randomly generated identifier to pass to Twitch
     * @link http://www.twobotechnologies.com/blog/2014/02/importance-of-state-in-oauth2.html
     * @return $this
     */
    public function state($state)
    {
        $this->_state = $state;
        return $this;
    }

    /**
     * Generate the end uri which you will redirect the user to
     *
     * @return string
     */
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

    /**
     * Place this at the redirectUri to wait for the request token, where it will then do the final post and return the
     * access token for that user. Finishing the authentication process.
     *
     * @param $code
     * @param $redirect_uri
     * @param string $state
     * @return mixed
     */
    public static function codeListener($code, $redirect_uri, $state = '')
    {
        $post_data = [
            'client_id' => Twitch::getClientId(),
            'client_secret' => Twitch::getClientSecret(),
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirect_uri,
            'code' => $code
        ];

        if (!empty($state)) {
            $post_data['state'] = $state;
        }

        return Twitch::api('oauth2/token')->post($post_data)->data();
    }
}
