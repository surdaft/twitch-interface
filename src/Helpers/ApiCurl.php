<?php

namespace Twitch\Helpers;

use Twitch\Twitch;
use Twitch\Helpers\HelperFunctions;
use Twitch\Exceptions\TwitchInterfaceException;
use Twitch\Exceptions\ApiCurlException;

class ApiCurl
{
    public static $base_path;

    private $_endpoint;
    private $_code;
    private $_url;

    private $_client;
    private $_headers;

    private $_errors = [];
    private $_data = [];

    function __construct($endpoint, $return_data = false)
    {
        $this->_endpoint = $endpoint;

        if (empty(static::$base_path)) {
            throw new ApiCurlException("No base path found");
        }

        $this->_url = static::$base_path . $this->_endpoint;

        $this->_client = new \GuzzleHttp\Client();

        $this->_headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.twitchtv.v3+json'
        ];

        if (!empty(Twitch::getClientId())) {
            $this->_headers[] = 'Client-ID' => Twitch::getClientId();
        }

        if (!empty(Twitch::getAccessToken())) {
            $this->_headers[] = 'Authorization' => 'OAuth ' . Twitch::getAccessToken();
        }
    }

    /**
     * Standard curl, returning the data found on the other end.
     * @return [type] [description]
     */
    public function get()
    {
        $this->_method = 'GET';

        // The finalise function collates all the data and is what actually
        // does the curl request.
        return $this->finalise();
    }

    /**
     * Complete the curl request with a put. To add data to be 'putted' You
     * insert an array as the params.
     * @param  array $data
     * @return this
     */
    public function put(array $data = [])
    {
        $this->_data = $data;
        $this->_method = 'PUT';

        return $this->finalise();
    }

    public function post(array $data = [])
    {
        $this->_data = $data;
        $this->_method = 'POST';

        return $this->finalise();
    }

    public function delete(array $data = [])
    {
        $this->_method = 'DELETE';

        return $this->finalise();
    }

    /**
     * Redefines the curls url to include the scope.
     * @param  [type] $scope [description]
     * @return [type]        [description]
     */
    public function scope($scope)
    {
        if (!empty($this->data())) {
            throw new ApiCurlException("You cannot call scope() after the curl request has been completed. Functions like get() or put() complete the curl request.");
        }

        $this->_url = static::$base_path . $this->_endpoint . '?scope=' . $scope;
        return $this;
    }

    /**
     * This function should be ran at the end of each VERB
     * This function exectures the curl request and gathers the information
     * about errors from the request.
     *
     * When calling the query, the data is retrieved by ->data() unless there
     * were any errors where then null is returned. You would use ->errors()
     * to retrieve the errors.
     * @return this
     */
    private function finalise()
    {
        $this->_request = $this->_client->request($this->_method, $this->_url, [
            'headers' => $this->_headers,
            'form_params' => $this->_data
        ]);
        $this->_response = (string) $this->_request->getBody();
        $this->_decoded_response = json_decode($this->_response);

        if (!HelperFunctions::is_json($this->_response)) {
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->_errors[] = json_last_error_msg();
            }
        }

        if ($this->_request->getStatusCode() !== 200) {
            if (!empty($this->_decoded_response->message)) {
                $this->_errors[] = $this->_decoded_response->message;
                $this->_errors[] = $this->_decoded_response->error;
            }

            if (!empty((string) $this->_response)) {
                $this->_errors[] = (string) $this->_response;
            }

            $known_error = array_search($this->_request->getStatusCode());
            $this->_errors[] = !empty($known_errors) ? $known_error : $this->_request->getStatusCode();
        }

        return $this;
    }

    /**
     * Return the errors for the query, this can be JSON format
     * errors to authentication errors.
     * @return array
     */
    public function errors()
    {
        return $this->_errors;
    }

    /**
     * Return the data for the query, this function will return
     * null if there were any errors.
     * @return object
     */
    public function data()
    {
        if (empty($this->_errors)) {
            return $this->_decoded_response;
        }

        dd($this->_errors);

        throw new \Twitch\Exceptions\ApiCurlException("Errors found when trying to get data.");
    }
}
