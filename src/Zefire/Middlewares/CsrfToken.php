<?php

namespace Zefire\Middlewares;

use Zefire\Contracts\Middleware;
use Zefire\Http\Request;

class CsrfToken implements Middleware
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
	protected $request;
	/**
     * Stores a list of excluded URIs.
     *
     * @var array
     */
	protected $exclusion = [];
	/**
     * Creates a new csrf token instance.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	/**
     * Validates a CSRF Token.
     *
     * @return void
     */
	public function handle()
	{
		if (!$this->isReading() || $this->isExcluded()) {
			$token = $this->getToken();
			if (!$this->tokenMatch($token)) {
				throw new \Exception('CSRF Token mismatch');
			}
		}		
	}
	/**
     * Checks if the request is a read or write request.
     *
     * @return bool
     */
	protected function isReading()
	{
		return (in_array($this->request->method(), ['HEAD', 'GET', 'OPTIONS'])) ? true : false;
	}
	/**
     * Pulls the token from the request.
     *
     * @return string
     */
	protected function getToken()
	{
		$inputs = $this->request->input();		
		if (isset($inputs['X-CSRF-TOKEN']) && $inputs['X-CSRF-TOKEN'] != '') {
			return $inputs['X-CSRF-TOKEN'];
		}
		if (($this->request->server('X-CSRF-TOKEN') != null)) {
			return $this->request->server('X-CSRF-TOKEN');
		}
		return false;
	}
	/**
     * Matches token from request against session token.
     *
     * @param  string $token
     * @return bool
     */
	protected function tokenMatch($token)
	{
		return ($token == \Session::get('XSRF-TOKEN')) ? true : false;
	}
	/**
     * Checks if request is excluded from CSRF protection.
     *
     * @return bool
     */
	protected function isExcluded()
	{
		return (in_array($this->request->uri(), $this->exclusion)) ? true : false;	
	}
	
}