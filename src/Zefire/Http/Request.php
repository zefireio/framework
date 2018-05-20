<?php

namespace Zefire\Http;

class Request
{
	/**
     * Creates a new Http request instance.
     *
     * @return void
     */
	public function __construct()
	{
		if (\App::runningMode() == 'cli') {
            if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
                $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
            }
            if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
                $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
            }
        }
	}
	/**
     * Retrieves headers from request.
     *
     * @return array
     */
	public function getHeaders()
	{
		$headers = [];
		foreach ($_SERVER as $key => $value) { 
			if (substr($key, 0, 5) == 'HTTP_') { 
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value; 
			}
		} 
		return $headers;
	}
	/**
     * Retrieves a server variable from request.
     *
     * @param  string $key
     * @return mixed
     */
	public function server($key)
	{
		return (isset($_SERVER[$key])) ? $_SERVER[$key] : null;
	}
	/**
     * Retrieves the Http method from request.
     *
     * @return string
     */
	public function method()
	{
		return $this->server('REQUEST_METHOD');
	}
	/**
     * Retrieves segments from the request's URI.
     *
     * @return array
     */
	public function segments()
    {
        $segments = explode('/', $this->server('REQUEST_URI'));
        return array_values(array_filter($segments, function ($v) {
        	if (strstr($v, '?')) {
        		$explode = explode('?', $v);
        		return $explode[0] !== '';
        	} else {
        		return $v !== '';
        	}
        }));        
    }	
    /**
     * Retrieves all inputs from request.
     *
     * @return array
     */
	public function inputs()
	{
		switch ($this->method()) {
			case 'GET':
			case 'HEAD':
			case 'OPTIONS':
				$input = $_GET;
				break;
			case 'POST':
			case 'PATCH':
			case 'PUT':
			case 'DELETE':
				$input = $_POST;
				break;
		}
		return $input;
	}
	/**
     * Retrieves all inputs from request.
     *
     * @return array
     */
	public function all()
	{
		return $this->inputs();
	}
	/**
     * Retrieves a specific input from request.
     *
     * @param  string $key
     * @return array
     */
	public function input($key)
	{
		$array = $this->inputs();
		return isset($array[$key]) ? $array[$key] : null;
	}
	/**
     * Retrieves all inputs from request except specific keys.
     *
	 * @param  mixed $keys
     * @return array
     */
	public function except($keys)
	{
		if (!is_array($keys)) {
			$explode = explode('|', $keys);
			$except = $explode;
		}
		$array = $this->inputs();
		$data = [];
		foreach ($array as $key => $value) {
			if (!in_array($key, $except)) {
				$data[$key] = $value;
			}
		}
		return $data;
	}
	/**
     * Retrieves uploaded file from request.
     *
     * @return string
     */
	public function file()
	{
		return $_FILE;
	}
	/**
     * Merges an array of value to existing inputs.
     *
     * @param  array $array
     * @return void
     */
	public function merge(array $array = [])
	{		
		switch ($this->method()) {
			case 'GET':
				foreach ($array as $key => $value) {
					$_GET[$key] = $value;
				}
				break;
			case 'POST':
			case 'PUT':
			case 'DELETE':
				foreach ($array as $key => $value) {
					$_POST[$key] = $value;
				}
				break;
		}
	}
	/**
     * Checks if an input exists on the request.
     *
     * @param  string $key
     * @return int
     */
	public function has($key)
	{
		switch ($this->method()) {
			case 'GET':
				return (isset($_GET[$key])) ? true : false;
			case 'POST':
			case 'PUT':
			case 'DELETE':
				return (isset($_POST[$key])) ? true : false;
		}
	}
	/**
     * Retrieves the request's language.
     *
     * @return string
     */
	public function language()
	{
		$explode = explode(';', $this->server('HTTP_ACCEPT_LANGUAGE'));
		return explode(',', $explode[0]);
	}
	/**
     * Retrieves the request's encoding.
     *
     * @return string
     */
	public function encoding()
	{
		$explode = explode(',', $this->server('HTTP_ACCEPT_ENCODING'));
		return $explode[0];
	}
	/**
     * Retrieves the request's accept header.
     *
     * @return string
     */
	public function accepts()
	{
		$explode = explode(',', $this->server('HTTP_ACCEPT'));
		return $explode[0];
	}
	/**
     * Retrieves the request's user agent.
     *
     * @return string
     */
	public function agent()
	{
		return $this->server('HTTP_USER_AGENT');
	}
	/**
     * Retrieves the request's host.
     *
     * @return string
     */
	public function host()
	{
		return $this->server('HTTP_HOST');
	}
	/**
     * Retrieves the request's Http status.
     *
     * @return int
     */
	public function status()
	{
		return $this->server('REDIRECT_STATUS');
	}
	/**
     * Retrieves the request's IP address.
     *
     * @return string
     */
	public function ip()
	{
		return $this->server('REMOTE_ADDR');
	}
	/**
     * Retrieves the request's port.
     *
     * @return string
     */
	public function port()
	{
		return $this->server('REMOTE_PORT');
	}
	/**
     * Retrieves the request's Http scheme.
     *
     * @return string
     */
	public function secure()
	{
		return ($this->server('SERVER_PORT') == 443) ? true : false;
	}
	/**
     * Determines if the request's was sent through AJAX.
     *
     * @return int
     */
	public function ajax()
	{
		return (strtolower($this->server('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') ? true : false;
	}
	/**
     * Retrieves the request's document root.
     *
     * @return string
     */
	public function root()
	{
		return $this->server('DOCUMENT_ROOT');
	}
	/**
     * Retrieves the request's referer.
     *
     * @return string
     */
	public function referer()
	{
		return $this->server('HTTP_REFERER');
	}
	/**
     * Retrieves the request's URI.
     *
     * @return string
     */
	public function uri()
	{
		$explode = explode('?', $this->server('REQUEST_URI'));
		return $explode[0];
	}
	/**
     * Retrieves the request's url.
     *
     * @return string
     */
	public function fullUri()
	{
		return $this->server('HTTP_HOST') . $this->server('REQUEST_URI');
	}
	/**
     * Checks if the request's wants JSON.
     *
     * @return string
     */
	public function wantsJson()
	{
		$explode = explode(',', $this->server('HTTP_ACCEPT'));
		return ($explode[0] == 'application/json') ? true : false;
	}
	/**
     * Retrieves the request's authorization token.
     *
     * @return string
     */
	public function bearerToken()
	{
		return str_replace('Bearer ', '', $this->server('HTTP_AUTHORIZATION'));
	}
	/**
     * Sets headers to request.
     *
     * @return void
     */
	public function header(array $headers = [])
	{
		foreach ($headers as $key => $value) {
			header($key . ': ' . $value);
		}
	}
}