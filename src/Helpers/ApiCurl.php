<?php

namespace Twitch\Helpers;

use Twitch\Twitch;
use Twitch\Helpers\HelperFunctions;
use Twitch\Exceptions\TwitchInterfaceException;

class ApiCurl
{
    private $endpoint;
    private $_code;
    private $_url;
    private $output_as_json = false;

    private $_response;
    private $_decoded_response;

    private $curl;

    private $_errors = [];
    private $_data = [];

    function __construct($endpoint)
    {
        if (empty(Twitch::$api_key)) {
            throw new TwitchInterfaceException("ClientID required. Use Twitch::setApiKey() to set the API key.");
        }

        $this->endpoint = $endpoint;
        $this->_url = Twitch::TWITCH_API_BASE_PATH . $endpoint;
        $this->curl = curl_init($this->_url);

        $headers = [
            "Content-Type: application/json",
            "Accept: application/vnd.twitchtv.v3+json",
            "Client-ID: " . Twitch::getApiKey()
        ];

        if (!empty(Twitch::getAccessToken())) {
            $headers[] = "Authorization: OAuth " . Twitch::getAccessToken();
        }

        curl_setopt_array($this->curl, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false // TODO: remove this later, xampp sucks.
        ]);
    }

    /**
     * Standard curl, returning the data found on the other end.
     * @return [type] [description]
     */
    public function get()
    {
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
        if (empty(Twitch::getAccessToken())) {
            throw new ApiCurlException("All put requests must be authenticated with an access token. To set the token you must use Twitch::setAccessToken().");
        }

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");

        // This attaches $data to the post
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, ['data' => http_build_query($data)]);
        return $this->finalise();
    }

    public function delete()
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->finalise();
    }

    public function as_json()
    {
        $this->output_as_json = true;
        return $this;
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

        curl_setopt($this->curl, CURLOPT_URL, Twitch::TWITCH_API_BASE_PATH . $this->endpoint . '?scope=' . $scope);
        $this->url = Twitch::TWITCH_API_BASE_PATH . $this->endpoint . '?scope=' . $scope;
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
        $response = curl_exec($this->curl);

        $this->_response = $response;
        $this->_data = json_decode($this->_response);

        $curl_info = curl_getinfo($this->curl);
        $this->_code = (int) $curl_info['http_code'];

        $bad_codes = [
            'UnprocessableEntity' => 422,
            'NoContent' => 204,
            'TwitchServerError' => 500
        ];

        if ($response === false) {
            $this->_errors['curl'][] = curl_error($this->curl);
        }

        if ($response === '') {
            $this->_errors['curl'][] = "Recieved empty response.";
        }

        if (in_array($curl_info['http_code'], $bad_codes)) {
            $this->_errors['curl'][] = array_search($this->_code, $bad_codes);
        }

        if (!HelperFunctions::is_json($response)) {
            $this->_errors['json'][] = json_last_error_msg();
        } elseif (!empty(json_decode($response)->error)) {
            $this->_errors['twitch'][] = json_decode($response)->error;
        }

        // The errors are present at this point, but we
        // only acknowledge them in a higher class.
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
            return ($this->output_as_json) ? $this->_response : $this->_data;
        }

        return null;
    }
}
