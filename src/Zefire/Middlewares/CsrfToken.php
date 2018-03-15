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
		if (!in_array($this->request->uri(), $this->exclusion)) {
			if ($this->request->server('X-CSRF-TOKEN') != \Session::get('X-CSRF-TOKEN')) {
				throw new \Exception('CSRF Token mismatch');
			}
		}		
	}
}