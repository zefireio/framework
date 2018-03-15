<?php

namespace Zefire\Middlewares;

use Zefire\Contracts\Middleware;
use Zefire\Http\Request;

class ApiAuthentication implements Middleware
{
	/**
     * Stores a request instance.
     *
     * @var \Zefire\Http\Request
     */
	protected $request;
	/**
     * Creates a new api authentication instance.
     *
     * @param  \Zefire\Http\Request $request
     * @return void
     */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	/**
     * Authenticates a user based on api token.
     *
     * @return void
     */
	public function handle()
	{
		$api_token = $this->request->bearerToken();
		if ($api_token != null) {
			if (!\Auth::token($input['api_token'])) {
				\HttpException::abort(401);
			}
		} else {
			\HttpException::abort(401);
		}		
	}
}