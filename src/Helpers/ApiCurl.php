<?php

namespace Twitch\Helpers;

use Twitch\Twitch;
use Twitch\Helpers\HelperFunctions;
use Twitch\Exceptions\TwitchInterfaceException;

class ApiCurl
{
    private $_endpoint;
    private $_code;
    private $_url;
    private $_as_json = false;

    private $_response;
    private $_decoded_response;

    private $_curl;

    private $_errors = [];
    private $_data = [];

    function __construct($endpoint, $return_data = false)
    {
        if (empty(Twitch::$api_key)) {
            throw new TwitchInterfaceException("ClientID required. Use Twitch::setApiKey() to set the API key.");
        }

        $this->_endpoint = $endpoint;
        $this->_url = Twitch::TWITCH_API_BASE_PATH . $this->_endpoint;
        $this->_curl = curl_init($this->_url);
        $this->_return_data = !empty($return_data);

        $headers = [
            "Content-Type: application/json",
            "Accept: application/vnd.twitchtv.v3+json",
            "Client-ID: " . Twitch::getClientId()
        ];

        if (!empty(Twitch::getAccessToken())) {
            $headers[] = "Authorization: OAuth " . Twitch::getAccessToken();
        }

        curl_setopt_array($this->_curl, [
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
            throw new ApiCurlException("All PUT requests must be authenticated with an access token. To set the token you must use Twitch::setAccessToken().");
        }

        $this->_data = $data;

        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, "PUT");

        // This attaches $data to the post
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        return $this->finalise();
    }
    
    public function post(array $data = [])
    {
        if (empty(Twitch::getAccessToken())) {
            throw new ApiCurlException("All POST requests must be authenticated with an access token. To set a token you must use Twitch::setAccessToken().");
        }

        $this->_data = $data;

        curl_setopt($this->_curl, CURLOPT_POST, true);

        // This attaches $data to the post
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        return $this->finalise();
    }

    public function delete(array $data = [])
    {
        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        return $this->finalise();
    }

    public function as_json()
    {
        $this->_as_json = true;
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

        curl_setopt($this->_curl, CURLOPT_URL, Twitch::TWITCH_API_BASE_PATH . $this->_endpoint . '?scope=' . $scope);
        $this->url = Twitch::TWITCH_API_BASE_PATH . $this->_endpoint . '?scope=' . $scope;
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
        $this->_response = curl_exec($this->_curl);
        
        $this->_decoded_response = json_decode($this->_response);

        $curl_info = curl_getinfo($this->_curl);
        $this->_code = (int) $curl_info['http_code'];

        $bad_codes = [
            'UnprocessableEntity' => 422,
            'Unauthorized' => 401,
            'NoContent' => 204,
            'TwitchServerError' => 500
        ];

        if ($this->_response === false) {
            $this->_errors['curl'][] = curl_error($this->_curl);
        }

        if ($this->_response === '') {
            $this->_errors['curl'][] = "Recieved empty response.";
        }

        if (in_array($curl_info['http_code'], $bad_codes)) {
            if (!empty($this->_decoded_response->message)) {
                $this->_errors['curl'][] = $this->_decoded_response->message;
            } else {
                $this->_errors['curl'][] = array_search($this->_code, $bad_codes);
            }
        }

        if (!HelperFunctions::is_json($this->_response)) {
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->_errors['json'][] = json_last_error_msg();
            }
        } elseif (!empty(json_decode($this->_response)->error)) {
            $this->_errors['twitch'][] = json_decode($this->_response)->error;
        }

        if ($this->_return_data) {
            return $this->_decoded_response;
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
            return ($this->_as_json) ? $this->_response : $this->_decoded_response;
        }
        
        dd($this->_errors);

        throw new \Twitch\Exceptions\ApiCurlException("Errors found when trying to get data.");
    }
}
